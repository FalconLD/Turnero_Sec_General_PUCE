<?php

namespace App\Http\Controllers;

use App\Models\Cubiculo;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Muestra la lista de horarios.
     */
    public function index()
    {
        $schedules = Schedule::with('cubiculo', 'days')->latest()->get();
        return view('schedules.index', compact('schedules'));
    }

    /**
     * Muestra el formulario para crear un nuevo horario.
     */
    public function create()
    {
        $cubiculos = Cubiculo::all();
        return view('schedules.create', compact('cubiculos'));
    }

    /**
     * Guarda un nuevo horario (Paso 1) y redirige al Paso 2.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descanso' => 'required|integer|min:0',
            'atencion' => 'required|integer|min:1',
            'vigencia_desde' => 'required|date',
            'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
            'cubiculo_id' => 'required|exists:cubiculos,id',
            'pausas' => 'present|array',
            'pausas.*.hora_inicio' => 'required_with:pausas.*.hora_fin|nullable|date_format:H:i',
            'pausas.*.hora_fin' => 'required_with:pausas.*.hora_inicio|nullable|date_format:H:i|after:pausas.*.hora_inicio',
        ]);

        try {
            DB::beginTransaction();

            $scheduleData = collect($validated)->except('pausas')->toArray();
            $schedule = Schedule::create($scheduleData);

            if (!empty($validated['pausas'])) {
                 $pausasParaGuardar = array_filter($validated['pausas'], fn($p) => !empty($p['hora_inicio']) && !empty($p['hora_fin']));
                 if (!empty($pausasParaGuardar)) {
                    $schedule->pauses()->createMany($pausasParaGuardar);
                 }
            }

            DB::commit();

            return redirect()->route('schedules.selectDays', $schedule)
                            ->with('success', 'Horario base creado. Ahora selecciona los días.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar el horario: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un horario (Paso 1).
     */
    public function edit(Schedule $schedule)
    {
        // REFINADO: Se activa eager loading para optimizar consultas.
        $schedule->load('pauses');
        $cubiculos = Cubiculo::all();
        return view('schedules.edit', compact('schedule', 'cubiculos'));
    }

    /**
     * Actualiza un horario (Paso 1) y redirige al Paso 2.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descanso' => 'required|integer|min:0',
            'atencion' => 'required|integer|min:1',
            'vigencia_desde' => 'required|date',
            'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
            'cubiculo_id' => 'required|exists:cubiculos,id',
            'pausas' => 'present|array',
            // REFINADO: Reglas de validación más flexibles para pausas.
            'pausas.*.hora_inicio' => 'required_with:pausas.*.hora_fin|nullable|date_format:H:i',
            'pausas.*.hora_fin' => 'required_with:pausas.*.hora_inicio|nullable|date_format:H:i|after:pausas.*.hora_inicio',
        ]);

        try {
            DB::beginTransaction();

            $scheduleData = collect($validated)->except('pausas')->toArray();
            $schedule->update($scheduleData);

            $schedule->pauses()->delete();

            if (!empty($validated['pausas'])) {
                $pausasParaGuardar = array_filter($validated['pausas'], fn($p) => !empty($p['hora_inicio']) && !empty($p['hora_fin']));
                if (!empty($pausasParaGuardar)) {
                    $schedule->pauses()->createMany($pausasParaGuardar);
                }
            }

            DB::commit();

            return redirect()->route('schedules.selectDays', $schedule)
                             ->with('success', 'Horario actualizado. Ahora ajusta los días.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el horario: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra la vista para seleccionar los días (Paso 2).
     */
    public function selectDays(Schedule $schedule)
    {
        $savedDays = $schedule->days()->pluck('weekday')->toArray();
        return view('schedules.select-days', compact('schedule', 'savedDays'));
    }

    /**
     * Guarda o actualiza los días seleccionados (Paso 2).
     */
    public function storeDays(Request $request, Schedule $schedule)
    {
        $request->validate([
            'weekdays' => 'sometimes|array',
            'weekdays.*' => 'integer|between:1,7',
        ]);

        $schedule->days()->delete();

        if ($request->has('weekdays')) {
            foreach ($request->weekdays as $weekday) {
                $schedule->days()->create(['weekday' => $weekday]);
            }
        }

        return redirect()->route('schedules.index')->with('success', 'Horario guardado y días asignados correctamente.');
    }

    /**
     * Elimina un horario.
     */
    public function destroy(Schedule $schedule)
    {
        // COMPLETADO: Lógica para eliminar.
        try {
            $schedule->delete();
            return redirect()->route('schedules.index')->with('success', 'Horario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar el horario: ' . $e->getMessage());
        }
    }
}
