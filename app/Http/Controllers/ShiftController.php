<?php

namespace App\Http\Controllers;

use App\Events\UpdateView;
use App\Helpers\Responses;
use App\Http\Requests\NewShift;
use App\Models\Schedule;
use App\Models\Person;
use App\Models\SurveySetting;
use App\Models\Shift;
use App\Models\StudentRegistration;
use App\Notifications\FeedbackRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function store(NewShift $request)
    {
        $idNumber = $request['identification'];
        $email = $request['email'];
        $schedule = \App\Models\Schedule::orderBy('valid_from', 'desc')->first();
        if ($schedule) {
        $today = Carbon::today();
        $validFrom = Carbon::parse($schedule->valid_from);

        // 🔹 Verificar si hoy es antes de la fecha de inicio válida
        if ($today->lt($validFrom)) {
            return response()->json([
                'error' => true,
                'message' => "Aún no es posible agendar turnos. 
                              Los turnos estarán disponibles a partir del " . $validFrom->format('d/m/Y') . "."
            ], 403);
        }
    }
        $person = StudentRegistration::where('dni', $idNumber)
            ->orWhere('email', $email)
            ->first();

        if (!$person) {
            $person = new StudentRegistration();
            
            // --- ARREGLO AQUÍ ---
            // Guardamos usando los nombres de columna correctos
            $person->cedula = $idNumber;
            $person->correo_puce = $email;
            
            // Asumo que 'names' también viene en el request, deberías añadirlo
            // $person->names = $request['names']; 
            
            $person->phone = $request['phone']; // Verifica que esta columna exista
            $person->is_puce = $request['isPuce'];
            $person->save();
        }

        $shift = new Shift();
        // Verifica que estas columnas de 'shifts' sean correctas
        $shift->cubicle_shift = $request['cubicle']; // Asumiendo 'cubicle_shift'
        $shift->date_shift = $request['date']; // Asumiendo 'date_shift'
        $shift->start_shift = $request['start_time']; // Asumiendo 'start_shift'
        $shift->end_shift = $request['end_time']; // Asumiendo 'end_shift'
        
        // Esto debe coincidir con el JOIN del método index()
        $shift->person_shift = $person->cedula; 
        
        $shift->status_shift = true; // Asumiendo 'status_shift'
        $shift->save();

        return ['data' => $shift, 'message' => 'New shift created successfully'];
    }

    public function destroy($id)
    {
        try {
            // Buscamos por 'id_shift' ya que es el 'id' que pasas desde el index
            $shift = Shift::where('id_shift', $id)->firstOrFail();

            // Eliminamos el turno de la base de datos
            $shift->delete();

            // Redirigimos "atrás" para mantener los filtros de la URL.
            return redirect()->back()->with('success', 'Turno eliminado correctamente.');

        } catch (\Exception $e) {
            // Redirigimos "atrás" también en caso de error.
            return redirect()->back()->with('error', 'Error al eliminar el turno.');
        }
    }

    public function getUserShifts(Request $request)
    {
        try {
            $shifts = Shift::where('cubicle_id', $request['cubicle'])
                ->where('date', '>=', now())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            return $shifts;
        } catch (\Exception $e) {
           // return Responses::errorResponse('Error retrieving shifts', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
      
    }

    public function index(Request $request)
    {
              
        // 1. Iniciar la consulta base con los joins
        $query = Shift::join("cubiculos", "cubiculos.id", "=", "shifts.cubicle_shift")
            ->leftJoin("student_registrations", "student_registrations.cedula", "=", "shifts.person_shift")          
            ->select(
                "shifts.id_shift as id",
                "shifts.date_shift",
                "shifts.start_shift as start_time",
                "shifts.end_shift as end_time",
                "cubiculos.nombre as cubicle_name",
                "student_registrations.names as student_name",
                "student_registrations.cedula as student_dni",  
                "student_registrations.correo_puce as student_email",
                "shifts.status_shift as status"
            );

        // --- INICIO DE FILTROS ---

        // 2. Filtrar por Cubículo(s)
        if ($request->has('cubiculo_id')) {
            // Si se pasa UN solo cubículo
            $query->where('cubiculos.id', $request->input('cubiculo_id'));

        } elseif ($request->has('cubiculo_ids')) {
            // Si se pasa una LISTA de cubículos (para "Todos los cubículos")
            $query->whereIn('cubiculos.id', $request->input('cubiculo_ids'));
        }

        // 3. Filtrar por las fechas del Horario
        if ($request->has('horario_id')) {
            
            // Buscamos el horario usando el 'horario_id' de la URL
            $schedule = Schedule::with('days')->find($request->input('horario_id'));

            if ($schedule) {
                // Si encontramos el horario, extraemos sus días
                // Esto funciona gracias a la relación 'days' que ya usas en tu vista
                $scheduleDates = $schedule->days->pluck('date_day')->map(function ($date) {
                    // Nos aseguramos que la fecha esté en formato Y-m-d para la BD
                    return Carbon::parse($date)->format('Y-m-d');
                });

                // Aplicamos el filtro de fechas a la consulta
                $query->whereIn('shifts.date_shift', $scheduleDates);
            }
        }
        
        // --- FIN DE FILTROS ---

        // 4. Aplicar ordenamiento y obtener los resultados
        $shifts = $query->orderBy("shifts.date_shift", "asc")
                       ->orderBy("shifts.start_shift", "asc")
                       ->get();

        // 5. Devolver la vista con los turnos filtrados
        return view('shifts.index', [
            'shifts' => $shifts
        ]);

    }
/*    
public function getShifts(Request $request, $fecha)
{
    $modalidad = $request->query('modalidad'); // puede ser 'presencial' o 'virtual'

    $query = \App\Models\Shift::whereDate('date_shift', $fecha)
        ->where('status_shift', true)
        ->join('cubiculos', 'cubiculos.id', '=', 'shifts.cubicle_shift')
        ->select('shifts.id_shift', 'shifts.start_shift', 'shifts.end_shift', 'cubiculos.nombre as cubiculo', 'cubiculos.tipo_atencion');

    if ($modalidad) {
        $query->where('cubiculos.tipo_atencion', $modalidad);
    }

    $turnos = $query->orderBy('shifts.start_shift')->get();

    return response()->json($turnos);
}*/

// ShiftController.php

public function getShifts(Request $request, $fecha)
{
    $modalidad = $request->query('modalidad'); // 'presencial' o 'virtual'

    try {
        // Normaliza la fecha
        try {
            $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Formato de fecha inválido',
                'message' => $e->getMessage(),
                'fecha_recibida' => $fecha
            ], 400);
        }

        // Construye la consulta
        $query = DB::table('shifts')
            ->join('cubiculos', 'cubiculos.id', '=', 'shifts.cubicle_shift')
            ->whereDate('shifts.date_shift', $fechaFormateada)
            ->where('shifts.status_shift', 1)
            ->select(
                'shifts.id_shift',
                'shifts.start_shift',
                'shifts.end_shift',
                'cubiculos.nombre as cubiculo',
                'cubiculos.tipo_atencion'
            );

        if ($modalidad) {
            $query->where('cubiculos.tipo_atencion', $modalidad);
        }

        $turnos = $query->orderBy('shifts.start_shift')->get();

        return response()->json([
            'success' => true,
            'fecha_consulta' => $fechaFormateada,
            'total_turnos' => $turnos->count(),
            'data' => $turnos
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error interno al procesar la solicitud.',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}



}
