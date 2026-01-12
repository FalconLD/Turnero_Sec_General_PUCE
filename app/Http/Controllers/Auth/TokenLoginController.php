<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\StudentRegistration;

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
        session()->put([
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

    
    public function loginWithToken($token)
    {
        
    // // 🔧 MODO PRUEBA
    session([
        'student_logged_in' => true,
        'student_id' => 1,
        'student_cedula' => '0102030405',
        'student_name' => 'Estudiante Prueba',
        'correo_puce' => 'estudiantePrueba@puce.edu.ec',
    ]);

    return redirect()->route('student.personal');

    //=======//
    // 🔧 MODO PRUEBA: Crea el registro real 
    // $student = \App\Models\StudentRegistration::updateOrCreate(
    //     ['cedula' => '0102030405'], // Si ya existe esta cédula, no crea otro
    //     [
    //         'names' => 'Estudiante Prueba',
    //         'correo_puce' => 'prueba@puce.edu.ec',
    //         'acepta_terminos' => true,
    //     ]
    // );

    // session([
    //     'student_logged_in' => true,
    //     'student_id' => $student->id, // Ahora este ID es REAL y existe en DBeaver
    //     'student_cedula' => $student->cedula,
    //     'student_name' => $student->names,
    // ]);

    // return redirect()->route('student.personal');
    //====================//

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

}
