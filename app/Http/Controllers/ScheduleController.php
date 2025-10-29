<?php

namespace App\Http\Controllers;

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
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sortBy', 'valid_from');
        $sortDesc = $request->get('sortDesc', 'false') === 'true';

        $query = Schedule::query();

        // Filtros opcionales
        if ($request->filled('start_time')) {
            $query->where('start_time', $request->input('start_time'));
        }
        if ($request->filled('end_time')) {
            $query->where('end_time', $request->input('end_time'));
        }

        // Orden dinámico
        if ($sortBy && Schema::hasColumn('schedules', $sortBy)) {
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        } else {
            $query->latest('valid_from');
        }

        // Relaciones y conteo
        $schedules = $query->withCount(['occupiedShifts', 'shifts', 'days', 'cubicles', 'breaks'])
            ->with(['cubicles', 'days', 'breaks'])
            ->orderBy('valid_from', 'desc')
            ->get();

        return view('schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        // Aquí podrías pasar cubiculoss disponibles para seleccionar
         $cubicles = Cubiculo::all();
        return view('schedules.create', compact('cubicles'));
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
        return view('schedules.edit', compact('schedule', 'cubicles'));
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
     * ===================================================================
     * FUNCIÓN DE GENERACIÓN DE TURNOS
     * ===================================================================
     * Esta función toma un Horario y crea todas sus filas de Turno.
     * La llamaremos desde el controlador de Días.
     */

    /*
    public function generateShifts(Schedule $schedule)
    {
        // 1. Cargar las relaciones que necesitamos (días, cubículos, descansos)
        // Usamos fresh() para asegurarnos de tener los datos más actuales.
        $schedule->load('days', 'cubicles', 'breaks');

        // Si no hay días o cubículos, no podemos generar nada.
        if ($schedule->days->isEmpty() || $schedule->cubicles->isEmpty()) {
            // Puedes loggear un error aquí si lo deseas
            return; 
        }

        $turnosParaInsertar = [];
        $ahora = now(); // Para los timestamps created_at/updated_at

        // 2. Parsear los datos del Horario
        $inicioHorario = Carbon::parse($schedule->start_time);
        $finHorario = Carbon::parse($schedule->end_time);
        $duracionTurno = (int)$schedule->attention_minutes;
        $duracionDescansoEntreTurnos = (int)$schedule->break_minutes; // El descanso *entre* turnos
        $duracionTotalSlot = $duracionTurno + $duracionDescansoEntreTurnos;

        // 3. Loop 1: Iterar por cada DÍA asignado al horario
        // Asumimos que tu modelo 'Day' tiene una columna 'date_day'
        foreach ($schedule->days as $day) {
            
            // 4. Loop 2: Iterar por cada CUBÍCULO asignado al horario
            foreach ($schedule->cubicles as $cubicle) {
                
                // 5. Loop 3: Iterar por el TIEMPO (desde inicio hasta fin)
                $horaInicioSlot = $inicioHorario->copy();

                while ($horaInicioSlot->copy()->addMinutes($duracionTurno) <= $finHorario) {
                    
                    $horaFinSlot = $horaInicioSlot->copy()->addMinutes($duracionTurno);

                    // 6. Comprobar si este slot cae en un DESCANSO (Break)
                    $enDescanso = false;
                    foreach ($schedule->breaks as $break) {
                        $inicioDescanso = Carbon::parse($break->start_break);
                        $finDescanso = Carbon::parse($break->end_break);

                        // Comprobar si el slot se solapa con el descanso
                        if ($horaInicioSlot < $finDescanso && $horaFinSlot > $inicioDescanso) {
                            $enDescanso = true;
                            // Movemos el inicio del próximo slot al final del descanso
                            $horaInicioSlot = $finDescanso->copy();
                            break; // Salir del loop de descansos
                        }
                    }

                    if ($enDescanso) {
                        continue; // Saltar al siguiente ciclo del 'while' con la nueva $horaInicioSlot
                    }

                    // 7. Si NO está en descanso, preparamos el Turno para la inserción
                    // Asegúrate de que los nombres de columna coincidan con tu BD (horario_tur, etc.)
                    $turnosParaInsertar[] = [
                        'id' => (string) \Illuminate\Support\Str::uuid(), // O como generes tus IDs
                        'horario_tur' => $schedule->id_hor,
                        'cubiculo_tur' => $cubicle->id,
                        'fecha_tur' => $day->date_day, // ¡Importante! Asegúrate que tu modelo Day tenga 'date_day'
                        'inicio_tur' => $horaInicioSlot->format('H:i:s'),
                        'fin_tur' => $horaFinSlot->format('H:i:s'),
                        'estado_tur' => 0, // 0 = Disponible
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ];

                    // 8. Avanzar al siguiente slot (sumando turno + descanso entre turnos)
                    $horaInicioSlot->addMinutes($duracionTotalSlot);
                }
            }
        }

        
    }*/

}
