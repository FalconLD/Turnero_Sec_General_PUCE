<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el usuario logeado
use Illuminate\Support\Facades\Hash; // Si permites cambiar la contraseña

class ProfileController extends Controller
{
    /**
     * Muestra la vista del formulario de edición de perfil.
     */
    public function edit()
    {
        // Obtiene el usuario actualmente autenticado
        $user = Auth::user();

        // Retorna la vista y le pasa los datos del usuario
        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Actualiza la información del perfil del usuario.
     */
    public function update(Request $request)
    {
        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Validación de los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Para cambio de contraseña opcional
        ]);

        // Actualiza los datos
        $user->name = $request->name;
        $user->email = $request->email;

        // Actualiza la contraseña solo si se proporcionó una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Redirige de vuelta a la página de perfil con un mensaje de éxito
        return redirect()->route('profile.edit')->with('success', '¡Perfil actualizado con éxito!');
    }
}
