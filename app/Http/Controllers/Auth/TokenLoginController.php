<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\StudentRegistration;
use Carbon\Carbon;
class TokenLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.token-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // 1️⃣ Verificar token PUCE
        $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/" . $request->token;
        $response = Http::get($url);

        if ($response->failed()) {
            return back()->withErrors(['token' => 'No se pudo verificar el token.']);
        }

        $data = $response->json();

        if (!isset($data['status']) || $data['status'] !== 'success') {
            return back()->withErrors(['token' => 'Token inválido o expirado.']);
        }

        $info = $data['data'];

        // 2️⃣ Crear o actualizar el estudiante
        $student = StudentRegistration::updateOrCreate(
            ['cedula' => $info['cedula']],
            [
                'names' => $info['nombre'],
                'correo_puce' => $info['usuario'] . '@puce.edu.ec',
                'facultad' => null,
                'carrera' =>null,
                'nivel' => null,
                'nivel_instruccion' => null,
                'beca_san_ignacio' => null,
                'forma_pago' => null,
                'edad' => null,
                'fecha_nacimiento' => null,
                'telefono' => null,
                'direccion' => null,
                'motivo' => null, 
                'acepta_terminos' => false,
            ]
        );

        // 3️⃣ Guardar datos básicos del estudiante en la sesión
        session([
            'student_logged_in' => true,
            'student_id' => $student->id,
            'student_name' => $student->names,
            'student_cedula' => $student->cedula,
        ]);

        // 4️⃣ Redirigir al formulario principal de estudiantes
        return redirect()->route('student.personal');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'student_logged_in',
            'student_id',
            'student_name',
            'student_cedula'
        ]);

        return redirect()->route('token.login.form');
    }
   public function loginWithToken($token,Request $request)
{
    // 🔹 Llamada al servicio remoto (ajusta la URL si tu entorno cambia)
    $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/{$token}";
    $response = Http::get($url);
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

    if ($response->failed() || !$response->json('status') || $response->json('status') !== 'success') {
        return redirect()->route('student.token.error')
            ->withErrors(['error' => 'Token inválido o expirado.']);
    }

    $data = $response->json('data');

    // 🔹 Extraer los datos del token
    $cedula   = $data['cedula']   ?? null;
    $nombre   = $data['nombre']   ?? null;
    $usuario  = $data['usuario']  ?? null;
    $facultad = $data['facultad'] ?? null;
    $carrera  = $data['carrera']  ?? null;

    if (!$cedula) {
        return redirect()->route('student.token.error')
            ->withErrors(['error' => 'El token no contiene cédula válida.']);
    }

    // 🔹 Buscar si el estudiante ya existe
    $student = StudentRegistration::where('cedula', $cedula)->first();

    if ($student) {
        // ✅ Ya existe — guardar sesión y redirigir directamente al agendamiento
        session([
            'student_logged_in' => true,
            'student_id' => $student->id,
            'student_cedula' => $student->cedula,
        ]);

        // 🔹 Nueva ruta que mostrará solo el paso 5
        return redirect()
            ->route('student.agendamiento')
            ->with('info', 'Bienvenido nuevamente, por favor agende su cita.');
    }
    $isGradoOrTec = in_array($request->nivel_instruccion, ['grado', 'tec']);
    // 🔹 Si no existe, crear nuevo estudiante
    $student = StudentRegistration::create([
    'cedula' => $cedula,
    'names' => $nombre,
    'correo_puce' => $usuario ? "{$usuario}@puce.edu.ec" : null,
    'facultad' => $facultad,
    'carrera' => $carrera,
    'edad' => 0,
    'fecha_nacimiento' =>Carbon::now()->toDateString(),
    'nivel' => $isGradoOrTec ? $request->nivel : 'N/A',
    'beca_san_ignacio' => $isGradoOrTec ? $request->beca_san_ignacio : 'no',
    'telefono' => $request->telefono,
    'direccion' => $request->direccion,
    'motivo' => $request->motivo, 
    'acepta_terminos' => false,
    'tomado' => 0,
    ]);

    // Guardar sesión
    session([
        'student_logged_in' => true,
        'student_id' => $student->id,
        'student_cedula' => $student->cedula,
         'student_name' => $student->names,
    ]);

    // 🔹 Redirigir al formulario de datos personales
    return redirect()->route('student.personal');
}



}
