<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use Illuminate\Http\Request;
use App\Models\Cubiculo;
use App\Models\Form;
use Illuminate\Support\Carbon; 

class AsignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $asignaciones = Asignacion::with(['cubiculo', 'form'])->get();
        return view('asignacion.index', compact('asignaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cubiculos = Cubiculo::all();
        $forms = Form::all();
        return view('asignacion.create', compact('cubiculos', 'forms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'cubiculo_id' => 'required|exists:cubiculos,id',
            'form_id' => 'required|exists:forms,id',
        ]);

        Asignacion::create($request->all());

        return redirect()->route('asignacion.index')->with('success', 'Asignación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asignacion $asignacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asignacion $asignacion)
    {
        $cubiculos = Cubiculo::all();
        $forms = Form::all();

        return view('asignacion.edit', compact('asignacion', 'cubiculos', 'forms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asignacion $asignacion)
    {
        $request->validate([
            'cubiculo_id' => 'required|exists:cubiculos,id',
            'form_id' => 'required|exists:forms,id',
            // ya no validas fecha_actualizacion aquí porque no viene del formulario
        ]);

        $data = $request->all();

        // Asignar fecha_actualizacion con la fecha y hora actual
        $data['fecha_actualizacion'] = Carbon::now();

        $asignacion->update($data);

        return redirect()->route('asignacion.index')->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asignacion $asignacion)
    {
        $asignacion->delete();
        return redirect()->route('asignacion.index')->with('success', 'Asignación eliminada correctamente.');
    }
}
