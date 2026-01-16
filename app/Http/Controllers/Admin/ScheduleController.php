<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleBreak;
use App\Models\CubicleSchedule;
use Illuminate\Http\Request;
use App\Models\Turno;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Cubiculo;

class ScheduleController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:horarios.ver')->only('index');
        $this->middleware('can:horarios.crear')->only(['create', 'store']);
        $this->middleware('can:horarios.editar')->only(['edit', 'update']);
        $this->middleware('can:horarios.eliminar')->only('destroy');
    }

    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Schedule::query();

        // --- FILTRO DE SEGURIDAD ---
        // Solo aplicamos restricción si NO es Super Admin
        if (!$user->hasRole('Super Admin')) {
            $misAreasIds = $user->operatingAreas->pluck('id');
            $query->whereHas('cubicles', function($q) use ($misAreasIds) {
                $q->whereIn('operating_area_id', $misAreasIds);
            });
        }

        // --- ORDENAMIENTO ---
        $sortBy = $request->get('sortBy', 'valid_from');
        $sortDesc = $request->get('sortDesc', 'false') === 'true';

        if ($sortBy && Schema::hasColumn('schedules', $sortBy)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        } else {
            $query->latest('valid_from');
        }

        // --- CORRECCIÓN PRINCIPAL ---
        // Separamos withCount de with para evitar conflictos
        // y nos aseguramos de cargar TODAS las relaciones necesarias
        $schedules = $query
            ->with([
                'cubicles' => function($q) {
                    // Sin límite, carga TODOS los cubículos
                    $q->select('cubiculos.id', 'cubiculos.nombre', 'cubiculos.operating_area_id');
                },
                'cubicles.users',
                'days' => function($q) {
                    // Sin límite, carga TODOS los días
                    $q->orderBy('date_day', 'asc');
                },
                'breaks' => function($q) {
                    // Sin límite, carga TODOS los breaks
                    $q->orderBy('start_break', 'asc');
                }
            ])
            ->get();

        // Calculamos los contadores DESPUÉS de cargar los datos
        // para tener control total sobre los resultados
        $schedules->each(function($schedule) {
            $schedule->shifts_count = $schedule->shifts()->count();
            $schedule->occupied_shifts_count = $schedule->occupiedShifts()->count();
            $schedule->days_count = $schedule->days->count();
            $schedule->cubicles_count = $schedule->cubicles->count();
            $schedule->breaks_count = $schedule->breaks->count();
        });

        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        // Aquí podrías pasar cubículos disponibles para seleccionar
        $cubicles = Cubiculo::all();
        return view('admin.schedules.create', compact('cubicles'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'valid_from' => 'required|date',
            'break_minutes' => 'required|integer|min:0',
            'attention_minutes' => 'required|integer|min:1',
            'cubicles' => 'required|array|min:1', // Cubículos asociados
            'cubicles.*' => 'integer|exists:cubiculos,id', // Cada cubículo debe existir
            'breaks' => 'nullable|array',
            'breaks.*.start' => 'required_with:breaks|date_format:H:i',
            'breaks.*.end' => 'required_with:breaks|date_format:H:i|after:breaks.*.start',
        ]);

        DB::beginTransaction();
        try {
            $now = now();

            // Crear horario
            $schedule = Schedule::create([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'valid_from' => $request->valid_from ?? $now,
                'break_minutes' => $request->break_minutes,
                'attention_minutes' => $request->attention_minutes,
            ]);

            // Asociar cubículos
            $cubiculosData = collect($request->cubicles)->map(fn($cub) => [
                'cubiculo_id' => $cub,
                'schedule_id' => $schedule->id_hor,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            if (!empty($cubiculosData)) {
                CubicleSchedule::insert($cubiculosData);
            }

            // Agregar breaks si existen
            if ($request->filled('breaks')) {
                $breakData = collect($request->breaks)->map(fn($b) => [
                    'schedule_id' => $schedule->id_hor,
                    'start_break' => $b['start'],
                    'end_break'   => $b['end'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])->toArray();

                if (!empty($breakData)) {
                    ScheduleBreak::insert($breakData);
                }
            }

            DB::commit();

            return redirect()
                ->route('days.create', ['schedule' => $schedule->id_hor])
                ->with('success', 'Horario creado correctamente. Ahora configure los días.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating schedule: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit($id)
    {
        $schedule = Schedule::with(['cubicles', 'breaks'])->findOrFail($id);
        $cubicles = Cubiculo::all();
        return view('admin.schedules.edit', compact('schedule', 'cubicles'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_minutes' => 'required|integer|min:0',
            'attention_minutes' => 'required|integer|min:1',
            'cubicles' => 'required|array|min:1',
            'breaks' => 'nullable|array',
            'breaks.*.start' => 'required_with:breaks|date_format:H:i',
            'breaks.*.end' => 'required_with:breaks|date_format:H:i|after:breaks.*.start',
        ]);

        DB::beginTransaction();
        try {
            $now = now();

            $schedule->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'break_minutes' => $request->break_minutes,
                'attention_minutes' => $request->attention_minutes,
                'valid_from' => $request->valid_from ?? $schedule->valid_from,
            ]);

            // Actualizar cubiculos
            CubicleSchedule::where('schedule_id', $schedule->id_hor)->delete();
            $cubiculosData = collect($request->cubicles)->map(fn($cub) => [
                'cubiculo_id' => $cub,
                'schedule_id' => $schedule->id_hor,
                'created_at' => $now,
                'updated_at' => $now
            ])->toArray();
            CubicleSchedule::insert($cubiculosData);

            // Actualizar breaks
            ScheduleBreak::where('schedule_id', $schedule->id_hor)->delete();
            if ($request->filled('breaks')) {
                $breakData = collect($request->breaks)->map(fn($b) => [
                    'schedule_id' => $schedule->id_hor,
                    'start_break' => $b['start'],
                    'end_break' => $b['end'],
                    'created_at' => $now,
                    'updated_at' => $now
                ])->toArray();
                ScheduleBreak::insert($breakData);
            }

            DB::commit();
            return redirect()->route('days.edit', ['schedule' => $schedule->id_hor])->with('success', 'Horario actualizado. Por favor, reconfigure los días.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error updating schedule: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy($id)
    {
        $schedule = Schedule::with('days')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Eliminar manualmente los días relacionados, ya que no hay eliminación en cascada.
            $schedule->days()->delete();

            // Los cubiculos_schedules, schedule_breaks y shifts relacionados
            // se eliminarán automáticamente debido a la restricción onDelete('cascade')
            // en sus respectivas migraciones.

            // Ahora, elimina el horario en sí.
            $schedule->delete();

            DB::commit();
            return redirect()->route('schedules.index')->with('success', 'Horario eliminado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el horario: ' . $e->getMessage()]);
        }
    }

    /**
     * Genera físicamente los registros en la tabla 'shifts'
     */
    public function generateShifts($id)
    {
        // Cargamos el horario con sus relaciones
        $schedule = Schedule::with(['days', 'cubicles', 'breaks'])->findOrFail($id);

        if ($schedule->days->isEmpty() || $schedule->cubicles->isEmpty()) {
            return redirect()->back()->with('error', 'No se pueden generar turnos sin días o cubículos asignados.');
        }

        $ahora = now();
        $turnosParaInsertar = [];

        // Parámetros de tiempo corregidos según tus migraciones
        $duracionTurno = (int) $schedule->attention_minutes; //
        $intervaloEntreTurnos = (int) $schedule->break_minutes; //
        $saltoTotal = $duracionTurno + $intervaloEntreTurnos;

        foreach ($schedule->days as $day) {
            foreach ($schedule->cubicles as $cubicle) {

                // Iniciamos el reloj para este día y cubículo
                $fechaLimpia = Carbon::parse($day->date_day)->format('Y-m-d');
                $horaInicioSlot = Carbon::parse($fechaLimpia . ' ' . $schedule->start_time);
                $horaFinJornada = Carbon::parse($fechaLimpia . ' ' . $schedule->end_time);

                while ($horaInicioSlot->copy()->addMinutes($duracionTurno) <= $horaFinJornada) {

                    $horaFinSlot = $horaInicioSlot->copy()->addMinutes($duracionTurno);

                    // Verificación de Descansos (Breaks) corregida
                    $enDescanso = false;
                    foreach ($schedule->breaks as $break) {
                        $inicioBreak = Carbon::parse($fechaLimpia . ' ' . $break->start_break);
                        $finBreak = Carbon::parse($fechaLimpia . ' ' . $break->end_break);

                        if ($horaInicioSlot->lt($finBreak) && $horaFinSlot->gt($inicioBreak)) {
                            $enDescanso = true;
                            $horaInicioSlot = $finBreak->copy(); // Saltamos al final del break
                            break;
                        }
                    }

                    if ($enDescanso) continue;

                    // Preparación del registro según la migración de shifts
                    $turnosParaInsertar[] = [
                        'id_shift'       => (string) \Illuminate\Support\Str::uuid(),
                        'schedule_shift' => $schedule->id_hor,
                        'cubicle_shift'  => $cubicle->id,
                        'date_shift'     => $day->date_day,
                        'start_shift'    => $horaInicioSlot->format('H:i:s'),
                        'end_shift'      => $horaFinSlot->format('H:i:s'),
                        'status_shift'   => 1, // 1 = Disponible
                        'created_at'     => $ahora,
                        'updated_at'     => $ahora,
                    ];

                    // Avanzamos sumando duración + intervalo
                    $horaInicioSlot->addMinutes($saltoTotal);
                }
            }
        }

        if (!empty($turnosParaInsertar)) {
            \App\Models\Shift::insert($turnosParaInsertar);
        }

        return redirect()->route('schedules.index')->with('success', '¡Turnos generados exitosamente!');
    }
}
