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
                'banner_id'    => $info['idbanner'], 
                'plan_estudio' => $info['plan_estudio'] ?? null, 
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

    
    // public function loginWithToken($token)
    // {
        
    // // // 🔧 MODO PRUEBA
    // session([
    //     'student_logged_in' => true,
    //     'student_id' => 1,
    //     'student_cedula' => '0102030405',
    //     'student_name' => 'Estudiante Prueba',
    //     'correo_puce' => 'estudiantePrueba@puce.edu.ec',
    // ]);

    // return redirect()->route('student.personal');


        // URL del servicio remoto
public function loginWithToken($token)
{
    // 1. URL del servicio remoto (Ya configurada para la PUCE)
    $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/{$token}";

    $response = Http::get($url);

    // Verificamos si la respuesta falló o si el status no es 'success'
    if ($response->failed() || $response->json('status') !== 'success') {
        return redirect()->route('student.token.error')
            ->withErrors(['error' => 'Token inválido o expirado.']);
    }

    // 2. Extraer datos reales del JSON (Mapeo completo)
    $data = $response->json('data');

    $cedula      = $data['cedula'] ?? null;
    $nombre      = $data['nombre'] ?? null;
    $usuario     = $data['usuario'] ?? null;
    $idBanner    = $data['idbanner'] ?? null;      // <--- Dato real para banner_id
    $planEstudio = $data['plan_estudio'] ?? null; // <--- Dato real para plan_estudio
    $facultad    = $data['facultad'] ?? null;
    $carrera     = $data['carrera'] ?? null;

    if (!$cedula) {
        return redirect()->route('student.token.error')
            ->withErrors(['error' => 'El token no contiene una cédula válida.']);
    }

    // 3. CREAR O ACTUALIZAR EL REGISTRO EN LA BASE DE DATOS
    // Esto asegura que el banner_id se guarde físicamente en tu tabla
    $student = StudentRegistration::updateOrCreate(
        ['cedula' => $cedula], // Buscamos por cédula para evitar duplicados
        [
            'names'        => $nombre,
            'banner_id'    => $idBanner,    // <--- Guardamos P00016545
            'plan_estudio' => $planEstudio, // <--- Guardamos Q096
            'correo_puce'  => $usuario ? "{$usuario}@puce.edu.ec" : null,
            'facultad'     => $facultad,
            'carrera'      => $carrera,
            'acepta_terminos' => false, // Valor inicial por defecto
            'edad'              => null, 
            'nivel'             => null,
            'nivel_instruccion' => null,
            'beca_san_ignacio'  => null,
            'forma_pago'        => null,
            'fecha_nacimiento'  => null,
            'telefono'          => null,
            'direccion'         => null,
            'motivo'            => null,
            'acepta_terminos'   => false,
        ]
    );

    // 4. Guardar datos en la sesión para el flujo del frontend
    // Usamos el ID real de la base de datos ($student->id)
    session([
        'student_logged_in' => true,
        'student_id'        => $student->id,
        'student_cedula'    => $student->cedula,
        'student_name'      => $student->names,
        'student_plan_estudio' => $planEstudio,
    ]);

    // 5. Redirigir al primer paso del formulario de datos personales
    return redirect()->route('student.personal')
        ->with('info', 'Bienvenido, tus datos académicos han sido cargados automáticamente.');
}
}