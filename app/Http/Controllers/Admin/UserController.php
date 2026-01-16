<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:usuarios.ver')->only('index');
        $this->middleware('can:usuarios.crear')->only(['create', 'store']);
        $this->middleware('can:usuarios.editar')->only(['edit', 'update']);
        $this->middleware('can:usuarios.eliminar')->only('destroy');
    }
    public function index()
    {
        $users = User::with('cubiculos', 'roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'DNI' => 'nullable|string|max:20|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'DNI' => $request->DNI,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'DNI' => 'nullable|string|max:20|unique:users,DNI,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'DNI' => $request->DNI,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // 6. Actualizamos el rol si viene en la petición
        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
