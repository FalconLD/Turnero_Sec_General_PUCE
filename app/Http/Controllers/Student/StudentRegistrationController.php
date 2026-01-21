<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentRegistration;
use App\Models\Parameter;
use App\Models\Shift;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentRegistered;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Faculty;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class StudentRegistrationController extends Controller
{
    // Login mediante token (desde PUCE)
    public function loginWithToken($token)
    {
        // URL del servicio remoto
        $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/{$token}";

        $response = Http::get($url);

        if ($response->failed() || !$response->json('status') || $response->json('status') !== 'success') {
            return redirect()->route('student.token.error')
                ->withErrors(['error' => 'Token inválido o expirado.']);
        }

        $data = $response->json('data');

        // Extraer datos del token
        $cedula = $data['cedula'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $facultad = $data['facultad'] ?? null;
        $carrera = $data['carrera'] ?? null;
        $plan = $data['plan'] ?? 'N/A';

        if (!$cedula) {
            return redirect()->route('student.token.error')
                ->withErrors(['error' => 'El token no contiene cédula válida.']);
        }

        // 🔹 Verificar si el estudiante ya existe
        $student = StudentRegistration::where('cedula', $cedula)->first();

        if ($student) {
            // ✅ Ya existe → ir directamente al paso de agendamiento
            session([
                'student_logged_in' => true,
                'student_id' => $student->id,
                'student_cedula' => $student->cedula,
                'student_name' => $student->names,
                'student_plan' => $student->plan,
            ]);

            return redirect()
                ->route('student.agendamiento')
                ->with('info', 'Bienvenido nuevamente, por favor agende su cita.');
        }

        // 🔹 NO crear registro todavía, solo guardar datos en sesión
        session([
            'student_logged_in' => true,
            'student_cedula' => $cedula,
            'student_name' => $nombre,
            'student_usuario' => $usuario,
            'student_facultad' => $facultad,
            'student_carrera' => $carrera,
            'student_correo' => $usuario ? "{$usuario}@puce.edu.ec" : null,
            'student_plan' => $plan,
        ]);

        // Redirigir al formulario de datos personales (paso 1)
        return redirect()->route('student.personal')
            ->with('info', 'Complete sus datos personales para continuar.');
    }

    // Mostrar formulario de datos personales
    public function showPersonalForm()
    {
        // Verificar sesión de estudiante
        if (!session('student_logged_in')) {
            return redirect()->route('token.login.form')
                ->withErrors(['error' => 'Debe iniciar sesión con su token.']);
        }

        $terminos = Parameter::where('clave', 'TERM')->first();
        $schedule = Schedule::orderBy('valid_from', 'desc')->first();
        $today = Carbon::today();

        $isAvailable = false;
        $startDate = null;

        if ($schedule) {
            $startDate = Carbon::parse($schedule->valid_from);
            $isAvailable = $today->greaterThanOrEqualTo($startDate);
        }

        if (!$isAvailable) {
            return view('student.not_available', [
                'startDate' => $startDate?->format('d/m/Y'),
                'terminos' => $terminos
            ]);
        }

        // 🔹 Recuperar datos del estudiante (BD o sesión)
        $student = null;

        if (session('student_id')) {
            // Caso: ya tiene registro en BD
            $student = StudentRegistration::find(session('student_id'));
        }

        if (!$student) {
            // Caso: estudiante nuevo → usar los datos del token en sesión
            $student = (object) [
                'names' => session('student_name'),
                'cedula' => session('student_cedula'),
                'correo_puce' => session('student_correo'),
                'facultad' => session('student_facultad'),
                'carrera' => session('student_carrera'),
            ];
        }

        // Pasamos los datos al formulario
        return view('student.registration.personal_data', [
            'terminos' => $terminos,
            'student' => $student,
            'student_name' => session('student_name'),
            'student_cedula' => session('student_cedula'),
            'student_correo' => session('student_correo'),
        ]);
    }

    public function success()
    {
        return view('student.status.success');
    }

        /**
         * ✅ MÉTODO CORREGIDO: Guardar datos personales Y asignar turno
         */
        public function finish(Request $request)
        {
            // Validar datos requeridos
            $request->validate([
                'edad' => 'required|integer|min:16|max:100',
                'telefono' => 'required|string|max:15',
                'direccion' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'turno_id' => 'required|string', // ✅ AGREGADO: validar turno
            ]);

            try {
                DB::beginTransaction();

                // 1. Buscar o crear el estudiante
                $cedula = session('student_cedula');
                
                if (!$cedula) {
                    DB::rollBack();
                    return redirect()->route('token.login.form')
                        ->withErrors(['error' => 'Sesión expirada.']);
                }

                $student = StudentRegistration::where('cedula', $cedula)->first();

                if (!$student) {
                    // Crear nuevo registro
                    $student = new StudentRegistration();
                    $student->cedula = $cedula;
                    $student->correo_puce = session('student_correo');
                    $student->names = session('student_name');
                    $student->facultad = session('student_facultad');
                    $student->carrera = session('student_carrera');
                    $student->plan = session('student_plan');
                }

                // 2. Actualizar datos personales
                $student->edad = $request->input('edad');
                $student->telefono = $request->input('telefono');
                $student->direccion = $request->input('direccion');
                $student->fecha_nacimiento = $request->input('fecha_nacimiento');
                $student->nivel_instruccion = $request->input('nivel_instruccion', 'grado');
                $student->motivo = $request->input('motivo', 'Matriculación');
                $student->forma_pago = $request->input('forma_pago', 'Efectivo');
                $student->acepta_terminos = $request->input('acepta_terminos') ? 1 : 0;

                
                
                // Datos de sesión
                $student->banner_id = session('student_banner_id');
                $student->plan_estudio = session('student_plan_estudio');
                
                // ✅ IMPORTANTE: Marcar como que TIENE turno asignado
                $student->tomado = 0; // 0 = Ya tiene turno
                
                $student->save();

                // 3. ✅ ASIGNAR EL TURNO
                $turnoId = $request->input('turno_id');
                
                if (!$turnoId) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'Debe seleccionar un turno.']);
                }

                // Buscar el turno con bloqueo
                $turno = Shift::where('id_shift', $turnoId)
                    ->lockForUpdate()
                    ->first();

                if (!$turno) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'El turno seleccionado no existe.']);
                }

                // Validar que el turno esté disponible
                if ($turno->status_shift == 0 || $turno->person_shift !== null) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'El turno seleccionado ya fue ocupado por otro estudiante.']);
                }

                // Asignar turno al estudiante
                $turno->person_shift = $student->cedula;
                $turno->status_shift = 0; // 0 = Ocupado
                $turno->save();

                DB::commit();

                // 4. Enviar correo de confirmación
                try {
                    Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));
                } catch (\Exception $e) {
                    Log::error("Error enviando correo a {$student->correo_puce}: " . $e->getMessage());
                }

                // 5. Guardar ID en sesión
                session(['student_id' => $student->id]);

                // 6. Redirigir a página de éxito
                return redirect()->route('student.success')
                    ->with('success', 'Registro completado. Su turno ha sido agendado exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en finish(): ' . $e->getMessage());
                
                return back()->withErrors(['error' => 'Ocurrió un error al procesar su registro. Intente nuevamente.']);
            }
        }

    /**
     * ✅ MÉTODO CORREGIDO: Agendar turno para estudiante
     */
    public function agendarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|string',
            'cedula' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buscar estudiante
            $student = StudentRegistration::where('cedula', $request->cedula)->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Estudiante no encontrado.'
                ], 404);
            }

            // 2. Verificar si el estudiante puede tomar turno
            if ($student->tomado == 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tiene un turno asignado. Debe cancelarlo antes de agendar uno nuevo.'
                ], 400);
            }

            // 3. Buscar turno usando id_shift (UUID)
            $turno = Shift::where('id_shift', $request->turno_id)
                ->lockForUpdate() // Bloqueo pesimista para evitar condiciones de carrera
                ->first();

            if (!$turno) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Turno no encontrado.'
                ], 404);
            }

            // 4. Validar que el turno esté disponible
            if ($turno->status_shift == 0 || $turno->person_shift !== null) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El turno seleccionado ya fue ocupado por otro estudiante.'
                ], 409);
            }

            // 5. Asignar turno al estudiante
            $turno->person_shift = $student->cedula;
            $turno->status_shift = 0; // 0 = Ocupado
            $turno->save();

            // 6. Marcar al estudiante como que tiene turno
            $student->tomado = 0; // 0 = Ya tiene turno asignado
            $student->save();

            DB::commit();

            // 7. Enviar correo de confirmación
            try {
                Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));
            } catch (\Exception $e) {
                Log::error("Error enviando correo a {$student->correo_puce}: " . $e->getMessage());
            }

            // 8. Retornar respuesta exitosa
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Turno agendado exitosamente. Revise su correo para más detalles.',
                    'turno' => [
                        'fecha' => $turno->date_shift,
                        'hora_inicio' => $turno->start_shift,
                        'hora_fin' => $turno->end_shift,
                    ]
                ]);
            }

            return redirect()->route('student.success')
                ->with('success', 'Turno agendado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agendar turno: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al procesar su solicitud. Intente nuevamente.'
                ], 500);
            }

            return back()->with('error', 'Error al agendar el turno.');
        }
    }

    public function validarDatos(Request $request)
    {
        $request->validate([
            'cedula' => 'required',
            'correo_puce' => 'required|email',
        ]);

        $cedula = trim($request->cedula);
        $correo = trim($request->correo_puce);

        $existe = StudentRegistration::where('cedula', $cedula)
            ->orWhere('correo_puce', $correo)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o cédula ya existentes'
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function studentLogout(Request $request)
    {
        // Cierra sesión de Laravel (usuarios normales)
        Auth::logout();

        // Limpia sesión del estudiante
        $request->session()->flush();

        // Mostrar vista de despedida
        return view('student.status.logout');
    }

    /**
     * Devuelve facultades filtradas por nivel de instrucción (Grado/Posgrado)
     */
    public function getFaculties(Request $request)
    {
        $request->validate(['nivel_instruccion' => 'required|string']);
        $nivelForm = $request->nivel_instruccion;

        $query = Faculty::select('facultad')->distinct();

        switch ($nivelForm) {
            case 'grado':
                $query->where('nivel', 'Grado');
                break;
            case 'tec':
                $query->where('nivel', 'Tec');
                break;
            case 'posgrado':
                $query->where('nivel', 'Maestría');
                break;
            case 'especializacion':
                $query->where('nivel', 'Especialización');
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        $faculties = $query->orderBy('facultad')->get();
        return response()->json($faculties);
    }

    /**
     * Devuelve programas (carreras) filtrados por facultad y nivel.
     */
    public function getPrograms(Request $request)
    {
        $request->validate([
            'nivel_instruccion' => 'required|string',
            'facultad' => 'required|string',
        ]);

        $nivelForm = $request->nivel_instruccion;
        $facultad = $request->facultad;

        $query = Faculty::select('programa_desc')
            ->where('facultad', $facultad);

        switch ($nivelForm) {
            case 'grado':
                $query->where('nivel', 'Grado');
                break;
            case 'tec':
                $query->where('nivel', 'Tec');
                break;
            case 'posgrado':
                $query->where('nivel', 'Maestría');
                break;
            case 'especializacion':
                $query->where('nivel', 'Especialización');
                break;
            default:
                $query->whereRaw('1 = 0');
        }

        $programs = $query->orderBy('programa_desc')->get();
        return response()->json($programs);
    }

    /**
     * ✅ Página de agendamiento de turnos
     */
    public function agendamiento()
    {
        $student = StudentRegistration::find(session('student_id'));

        if (!$student) {
            return redirect()->route('student.token.error');
        }

        session()->put('student_name', $student->names);

        // Buscar si el estudiante ya tiene un turno tomado
        $turnoActual = Shift::where('person_shift', $student->cedula)
            ->where('status_shift', 0)
            ->first();

        // Si tiene un turno y tomado = 0, mostrar el turno actual
        if ($turnoActual && $student->tomado == 0) {
            return view('student.status.turno_actual', compact('student', 'turnoActual'));
        }

        // Si tomado = 1, permitir agendar otro turno
        if ($student->tomado == 1) {
            return view('student.booking.agendamiento', compact('student'));
        }

        // Si no tiene turno asignado en absoluto
        if (!$turnoActual) {
            return view('student.booking.agendamiento', compact('student'));
        }

        // Caso de respaldo
        return redirect()->route('student.token.error')->with('error', 'No se pudo determinar el estado del turno.');
    }

    /**
     * ✅ MÉTODO CORREGIDO: Eliminar turno del estudiante
     */
    public function eliminarTurno(Request $request)
    {
        $cedula = $request->cedula;

        try {
            DB::beginTransaction();

            $student = StudentRegistration::where('cedula', $cedula)->first();

            if (!$student) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Estudiante no encontrado.');
            }

            $turno = Shift::where('person_shift', $cedula)
                ->where('status_shift', 0)
                ->lockForUpdate()
                ->first();

            if (!$turno) {
                DB::rollBack();
                return redirect()->back()->with('error', 'No se encontró un turno activo asociado a esta cédula.');
            }

            // Liberar turno
            $turno->person_shift = null;
            $turno->status_shift = 1; // 1 = Disponible
            $turno->save();

            // Marcar al estudiante como disponible para tomar otro turno
            $student->tomado = 1; // 1 = Puede tomar turno
            $student->save();

            DB::commit();

            return redirect()->route('student.agendamiento')
                ->with('success', 'El turno ha sido eliminado correctamente. Ahora puede agendar un nuevo turno.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar turno: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el turno.');
        }
    }
}