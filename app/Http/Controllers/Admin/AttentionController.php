<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift; //  Importamos el modelo Shift
use App\Models\Cubiculo; //  Importamos el modelo Cubiculo
use Carbon\Carbon; //  Importamos Carbon para manejo de fechas



class AttentionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $misAreasIds = $user->operatingAreas->pluck('id');

        // Traemos TODOS los turnos de las áreas del operador
        $shifts = Shift::join("cubiculos", "cubiculos.id", "=", "shifts.cubicle_shift")
            ->leftJoin("student_registrations", "student_registrations.cedula", "=", "shifts.person_shift")
            ->select(
                "shifts.id_shift",
                "shifts.date_shift",
                "shifts.start_shift",
                "shifts.end_shift",
                "shifts.status_shift", // Importante para el color
                "cubiculos.nombre as cubicle_name",
                "student_registrations.names as student_name"
            )
            ->whereIn('cubiculos.operating_area_id', $misAreasIds)
            ->get();

        $calendarEvents = $shifts->map(function ($shift) {
            $estaOcupado = ($shift->status_shift == 0);

            return [
                'id'    => $shift->id_shift,
                'title' => $estaOcupado
                            ? "🚫 " . $shift->cubicle_name . ": " . ($shift->student_name ?? 'Ocupado')
                            : "✅ " . $shift->cubicle_name . ": Libre",
                'start' => $shift->date_shift . 'T' . $shift->start_shift,
                'end'   => $shift->date_shift . 'T' . $shift->end_shift,
                'backgroundColor' => $estaOcupado ? '#dc3545' : '#28a745', // Rojo ocupado, Verde libre
                'borderColor'     => $estaOcupado ? '#bd2130' : '#1e7e34',
            ];
        })->toArray();

        $cubiculos = Cubiculo::with(['users', 'shifts' => function($query) {
                $query->where('date_shift', date('Y-m-d'));
            }])
            ->whereIn('operating_area_id', $misAreasIds)
            ->get();

        return view('admin.attention.index', compact('calendarEvents', 'cubiculos'));
    }
}
