<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Muestra la vista del formulario de edición de perfil.
     */
    public function edit(Request $request) // <-- Inyectamos Request
    {
        // CAMBIO AQUÍ:
        // En lugar de Auth::user(), usamos $request->user()
        // El editor sabe que esto no puede ser nulo aquí.
        $user = $request->user();

        // Retorna la vista y le pasa los datos del usuario
        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Actualiza la información del perfil del usuario.
     */
    public function update(Request $request)
    {
        // CAMBIO AQUÍ:
        // Obtenemos el usuario del request. ¡La advertencia desaparecerá!
        $user = $request->user();

        // Validación de los datos
        $request->validate([
            'name' => 'required|string|max:255',
            // El email es 'readonly', pero lo validamos por seguridad
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Actualiza los datos (el email no cambiará porque es readonly)
        $user->name = $request->name;
        $user->email = $request->email; 

        // Actualiza la contraseña solo si se proporcionó una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // <-- La advertencia en esta línea desaparecerá.

        // Redirige de vuelta a la página de perfil con un mensaje de éxito
        return redirect()->route('profile.edit')->with('success', '¡Perfil actualizado con éxito!');
    }
}