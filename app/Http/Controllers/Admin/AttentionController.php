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

            // ✅ FORMATO CONSISTENTE PARA LAS HORAS
            // Asegurar que las horas tengan formato HH:MM
            $startTime = $this->formatearHora($shift->start_shift);
            $endTime = $this->formatearHora($shift->end_shift);

            $titulo = $estaOcupado
                ? "🚫 " . $shift->cubicle_name . " - " . ($shift->student_name ?? 'Ocupado')
                : "✅ " . $shift->cubicle_name . " - Libre";

            return [
                'id'    => $shift->id_shift,
                'title' => $titulo,
                'start' => $shift->date_shift . 'T' . $startTime,
                'end'   => $shift->date_shift . 'T' . $endTime,
                'backgroundColor' => $estaOcupado ? '#dc3545' : '#28a745',
                'borderColor'     => $estaOcupado ? '#bd2130' : '#1e7e34',
                'extendedProps' => [
                    'hora_inicio' => $startTime,
                    'hora_fin' => $endTime,
                    'estado' => $estaOcupado ? 'ocupado' : 'libre'
                ]
            ];
        })->toArray();

        $cubiculos = Cubiculo::with(['users', 'shifts' => function ($query) {
            $query->where('date_shift', date('Y-m-d'));
        }])
            ->whereIn('operating_area_id', $misAreasIds)
            ->get();

        return view('admin.attention.index', compact('calendarEvents', 'cubiculos'));
    }
    // ✅ FUNCIÓN PARA FORMATEAR HORAS CONSISTENTEMENTE
    private function formatearHora($hora)
    {
        // Si la hora es null o vacía, retornar hora por defecto
        if (empty($hora)) {
            return '00:00';
        }

        // Eliminar espacios en blanco
        $hora = trim($hora);

        // Si ya tiene formato HH:MM, retornar tal cual pero con padding
        if (strpos($hora, ':') !== false) {
            $partes = explode(':', $hora);
            // Asegurar dos dígitos para horas y minutos
            $horaPart = str_pad($partes[0], 2, '0', STR_PAD_LEFT);
            $minutoPart = isset($partes[1]) ? str_pad($partes[1], 2, '0', STR_PAD_LEFT) : '00';
            return $horaPart . ':' . $minutoPart;
        }

        // Si es solo un número, tratar como hora
        if (is_numeric($hora)) {
            // Si es mayor que 24, podría ser un timestamp o algo raro
            if ($hora > 24) {
                // Convertir a formato de hora (ej: 1300 -> 13:00)
                $horaStr = str_pad($hora, 4, '0', STR_PAD_LEFT);
                return substr($horaStr, 0, 2) . ':' . substr($horaStr, 2, 2);
            }
            // Caso normal: solo la hora
            return str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Si no coincide con ningún formato conocido, devolver 00:00
        return '00:00';
    }
}
