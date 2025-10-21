<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentRegistration;
use App\Models\Parameter; // Donde está el parámetro TERM

class StudentRegistrationController extends Controller
{
    // Paso 1: Términos
    public function showTerms()
    {
        $terminos = Parameter::where('clave', 'TERM')->first();

        return view('student.terms', compact('terminos'));
    }

    // Validar aceptación de términos
    public function acceptTerms(Request $request)
    {
        $request->validate([
            'acepta_terminos' => 'accepted',
        ]);

        // Guardar en sesión que aceptó
        session(['accepted_terms' => true]);

        return redirect()->route('student.personal');
    }

    // Paso 2: Datos personales
    public function showPersonalForm()
    {
        if (!session('accepted_terms')) {
            return redirect()->route('student.terms');
        }

        $terminos = Parameter::where('clave', 'TERM')->first();

        return view('student.personal_data', compact('terminos'));
    }


    // Guardar datos personales
    public function store(Request $request)
    {
        $request->validate([
            'names' => 'required|string|max:255',
            'cedula' => 'required|string|max:20',
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

        $valor = 0;
        if ($request->nivel_instruccion == 'grado') {
            $valor = $request->beca_san_ignacio == 'si' ? 0.50 : 2.50;
        } else {
            $valor = 7.50;
        }

        StudentRegistration::create(array_merge(
            $request->all(),
            ['valor_pagar' => $valor, 'acepta_terminos' => true]
        ));

        return redirect()->route('student.success')->with('success', 'Registro guardado correctamente.');
    }

    public function success()
    {
        return view('student.success');
    }
}
