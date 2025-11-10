<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Shift; //  Importamos el modelo Shift
    use App\Models\Cubiculo; //  Importamos el modelo Cubiculo
    use Carbon\Carbon; //  Importamos Carbon para manejo de fechas



    class AttentionController extends Controller
    {
        public function index()
        {
            // 2. Consultamos los turnos agendados
            // Buscamos solo los turnos que SÍ tienen una persona asignada (person_shift NO es nulo)
            $bookedShifts = Shift::join("cubiculos", "cubiculos.id", "=", "shifts.cubicle_shift")
                ->join("student_registrations", "student_registrations.cedula", "=", "shifts.person_shift")
                ->select(
                    "shifts.date_shift",
                    "shifts.start_shift",
                    "shifts.end_shift",
                    "cubiculos.nombre as cubicle_name",
                    "student_registrations.names as student_name"
                )
                ->whereNotNull('shifts.person_shift') // ¡Clave! Solo turnos reservados
                ->get();

            // 3. Formateamos los datos para FullCalendar
            $calendarEvents = $bookedShifts->map(function ($shift) {
                // Creamos el título del evento, ej: "Cubículo A: Juan Pérez"
                // Añadimos la hora de inicio al título para que sea visible en la vista de mes.
                $time = Carbon::parse($shift->start_shift)->format('H:i'); // Formato "09:30"
                $title = $shift->cubicle_name . ': ' . $shift->student_name;

                // Formato ISO 8601 (YYYY-MM-DDTHH:MM:SS) que FullCalendar entiende
                $start = $shift->date_shift . 'T' . $shift->start_shift;
                $end = $shift->date_shift . 'T' . $shift->end_shift;

                return [
                    'title' => $title,
                    'start' => $start,
                    'end'   => $end,
                ];
            })->toArray();
            $cubiculos = Cubiculo::with('users')->get();


            // 4. Pasamos la variable $calendarEvents a la vista
            return view('attention.index', compact('calendarEvents', 'cubiculos'));
        }
    }