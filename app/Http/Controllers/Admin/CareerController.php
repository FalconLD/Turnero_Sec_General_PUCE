<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\OperatingArea;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:carreras.ver')->only('index');
        $this->middleware('can:carreras.crear')->only(['create', 'store']);
        $this->middleware('can:carreras.editar')->only(['edit', 'update']);
        $this->middleware('can:carreras.eliminar')->only('destroy');
    }
    public function index()
    {
        // Cargamos la relación para mostrar el nombre del área en la tabla
        $careers = Career::with('operatingArea')->get();
        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        $areas = OperatingArea::orderBy('name', 'asc')->get();
        return view('admin.careers.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'career_code'       => 'nullable|string|max:100', // Permitimos null por los "No existe programa"
            'name'              => 'required|string|max:255',
            'operating_area_id' => 'required|exists:operating_areas,id',
        ]);

        Career::create($request->all());

        return redirect()->route('careers.index')
            ->with('info', 'La carrera se ha registrado correctamente.');
    }

    public function edit(Career $career)
    {
        $areas = OperatingArea::orderBy('name', 'asc')->get();
        return view('admin.careers.edit', compact('career', 'areas'));
    }

    public function update(Request $request, Career $career)
    {
        $request->validate([
            'career_code'       => 'nullable|string|max:100',
            'name'              => 'required|string|max:255',
            'operating_area_id' => 'required|exists:operating_areas,id',
        ]);

        $career->update($request->all());

        return redirect()->route('careers.index')
            ->with('info', 'Carrera actualizada con éxito.');
    }

    public function destroy(Career $career)
    {
        $career->delete();
        return redirect()->route('careers.index')
            ->with('info', 'Carrera eliminada.');
    }
}
