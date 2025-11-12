<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentRegistration;
use App\Models\Parameter; // Donde está el parámetro TERM
use App\Models\Shift;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentRegistered;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Faculty; // <-- AÑADIR ESTE
use Illuminate\Validation\Rule; // <-- AÑADIR ESTE
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentRegistrationController extends Controller
{
    // NUEVO: Login mediante token (desde PUCE)
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

    if (!$cedula) {
        return redirect()->route('student.token.error')
            ->withErrors(['error' => 'El token no contiene cédula válida.']);
    }

    // 🔹 Verificar si el estudiante ya existe
    $student = StudentRegistration::where('cedula', $cedula)->first();

    if ($student) {
        // ✅ Ya existe → ir directamente al paso 5 (agendamiento)
        session([
            'student_logged_in' => true,
            'student_id' => $student->id,
            'student_cedula' => $student->cedula,
            'student_name' => $student->names,
        ]);

        return redirect()
            ->route('student.agendamiento')
            ->with('info', 'Bienvenido nuevamente, por favor agende su cita.');
    }

    // 🔹 NO crear registro, solo guardar datos en sesión
    session([
        'student_logged_in' => true,
        'student_cedula' => $cedula,
        'student_name' => $nombre,
        'student_usuario' => $usuario,
        'student_facultad' => $facultad,
        'student_carrera' => $carrera,
        'student_correo' => $usuario ? "{$usuario}@puce.edu.ec" : null,
    ]);

    // Redirigir al formulario de datos personales (paso 1)
    return redirect()->route('student.personal')
        ->with('info', 'Complete sus datos personales para continuar.');
}


    // Paso 1: Términos
    public function showTerms()
    {
        $terminos = Parameter::where('clave', 'TERM')->first();
        //return view('student.terms', compact('terminos'));
    }

    // Validar aceptación de términos
    public function acceptTerms(Request $request)
    {
        $request->validate([
            'acepta_terminos' => 'accepted',
        ]);

        session(['accepted_terms' => true]);

        return redirect()->route('student.personal');
    }

    // Paso 2: Datos personales
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
    return view('student.personal_data', [
        'terminos' => $terminos,
        'student' => $student,
        'student_name' => session('student_name'),
        'student_cedula' => session('student_cedula'),
        'student_correo' => session('student_correo'),
    ]);
}


    // Guardar datos personales (opcional si usas finish())
    public function store(Request $request)
    {
        if (!session('student_logged_in')) {
            return redirect()->route('token.login.form')->withErrors(['error' => 'Debe iniciar sesión con su token.']);
        }
        $request->validate([
            'names' => 'required|string|max:255',
            'cedula' => 'required|string|max:36',
            'edad' => 'required|integer|min:0',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'correo_puce' => 'required|email',
            'facultad' => 'required|string',
            'carrera' => 'required|string',
            'nivel' => 'required|string',
            'motivo' => 'required|string',
            'nivel_instruccion' => 'required|string',
            'beca_san_ignacio' => 'required|string',
            'forma_pago' => 'required|string',
        ]);

        $valor = ($request->nivel_instruccion === 'grado')
                    ? ($request->beca_san_ignacio === 'si' ? 0.50 : 2.50)
                    : 7.50;

        StudentRegistration::create(array_merge(
            $request->only([
                'names','cedula','edad','fecha_nacimiento','telefono','direccion',
                'correo_puce','facultad','carrera','nivel','motivo',
                'nivel_instruccion','beca_san_ignacio','forma_pago'
            ]),
            [
                'valor_pagar' => $valor,
                'acepta_terminos' => true,
                'comprobante' => $request->file('comprobante') ? $request->file('comprobante')->store('comprobantes') : null
            ]
        ));

        return redirect()->route('student.success')->with('success', 'Registro guardado correctamente.');
    }

    public function success()
    {
        return view('student.success');
    }

    // Guardar todo y asignar turno
    public function finish(Request $request)
    {
        // =======================================================
        // 1. REGLAS DE VALIDACIÓN 
        // =======================================================
        $request->validate([
            // Datos Personales
            'names' => 'required|string|max:255',
            'cedula' => 'required|string|max:36',
            'edad' => 'required|integer|min:0',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'correo_puce' => 'required|email',
            
            // Turno
            'turno_id' => 'required|exists:shifts,id_shift',
            
            // --- VALIDACIÓN ACADÉMICA 
            'nivel_instruccion' => 'required|string|in:grado,tec,posgrado,especializacion',
            'facultad' => 'required|string', 
            'carrera' => 'required|string', 
            'nivel' => [ // Nivel de semestre (Primero, Segundo...)
                Rule::requiredIf(in_array($request->input('nivel_instruccion'), ['grado', 'tec'])),
                'nullable', // Permite que sea nulo si es posgrado
                'string',
            ],
            'beca_san_ignacio' => [
                Rule::requiredIf(in_array($request->input('nivel_instruccion'), ['grado', 'tec'])),
                'nullable', // Permite que sea nulo si es posgrado
                'string',
            ],
            // --- FIN VALIDACIÓN ACADÉMICA ---

            // Pago y Motivo
            'motivo' => 'required|string',
            'forma_pago' => 'required|string',
        ]);

        $cedula = trim($request->cedula);
        $correo = trim($request->correo_puce);
        // ===== Validación de identificadores unicos y controlar errores =====
        if (StudentRegistration::where('cedula', $cedula)->exists()) { // Usar $cedula (limpia)
            return redirect()->back()
                            ->with('error', 'La cédula ' . $cedula . ' ya se encuentra registrada.')
                            ->withInput(); 
        }
        
        if (StudentRegistration::where('correo_puce', $correo)->exists()) { // Usar $correo (limpio)
            return redirect()->back()
                            ->with('error', 'El correo electrónico ' . $correo . ' ya se encuentra registrado.')
                            ->withInput();
        }
        // ===== FIN DE LA VALIDACIÓN =====

        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $comprobantePath = $request->file('comprobante')->store('public/comprobantes');
            $comprobantePath = str_replace('public/', '', $comprobantePath);
        }

        
       // 2.LÓGICA DE VALOR A PAGAR
        $isGradoOrTec = in_array($request->nivel_instruccion, ['grado', 'tec']); // <--- CAMBIO AQUÍ
        
        $valor = ($isGradoOrTec)
                    ? ($request->beca_san_ignacio === 'si' ? 0.50 : 2.50) // Lógica Grado/Tec
                    : 7.50; // Lógica Posgrado/Especialización

        // =======================================================
        // 2. CORRECCIÓN DEL CREATE (Añadiendo 'motivo')
        // =======================================================
        $student = StudentRegistration::create([
            'names' => $request->names,
            'cedula' => $cedula,
            'edad' => $request->edad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'correo_puce' => $correo,
            'facultad' => $request->facultad,
            'carrera' => $request->carrera,
            'nivel_instruccion' => $request->nivel_instruccion,
            // SI es 'grado', guarda el nivel (ej: 'Primero'). SI NO, guarda 'N/A'.
            'nivel' => $isGradoOrTec ? $request->nivel : 'N/A',
            // SI es 'grado', guarda la respuesta (ej: 'si' o 'no'). SI NO, guarda 'no' por defecto.
            'beca_san_ignacio' => $isGradoOrTec ? $request->beca_san_ignacio : 'no',
            'motivo'=>$request->motivo,
            
            'forma_pago' => $request->forma_pago,
            'valor_pagar' => $valor,
            'acepta_terminos' => true,
            'comprobante' => $comprobantePath,
            'tomado' => 0, 
        ]);

        
        $turno = Shift::find($request->turno_id);
        $turno->person_shift = $student->cedula;
        $turno->status_shift = 0; // 0 = Ocupado
        $turno->save();


        
        Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));

        return redirect()->route('student.success')->with('success', 'Registro y turno guardados correctamente.');
    }
    
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
    $turno->status_shift = 0; // 0 = Ocupado
    $turno->save();

    $student->tomado = 0;
    $student->save();
    // Enviar correo
    try {
        Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));
    } catch (\Exception $e) {
        Log::error("Error enviando correo: " . $e->getMessage());
    }

    return redirect()
        ->route('student.success')
        ->with('success', 'Su cita ha sido agendada correctamente.');
}


    public function validarDatos(Request $request)
    {
        $request->validate([
            'cedula' => 'required',
            'correo_puce' => 'required|email',
        ]);

        // --- CORRECCIÓN: Usar trim() ---
        $cedula = trim($request->cedula);
        $correo = trim($request->correo_puce);

        $existe = \App\Models\StudentRegistration::where('cedula', $cedula)
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
            return view('student.logout'); // <- crea esta vista
        }





    /**
     * Devuelve facultades filtradas por nivel de instrucción (Grado/Posgrado)
     */
    public function getFaculties(Request $request)
    {
        $request->validate(['nivel_instruccion' => 'required|string']);
        $nivelForm = $request->nivel_instruccion; // 'grado', 'tec', 'posgrado', 'especializacion'

        $query = Faculty::select('facultad')->distinct();

        // Mapeo de los valores del formulario a los valores de la BD
        switch ($nivelForm) {
            case 'grado':
                $query->where('nivel', 'Grado');
                break;
            case 'tec':
                $query->where('nivel', 'Tec');
                break;
            case 'posgrado':
                // Asumiendo que 'posgrado' en el form se refiere a 'Maestría' en tu BD
                $query->where('nivel', 'Maestría'); 
                break;
            case 'especializacion':
                $query->where('nivel', 'Especialización');
                break;
            default:
                $query->whereRaw('1 = 0'); // No devuelve nada si el valor es inválido
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
        
        // Mapeo de los valores del formulario a los valores de la BD
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
                $query->whereRaw('1 = 0'); // No devuelve nada
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
        return view('student.turno_actual', compact('student', 'turnoActual'));
    }

    // Si tomado = 1, permitir agendar otro turno
    if ($student->tomado == 1) {
        return view('student.agendamiento', compact('student'));
    }

    // Si no tiene turno asignado en absoluto
    if (!$turnoActual) {
        return view('student.agendamiento', compact('student'));
    }

    // Caso de respaldo (por si ocurre algo inesperado)
    return redirect()->route('student.token.error')->with('error', 'No se pudo determinar el estado del turno.');
}


public function eliminarTurno(Request $request)
{
    $cedula = $request->cedula;

    // Buscar estudiante por cédula
    $student = StudentRegistration::where('cedula', $cedula)->first();

    if (!$student) {
        return redirect()->back()->with('error', 'Estudiante no encontrado.');
    }

    // Buscar turno asociado al estudiante
    $turno = Shift::where('person_shift', $cedula)->first();

    if (!$turno) {
        return redirect()->back()->with('error', 'No se encontró un turno asociado a esta cédula.');
    }

    try {
        // Liberar turno
        $turno->person_shift = null;
        $turno->status_shift = 1; // 1 = Disponible
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
