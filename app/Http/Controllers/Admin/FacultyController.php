<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:facultades.ver')->only('index');
        $this->middleware('can:facultades.crear')->only(['create', 'store']);
        $this->middleware('can:facultades.editar')->only(['edit', 'update']);
        $this->middleware('can:facultades.eliminar')->only('destroy');
    }

    public function index()
    {
        $faculties = Faculty::all();
        return view('admin.faculties.index', compact('faculties'));
    }

    public function create()
    {
        return view('admin.faculties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'facultad' => 'required|string|max:255',
            'programa_desc' => 'required|string|max:255',
            'nivel' => 'required|string|max:100',
        ]);

        Faculty::create($request->all());

        return redirect()->route('faculties.index')
            ->with('info', 'La facultad se ha registrado con éxito.');
    }

    public function edit(Faculty $faculty)
    {
        return view('admin.faculties.edit', compact('faculty'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'facultad' => 'required|string|max:255',
            'programa_desc' => 'required|string|max:255',
            'nivel' => 'required|string|max:100',
        ]);

        $faculty->update($request->all());

        return redirect()->route('faculties.index')
            ->with('info', 'Facultad actualizada correctamente.');
    }

    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        return redirect()->route('faculties.index')
            ->with('info', 'Facultad eliminada con éxito.');
    }
}
