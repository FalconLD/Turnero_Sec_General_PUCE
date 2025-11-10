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

class StudentRegistrationController extends Controller
{
    // NUEVO: Login mediante token (desde PUCE)
    public function loginWithToken($token)
    {
        // Ejemplo: URL del servicio remoto (ajústala según tu API real)
        $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/{$token}";

        $response = Http::get($url);

        if ($response->failed() || !$response->json('status') || $response->json('status') !== 'success') {
            return redirect()->route('student.token.error')
                ->withErrors(['error' => 'Token inválido o expirado.']);
        }

        $data = $response->json('data');

        // Extraer los datos que vienen del token
        $cedula = $data['cedula'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $facultad = $data['facultad'] ?? null;
        $carrera = $data['carrera'] ?? null;

        if (!$cedula) {
            return redirect()->route('student.token.error')
                ->withErrors(['error' => 'El token no contiene cédula válida.']);
        }

        // Buscar o crear el estudiante
        $student = StudentRegistration::firstOrCreate(
            ['cedula' => $cedula],
            [
                'names' => $nombre,
                'correo_puce' => $usuario ? "{$usuario}@puce.edu.ec" : null,
                'facultad' => $facultad,
                'carrera' => $carrera,
            ]
        );

        // Guardar sesión
        session([
            'student_logged_in' => true,
            'student_id' => $student->id,
        ]);

        // Redirigir al formulario de datos personales
        return redirect()->route('student.personal');
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
        //  Verificar sesión de estudiante
        if (!session('student_logged_in')) {
            return redirect()->route('token.login.form')->withErrors(['error' => 'Debe iniciar sesión con su token.']);
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

        //  Recuperar datos del estudiante logueado
        $student = StudentRegistration::find(session('student_id'));

        // Pasamos los datos del estudiante al formulario
        return view('student.personal_data', [
            'terminos' => $terminos,
            'student' => $student
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
            
            'motivo' => $request->motivo, 
            'forma_pago' => $request->forma_pago,
            'valor_pagar' => $valor,
            'acepta_terminos' => true,
            'comprobante' => $comprobantePath
        ]);

        
        $turno = Shift::find($request->turno_id);
        $turno->person_shift = $student->cedula;
        $turno->status_shift = 0; // 0 = Ocupado
        $turno->save();


        
        Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));

        return redirect()->route('student.success')->with('success', 'Registro y turno guardados correctamente.');
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

}
