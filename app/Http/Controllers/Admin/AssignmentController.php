<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OperatingArea;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        // Cargamos los usuarios con sus áreas y la facultad de cada área
        $users = User::with('operatingAreas.faculty')->get();
        return view('admin.assignments.index', compact('users'));
    }

    public function edit(User $user)
    {
        // Cargamos todas las áreas con sus facultades para el formulario
        $areas = OperatingArea::with('faculty')->get();
        return view('admin.assignments.edit', compact('user', 'areas'));
    }

    public function update(Request $request, User $user)
    {
        // El método sync actualiza la tabla pivote area_user automáticamente
        // Si no se selecciona ninguna, enviamos un array vacío
        $user->operatingAreas()->sync($request->input('areas', []));

        return redirect()->route('assignments.index')
            ->with('success', "Áreas actualizadas para {$user->name}");
    }
}
