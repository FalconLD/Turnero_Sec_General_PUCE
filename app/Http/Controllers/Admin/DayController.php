<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return view('admin.days.create', compact('schedule'));
    }

    public function edit($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $existingDays = Day::where('schedule_day', $scheduleId)->pluck('date_day')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        return view('admin.days.edit', compact('schedule', 'existingDays'));
    }

    /**
     * Store the assigned days and trigger automatic shift generation.
     */
    public function store(Request $request)
    {
        // 1. Validación de entrada según los campos del formulario
        $request->validate([
            'schedule' => 'required|exists:schedules,id_hor',
            'dates'    => 'required|array|min:1',
            'dates.*'  => 'date'
        ]);

        $scheduleId = $request->schedule;
        // Normalizamos las fechas para evitar problemas de formato en la BD
        $dates = collect($request->dates)->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->unique();

        DB::beginTransaction();
        try {
            $schedule = Schedule::findOrFail($scheduleId);
            $scheduleStartTime = Carbon::parse($schedule->start_time)->format('H:i:s');
            $scheduleEndTime = Carbon::parse($schedule->end_time)->format('H:i:s');

            // 2. Obtener cubículos asociados desde la tabla pivote
            $cubicles = DB::table('cubiculos_schedules')
                ->where('schedule_id', $scheduleId)
                ->pluck('cubiculo_id');

            if ($cubicles->isEmpty()) {
                DB::rollBack();
                return back()->withErrors(['error' => 'No hay cubículos asociados a este horario.']);
            }

            // =================================================================
            // [LOGICA DE SOLAPAMIENTO: Mantenida del original]
            // Comprobamos si ya existen turnos de OTROS horarios para estos cubículos
            // =================================================================
            $existingShifts = DB::table('shifts')
                ->whereIn('cubicle_shift', $cubicles)
                ->whereIn('date_shift', $dates)
                ->where('schedule_shift', '!=', $scheduleId) // Ignorar el horario actual
                ->where(function ($query) use ($scheduleStartTime, $scheduleEndTime) {
                    $query->whereBetween('start_shift', [$scheduleStartTime, $scheduleEndTime])
                          ->orWhereBetween('end_shift', [$scheduleStartTime, $scheduleEndTime])
                          ->orWhere(function ($q) use ($scheduleStartTime, $scheduleEndTime) {
                              $q->where('start_shift', '<=', $scheduleStartTime)
                                ->where('end_shift', '>=', $scheduleEndTime);
                          });
                })
                ->get();

            if ($existingShifts->isNotEmpty()) {
                $firstConflict = $existingShifts->first();
                $conflictDate = Carbon::parse($firstConflict->date_shift)->format('d/m/Y');
                $conflictCubicle = DB::table('cubiculos')->where('id', $firstConflict->cubicle_shift)->value('nombre');

                DB::rollBack();
                return back()->withErrors(['error' => "Solapamiento: El cubículo '{$conflictCubicle}' ya tiene turnos asignados el {$conflictDate} en otro horario."]);
            }

            // 3. ACTUALIZAR DÍAS: Borrar anteriores e insertar los nuevos
            Day::where('schedule_day', $scheduleId)->delete();

            $insertDays = $dates->map(fn($date) => [
                'schedule_day' => $scheduleId,
                'date_day'     => $date,
                'created_at'   => now(),
                'updated_at'   => now(),
            ])->toArray();

            Day::insert($insertDays);

            // 4. LIMPIEZA DE TURNOS: Solo borramos los que NO han sido tomados por estudiantes
            DB::table('shifts')
                ->where('schedule_shift', $scheduleId)
                ->whereNull('person_shift')
                ->delete();

            DB::commit();

            // =================================================================
            // [GENERACIÓN AUTOMÁTICA: Opción 1]
            // Delegamos la creación de bloques de tiempo al ScheduleController
            // =================================================================
            $scheduleController = new ScheduleController();
            $scheduleController->generateShifts($scheduleId);

            return redirect()->route('schedules.index')
                ->with('success', 'Días guardados y turnos generados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar: ' . $e->getMessage()])->withInput();
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
