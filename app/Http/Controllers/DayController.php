<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Schedule;
use App\Models\ScheduleBreak;
use App\Models\Turno; // <-- Asegúrate de tener este modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        return view('days.create', compact('schedule'));
    }

    public function edit($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $existingDays = Day::where('schedule_day', $scheduleId)->pluck('date_day')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        return view('days.edit', compact('schedule', 'existingDays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule' => 'required|exists:schedules,id_hor',
            'dates' => 'required|array|min:1',
            'dates.*' => 'date'
        ]);

        $scheduleId = $request->schedule;
        // 1. Convertir todas las fechas a formato Y-m-d para una comparación limpia
        $dates = collect($request->dates)->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->unique();

        DB::beginTransaction();
        try {
            $schedule = \App\Models\Schedule::findOrFail($scheduleId);
            $scheduleStartTime = Carbon::parse($schedule->start_time)->format('H:i:s');
            $scheduleEndTime = Carbon::parse($schedule->end_time)->format('H:i:s');

            // Obtener cubículos asociados desde la tabla pivote
            $cubicles = DB::table('cubiculos_schedules')
                ->where('schedule_id', $scheduleId)
                ->pluck('cubiculo_id');

            if ($cubicles->isEmpty()) {
                DB::rollBack();
                return back()->withErrors(['error' => 'No hay cubículos asociados al horario.']);
            }

            // =================================================================
            // [FIX 1: LÓGICA DE SOLAPAMIENTO]
            // Comprobamos si ya existen turnos (de OTROS horarios) para
            // los cubículos y fechas seleccionados.
            // =================================================================
            $existingShifts = DB::table('shifts')
                ->whereIn('cubicle_shift', $cubicles)
                ->whereIn('date_shift', $dates)
                ->where('schedule_shift', '!=', $scheduleId) // que NO sean de este mismo horario
                ->where(function ($query) use ($scheduleStartTime, $scheduleEndTime) {
                    // Caso 1: El inicio de un turno existente cae DENTRO del nuevo horario
                    $query->whereBetween('start_shift', [$scheduleStartTime, $scheduleEndTime])
                    // Caso 2: El fin de un turno existente cae DENTRO del nuevo horario
                          ->orWhereBetween('end_shift', [$scheduleStartTime, $scheduleEndTime])
                    // Caso 3: Un turno existente ABARCA todo el nuevo horario
                          ->orWhere(function ($q) use ($scheduleStartTime, $scheduleEndTime) {
                              $q->where('start_shift', '<=', $scheduleStartTime)
                                ->where('end_shift', '>=', $scheduleEndTime);
                          });
                })
                ->get();

            if ($existingShifts->isNotEmpty()) {
                // Encontramos un solapamiento. Construimos un mensaje de error.
                $firstConflict = $existingShifts->first();
                $conflictDate = Carbon::parse($firstConflict->date_shift)->format('d/m/Y');
                $conflictCubicle = DB::table('cubiculos')->where('id', $firstConflict->cubicle_shift)->value('nombre');
                
                DB::rollBack();
                return back()->withErrors(['error' => "Solapamiento detectado: El cubículo '{$conflictCubicle}' ya tiene turnos asignados el día {$conflictDate} en ese rango de horas (de otro horario)."]);
            }

            // Obtener los bloques de descanso
            $breaks = DB::table('schedule_breaks')
                ->where('schedule_id', $scheduleId)
                ->get(['start_break', 'end_break']);

            // Borrar días anteriores de ESTE horario
            \App\Models\Day::where('schedule_day', $scheduleId)->delete();

            // Insertar días seleccionados
            $insertDays = [];
            foreach ($dates as $date) {
                $insertDays[] = [
                    'schedule_day' => $scheduleId,
                    'date_day' => $date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            \App\Models\Day::insert($insertDays);

            DB::table('shifts')
                ->where('schedule_shift', $scheduleId)
                ->whereNull('person_shift') // <-- SÓLO BORRA TURNOS DISPONIBLES
                ->delete();

            // Generar turnos
            $insertShifts = [];
            $now = now();

            foreach ($dates as $date) {
                $start = \Carbon\Carbon::parse($date . ' ' . $schedule->start_time);
                $end = \Carbon\Carbon::parse($date . ' ' . $schedule->end_time);

                while ($start->lt($end)) {
                    $shiftStart = $start->copy();
                    $shiftEnd = $start->copy()->addMinutes($schedule->attention_minutes);

                    // Verificar si el turno se cruza con un break
                    $isInBreak = $breaks->contains(function ($b) use ($shiftStart, $shiftEnd) {
                        $breakStart = \Carbon\Carbon::parse($shiftStart->toDateString() . ' ' . $b->start_break);
                        $breakEnd = \Carbon\Carbon::parse($shiftStart->toDateString() . ' ' . $b->end_break);
                        return $shiftStart->lt($breakEnd) && $shiftEnd->gt($breakStart);
                    });

                    if (!$isInBreak && $shiftEnd->lte($end)) {
                        foreach ($cubicles as $cubicleId) {
                            $insertShifts[] = [
                                'id_shift' => (string) \Illuminate\Support\Str::uuid(),
                                'schedule_shift' => $scheduleId,
                                'cubicle_shift' => $cubicleId,
                                'date_shift' => $date,
                                'start_shift' => $shiftStart->format('H:i:s'),
                                'end_shift' => $shiftEnd->format('H:i:s'),
                                'person_shift' => null, // nadie lo ha tomado aún
                                'status_shift' => 1, // 1 = Disponible/Activo
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }

                    // Avanzar tomando en cuenta el descanso entre turnos
                    $start->addMinutes($schedule->attention_minutes + $schedule->break_minutes);
                }
            }

            if (!empty($insertShifts)) {
                // Usamos chunk para insertar en lotes de 500
                foreach (array_chunk($insertShifts, 500) as $chunk) {
                    DB::table('shifts')->insert($chunk);
                }
            }

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Días y turnos configurados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Devolvemos el error real para depuración
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Determina si el turno actual cae dentro de un break.
     */
    private function isBreakTime(Carbon $start, Carbon $end, $breaks): bool
    {
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_break);
            $breakEnd = Carbon::parse($break->end_break);

            if (
                ($start->between($breakStart, $breakEnd, true)) ||
                ($end->between($breakStart, $breakEnd, true)) ||
                ($start->lt($breakStart) && $end->gt($breakEnd))
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Devuelve la hora siguiente disponible después de un break.
     */
    private function nextAvailableTime(Carbon $current, $breaks): ?Carbon
    {
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_break);
            $breakEnd = Carbon::parse($break->end_break);

            if ($current->between($breakStart, $breakEnd, true)) {
                return $breakEnd->copy();
            }
        }
        return null;
    }
}
