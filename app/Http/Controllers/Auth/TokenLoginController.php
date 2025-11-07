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
                'facultad' => $info['facultad'] ?? '',
                'carrera' => $info['carrera'] ?? '',
                'nivel' => 'N/A',
                'nivel_instruccion' => 'grado',
                'beca_san_ignacio' => 'no',
                'forma_pago' => 'Efectivo',
                'edad' => 0,
                'fecha_nacimiento' => now(),
                'telefono' => '',
                'direccion' => '',
                'motivo' => '',
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
    public function loginWithToken($token)
{
    $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/" . $token;
    $response = Http::get($url);

    if ($response->failed()) {
        return redirect()->route('student.token.error')->withErrors(['token' => 'No se pudo verificar el token.']);
    }

    $data = $response->json();

    if (!isset($data['status']) || $data['status'] !== 'success') {
        return redirect()->route('student.token.error')->withErrors(['token' => 'Token inválido o expirado.']);
    }

    $info = $data['data'];

    // Crear o actualizar estudiante
    $student = StudentRegistration::updateOrCreate(
        ['cedula' => $info['cedula']],
        [
            'names' => $info['nombre'],
            'correo_puce' => $info['usuario'] . '@puce.edu.ec',
            'facultad' => $info['facultad'] ?? '',
            'carrera' => $info['carrera'] ?? '',
            'nivel' => 'N/A',
            'nivel_instruccion' => 'grado',
            'beca_san_ignacio' => 'no',
            'forma_pago' => 'Efectivo',
            'edad' => 0,
            'fecha_nacimiento' => now(),
            'telefono' => '',
            'direccion' => '',
            'motivo' => '',
            'acepta_terminos' => false,
        ]
    );

    // Guardar sesión
    session([
    'student_logged_in' => true,
    'student_id' => $student->id,
    'student_data' => [
            'cedula' => $info['cedula'] ?? '',
            'nombre' => $info['nombre'] ?? '',
            'correo_puce' => ($info['usuario'] ?? '') . '@puce.edu.ec',
            'facultad' => $info['facultad'] ?? '',
            'carrera' => $info['carrera'] ?? '',
        ],
]);
    // Redirigir al formulario
    return redirect()->route('student.personal');
}

}
