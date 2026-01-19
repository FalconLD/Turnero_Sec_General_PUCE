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

    //MÉTODO CORREGIDO: Guardar todo y asignar turno
    public function finish(Request $request)
    {
        // 1. Buscamos al estudiante por el ID que guardamos en la sesión al loguearse
        $student = StudentRegistration::find(session('student_id'));

        if (!$student) {
            return redirect()->route('token.login.form')->withErrors(['error' => 'Sesión expirada.']);
        }

        // 2. Mapeo manual de campos (Asegúrate de que los 'name' en el HTML coincidan)
        $student->edad = $request->input('edad');
        $student->telefono = $request->input('telefono');
        $student->direccion = $request->input('direccion');
        $student->fecha_nacimiento = $request->input('fecha_nacimiento');

        // Otros campos necesarios
        $student->nivel_instruccion = $request->input('nivel_instruccion');
        $student->motivo = $request->input('motivo');

        // 3. Rescatar datos de sesión (Garantiza que Facultad y Carrera se guarden)
        $student->banner_id = session('student_banner_id');
        $student->plan_estudio = session('student_plan_estudio');
        $student->facultad     = session('student_facultad') ?? $student->facultad;
        $student->carrera      = session('student_carrera') ?? $student->carrera;

        $student->tomado = 1;

        // 4. GUARDADO Y REDIRECCIÓN
        if($student->save()) {
        // Volvemos a la misma página, activando el paso 1 (Datos) mediante el 'step'
        return redirect()->route('student.personal')
            ->with('success', 'Datos guardados correctamente.')
            ->with('step', 1);
    }

    // Si llega aquí es porque hubo un error al guardar
    return back()->with('error', 'No se pudieron actualizar los datos.');
    }

    // Método para agendar turno (estudiantes que ya existen)
    public function agendarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:shifts,id_shift',
            'cedula' => 'required|exists:student_registrations,cedula',
        ]);

        $student = StudentRegistration::where('cedula', $request->cedula)->first();
        $turno = Shift::find($request->turno_id);

        if (!$student || !$turno) {
            return back()->with('error', 'No se encontró el estudiante o el turno.');
        }

        // Validar si el turno ya está ocupado
        if ($turno->status_shift == 0) {
            return back()->with('error', 'El turno seleccionado ya fue ocupado.');
        }

        // Asignar turno
        $turno->person_shift = $student->cedula;
        $turno->status_shift = 0;
        $turno->save();

        $student->tomado = 0;
        $student->save();

        // Enviar correo
        try {
            Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));
        } catch (\Exception $e) {
            Log::error("Error enviando correo: " . $e->getMessage());
        }

        if ($student->save()) {
            // Si la petición es AJAX, devolvemos JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos guardados correctamente'
                ]);
            }

            // Si no es AJAX, redirigimos al formulario pero con un marcador de éxito
            return redirect()->route('student.personal')->with('step', 1)->with('success', 'Información guardada.');
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

    public function eliminarTurno(Request $request)
    {
        $cedula = $request->cedula;

        $student = StudentRegistration::where('cedula', $cedula)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Estudiante no encontrado.');
        }

        $turno = Shift::where('person_shift', $cedula)->first();

        if (!$turno) {
            return redirect()->back()->with('error', 'No se encontró un turno asociado a esta cédula.');
        }

        try {
            // Liberar turno
            $turno->person_shift = null;
            $turno->status_shift = 1;
            $turno->save();

            // Marcar al estudiante como "puede tomar otro turno"
            $student->tomado = 1;
            $student->save();

            return redirect()->route('student.turno')
                ->with('success', 'El turno ha sido eliminado correctamente. Ahora puede agendar un nuevo turno.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar turno: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el turno.');
        }
    }
}
