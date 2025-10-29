<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentRegistration;
use App\Models\Parameter; // Donde está el parámetro TERM
use App\Models\Shift;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentRegistered;

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
        // Ya no hace falta validar session('accepted_terms')
        // porque el usuario acepta los términos dentro del mismo formulario.

        $terminos = Parameter::where('clave', 'TERM')->first();

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
        'turno_id' => 'required|exists:shifts,id_shift',
    ]);

    
    $comprobantePath = null;
    if ($request->hasFile('comprobante')) {
        $comprobantePath = $request->file('comprobante')->store('public/comprobantes');
        $comprobantePath = str_replace('public/', '', $comprobantePath);
    }

    
    $valor = ($request->nivel_instruccion === 'grado')
                ? ($request->beca_san_ignacio === 'si' ? 0.50 : 2.50)
                : 7.50;

    // Guardar registro del estudiante
    $student = StudentRegistration::create([
        'names' => $request->names,
        'cedula' => $request->cedula,
        'edad' => $request->edad,
        'fecha_nacimiento' => $request->fecha_nacimiento,
        'telefono' => $request->telefono,
        'direccion' => $request->direccion,
        'correo_puce' => $request->correo_puce,
        'facultad' => $request->facultad,
        'carrera' => $request->carrera,
        'nivel' => $request->nivel,
        'motivo' => $request->motivo,
        'nivel_instruccion' => $request->nivel_instruccion,
        'beca_san_ignacio' => $request->beca_san_ignacio,
        'forma_pago' => $request->forma_pago,
        'valor_pagar' => $valor,
        'acepta_terminos' => true,
        'comprobante' => $comprobantePath
    ]);

   
    $turno = Shift::find($request->turno_id);
    $turno->person_shift = $student->cedula;
    $turno->status_shift = 0;
    $turno->save();


    
    Mail::to($student->correo_puce)->send(new StudentRegistered($student, $turno));

    return redirect()->route('student.success')->with('success', 'Registro y turno guardados correctamente.');
}

}
