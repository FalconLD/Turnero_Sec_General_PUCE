<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewShift;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\StudentRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    /**
     * Almacena un nuevo turno vinculado a un estudiante.
     */
    public function store(NewShift $request)
    {
        $idNumber = $request['identification'];
        $email = $request['email'];

        // 1. Verificación de vigencia del horario maestro
        $schedule = Schedule::orderBy('valid_from', 'desc')->first();
        if ($schedule) {
            $today = Carbon::today();
            $validFrom = Carbon::parse($schedule->valid_from);

            if ($today->lt($validFrom)) {
                return response()->json([
                    'error' => true,
                    'message' => "Los turnos estarán disponibles a partir del " . $validFrom->format('d/m/Y') . "."
                ], 403);
            }
        }

        // 2. Buscar o crear el registro del estudiante (StudentRegistration)
        $person = StudentRegistration::where('cedula', $idNumber)
            ->orWhere('correo_puce', $email)
            ->first();

        if (!$person) {
            $person = new StudentRegistration();
            $person->cedula = $idNumber;
            $person->correo_puce = $email;
            $person->names = $request['names'] ?? 'Estudiante Nuevo';
            $person->telefono = $request['phone'] ?? '0000000000';
            $person->direccion = $request['address'] ?? 'N/A';
            $person->edad = $request['age'] ?? 18;
            $person->fecha_nacimiento = $request['birthdate'] ?? now();
            $person->facultad = $request['faculty'] ?? 'N/A';
            $person->carrera = $request['career'] ?? 'N/A';
            $person->nivel = $request['level'] ?? 'N/A';
            $person->motivo = $request['reason'] ?? 'Matriculación';
            $person->save();
        }

        // 3. Crear el turno (Shift) vinculado por cédula
        $shift = new Shift();
        $shift->id_shift = (string) \Illuminate\Support\Str::uuid();
        $shift->cubicle_shift = $request['cubicle'];
        $shift->date_shift = $request['date'];
        $shift->start_shift = $request['start_time'];
        $shift->end_shift = $request['end_time'];
        $shift->person_shift = $person->cedula;
        $shift->status_shift = Shift::STATUS_AVAILABLE; 
        $shift->save();

        return response()->json(['data' => $shift, 'message' => 'Turno creado con éxito']);
    }

    /**
     * Lista los turnos con filtros de cubículo y horario para la vista administrativa.
     */
    public function index(Request $request)
    {
        $query = Shift::join("cubiculos", "cubiculos.id", "=", "shifts.cubicle_shift")
            ->leftJoin("student_registrations", "student_registrations.cedula", "=", "shifts.person_shift")
            ->select(
                "shifts.id_shift as id",
                "shifts.date_shift",
                "shifts.start_shift as start_time",
                "shifts.end_shift as end_time",
                "cubiculos.nombre as cubicle_name",
                "cubiculos.enlace_o_ubicacion as meeting_link",
                "student_registrations.names as student_name",
                "student_registrations.cedula as student_dni",
                "student_registrations.correo_puce as student_email",
                "shifts.status_shift as status"
            );

        // Filtro por cubículo
        if ($request->has('cubiculo_id')) {
            $query->where('cubiculos.id', $request->input('cubiculo_id'));
        }

        // Filtro por fechas del Horario Maestro
        if ($request->has('horario_id')) {
            $schedule = Schedule::with('days')->find($request->input('horario_id'));
            if ($schedule) {
                $scheduleDates = $schedule->days->pluck('date_day')->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                });
                $query->whereIn('shifts.date_shift', $scheduleDates);
            }
        }

        $shifts = $query->orderBy("shifts.date_shift", "asc")
                       ->orderBy("shifts.start_shift", "asc")
                       ->get();

        return view('operator.shifts.index', ['shifts' => $shifts]);
    }

    /**
     * ✅ API CORREGIDA: Obtener turnos disponibles SOLO modalidad virtual
     */
    public function getShifts(Request $request, $fecha)
    {
    
        try {
            $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');

            // Validar que la fecha no sea pasada
            if (Carbon::parse($fechaFormateada)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden consultar turnos de fechas pasadas.',
                    'data' => []
                ]);
            }

        // Obtener plan_estudio del estudiante desde sesión
        $planEstudio = session('student_plan_estudio');

        // Buscar el operating_area_id que corresponde a ese plan_estudio
        $operatingAreaId = DB::table('careers')
            ->where('career_code', $planEstudio) // career_code = plan_estudio
            ->value('operating_area_id');

        // Si no se encuentra, no mostrar turnos
        if (!$operatingAreaId) {
            return response()->json([
                'success' => true,
                'fecha_consulta' => $fechaFormateada,
                'data' => [],
                'total' => 0,
                'message' => 'No hay cubículos disponibles para tu carrera de estudios.'
            ]);
        }

            // Consultar turnos SOLO virtuales y disponibles
            $turnos = DB::table('shifts')
                ->join('cubiculos', 'cubiculos.id', '=', 'shifts.cubicle_shift')
                ->whereDate('shifts.date_shift', $fechaFormateada)
                ->where('date_shift', '>=', Carbon::today()) // Solo turnos de hoy y futuros
                ->where('shifts.status_shift', Shift::STATUS_AVAILABLE) // ✅ Solo disponibles
                ->where('cubiculos.tipo_atencion', 'virtual') // ✅ Solo virtuales
                ->whereNull('shifts.person_shift') // Asegurar que no esté ocupado
                ->where('cubiculos.operating_area_id', $operatingAreaId) // ← Cubiculos segun el area operativa
                ->select(
                    'shifts.id_shift',
                    'shifts.start_shift',
                    'shifts.end_shift',
                    'cubiculos.nombre as cubiculo',
                    'cubiculos.enlace_o_ubicacion as link'
                )
                ->orderBy('shifts.start_shift')
                ->get();

            return response()->json([
                'success' => true,
                'fecha_consulta' => $fechaFormateada,
                'data' => $turnos,
                'total' => $turnos->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getShifts: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un turno.
     */
    public function destroy($id)
    {
        try {
            $shift = Shift::where('id_shift', $id)->firstOrFail();
            $shift->delete();
            return redirect()->back()->with('success', 'Turno eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el turno.');
        }
    }
}