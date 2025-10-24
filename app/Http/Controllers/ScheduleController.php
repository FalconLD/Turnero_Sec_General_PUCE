<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleBreak;
use App\Models\CubicleSchedule;
use Illuminate\Http\Request;
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
            ->paginate($perPage);

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
            return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully.');
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
}
