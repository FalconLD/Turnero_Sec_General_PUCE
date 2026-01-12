<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingArea;
use App\Models\Faculty;
use Illuminate\Http\Request;

class OperatingAreaController extends Controller
{
    /**
     * Muestra el listado de áreas de atención.
     */
    public function index()
    {
        // Cargamos la relación 'faculty' para mostrar el nombre de la facultad en la tabla
        $areas = OperatingArea::with('faculty')->get();
        return view('admin.operating_areas.index', compact('areas'));
    }

    /**
     * Muestra el formulario para crear una nueva área.
     */
    public function create()
    {
        // Necesitamos todas las facultades para el select del formulario
        $faculties = Faculty::orderBy('facultad', 'asc')->get();
        return view('admin.operating_areas.create', compact('faculties'));
    }

    /**
     * Almacena una nueva área en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'faculty_id'  => 'required|exists:faculties,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        OperatingArea::create($request->all());

        return redirect()->route('operating-areas.index')
            ->with('info', 'El área de atención se creó con éxito.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(OperatingArea $operatingArea)
    {
        $faculties = Faculty::orderBy('facultad', 'asc')->get();
        return view('admin.operating_areas.edit', compact('operatingArea', 'faculties'));
    }

    /**
     * Actualiza el área en la base de datos.
     */
    public function update(Request $request, OperatingArea $operatingArea)
    {
        $request->validate([
            'faculty_id'  => 'required|exists:faculties,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $operatingArea->update($request->all());

        return redirect()->route('operating-areas.index')
            ->with('info', 'Área de atención actualizada correctamente.');
    }

    /**
     * Elimina el área de la base de datos.
     */
    public function destroy(OperatingArea $operatingArea)
    {
        $operatingArea->delete();

        return redirect()->route('operating-areas.index')
            ->with('info', 'El área de atención ha sido eliminada.');
    }
}
