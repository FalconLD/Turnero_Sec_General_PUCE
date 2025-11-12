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

class StudentRegistrationController extends Controller
{
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
        

        $terminos = \App\Models\Parameter::where('clave', 'TERM')->first();

       
        $schedule = \App\Models\Schedule::orderBy('valid_from', 'desc')->first();
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

        
        return view('student.personal_data', compact('terminos'));
    }

    // Guardar datos personales (opcional si usas finish())
    public function store(Request $request)
    {
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

        // Ahora, crea el registro de pago vinculado en la nueva tabla 'payments'
        try {
            $student->payment()->create([
                'amount'           => $valor, // El valor que calculaste
                'payment_method'   => $request->forma_pago,
                'comprobante_path' => $comprobantePath, // La ruta del archivo
                'status'           => 'pending', // Siempre inicia como pendiente
            ]);
        } catch (\Exception $e) {
            // Manejar un error aquí si falla la creación del pago
            // (Aunque si la migración está bien, no debería fallar)
        }

        
        $turno = Shift::with('cubicle')->find($request->turno_id);
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