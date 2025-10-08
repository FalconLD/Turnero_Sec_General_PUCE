<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    public function index()
    {
        $parameters = Parameter::all();
        return view('parameters.index', compact('parameters'));
    }

    public function create()
    {
        return view('parameters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'parametro' => 'required|string|max:255',
        ]);

        Parameter::create($request->all());

        return redirect()->route('parameters.index')->with('success', 'Parámetro creado correctamente.');
    }
        public function destroy($id)
    {
        $parameter = Parameter::findOrFail($id);
        $parameter->delete();

        return redirect()->route('parameters.index')->with('success', 'Parámetro eliminado correctamente.');
    }
    // En ParameterController.php

    public function edit($id)
    {
        $parameter = Parameter::findOrFail($id);
        return view('parameters.edit', compact('parameter'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'clave' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'parametro' => 'required|string|max:255',
        ]);

        $parameter = Parameter::findOrFail($id);
        $parameter->update($request->all());

        return redirect()->route('parameters.index')->with('success', 'Parámetro actualizado correctamente.');
    }

}
