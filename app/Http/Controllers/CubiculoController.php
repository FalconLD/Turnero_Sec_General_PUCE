<?php

namespace App\Http\Controllers;

use App\Models\Cubiculo;
use Illuminate\Http\Request;
use App\Models\User;
class CubiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $cubiculos = Cubiculo::with('users')->get();
        return view('cubiculos.index', compact('cubiculos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $users = User::all();
        return view('cubiculos.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_atencion' => 'required|in:virtual,presencial',
            'user_id' => 'required|exists:users,id',
        ]);

        Cubiculo::create($request->all());

        return redirect()->route('cubiculos.index')->with('success', 'Cubículo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cubiculo $cubiculo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cubiculo $cubiculo)
    {
        //
        $users = User::all();
        return view('cubiculos.edit', compact('cubiculo', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cubiculo $cubiculo)
    {
        //
         $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_atencion' => 'required|in:virtual,presencial',
            'user_id' => 'required|exists:users,id',
        ]);

        $cubiculo->update($request->all());

        return redirect()->route('cubiculos.index')->with('success', 'Cubículo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cubiculo $cubiculo)
    {
        //
         $cubiculo->delete();
        return redirect()->route('cubiculos.index')->with('success', 'Cubículo eliminado correctamente.');
    }
}
