<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift; //  Importamos el modelo Shift
use App\Models\Cubiculo; //  Importamos el modelo Cubiculo
use Carbon\Carbon; //  Importamos Carbon para manejo de fechas



class AttentionController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // 1. Obtenemos al usuario logueado

        // 2. Obtenemos los IDs de las áreas operativas que tiene asignadas
        $misAreasIds = $user->operatingAreas->pluck('id');

        // 3. Consultamos los turnos filtrando por las áreas del usuario
        $bookedShifts = Shift::join("cubiculos", "cubiculos.id", "=", "shifts.cubicle_shift")
            ->join("student_registrations", "student_registrations.cedula", "=", "shifts.person_shift")
            ->select(
                "shifts.date_shift",
                "shifts.start_shift",
                "shifts.end_shift",
                "cubiculos.nombre as cubicle_name",
                "student_registrations.names as student_name"
            )
            ->whereNotNull('shifts.person_shift')
            // FILTRO CLAVE: Solo turnos de cubículos que pertenecen a mis áreas
            ->whereIn('cubiculos.operating_area_id', $misAreasIds)
            ->get();

        // 4. Formateamos los datos para FullCalendar (Tu lógica actual se mantiene)
        $calendarEvents = $bookedShifts->map(function ($shift) {
            $start = $shift->date_shift . 'T' . $shift->start_shift;
            $end = $shift->date_shift . 'T' . $shift->end_shift;

            return [
                'title' => $shift->cubicle_name . ': ' . $shift->student_name,
                'start' => $start,
                'end'   => $end,
                'backgroundColor' => '#3788d8', // Opcional: Color para eventos
            ];
        })->toArray();

        // 5. Filtramos la lista de cubículos laterales (Equipo)
        // Solo los cubículos que pertenecen a las áreas del usuario
        $cubiculos = Cubiculo::with('users')
            ->whereIn('operating_area_id', $misAreasIds)
            ->get();

        return view('attention.index', compact('calendarEvents', 'cubiculos'));
    }
}
