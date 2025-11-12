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
   public function loginWithToken($token) // <-- CORRECCIÓN: Eliminado 'Request $request'
    {
        // 🔹 Llamada al servicio remoto (ajusta la URL si tu entorno cambia)
        $url = "https://www.puce.edu.ec/intranet/servicios/datos/turneros/token/{$token}";
        $response = Http::get($url);

        // --- CORRECCIÓN ---
        // Se eliminó el bloque $request->validate([...])
        // que causaba el bucle de redirección infinito.
        // La validación se debe hacer en el controlador que RECIBE el formulario.
        // --- FIN CORRECCIÓN ---

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
                'student_name' => $student->names, // Asegúrate de guardar el nombre también
            ]);

            // 🔹 Nueva ruta que mostrará solo el paso 5
            return redirect()
                ->route('student.agendamiento')
                ->with('info', 'Bienvenido nuevamente, por favor agende su cita.');
        }

        // --- CORRECCIÓN ---
        // Se eliminó el bloque StudentRegistration::create([...])
        // que causaba el error de 'edad' (ya que intentaba crear un usuario sin la edad).
        
        // 🔹 Si no existe, pre-llenamos el formulario 'student.personal'
        $dataFromToken = [
            'names' => $nombre,
            'cedula' => $cedula,
            'correo_puce' => $usuario ? "{$usuario}@puce.edu.ec" : null,
            'facultad' => $facultad,
            'carrera' => $carrera,
        ];

        // 🔹 Redirige a la vista de registro ('student.personal') con los datos
        // 'withInput' flashea los datos a la sesión para que la función old() los pueda usar.
        return redirect()->route('student.personal')
                        ->withInput($dataFromToken)
                        ->with('info', 'Por favor complete su registro para continuar.');
    }

}
