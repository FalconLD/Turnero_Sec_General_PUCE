<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Spatie\Permission\Models\Role;
    use Spatie\Permission\Models\Permission;

    class RoleController extends Controller
    {
        // 1. Listar los roles existentes
        public function index()
        {
            $roles = Role::all();
            return view('roles.index', compact('roles'));
        }

        // 2. Mostrar el formulario para EDITAR un rol (AQUÍ ESTÁ LA ESTRATEGIA UX)
        public function edit($id)
        {
            $role = Role::find($id);
            $permissions = Permission::all();

            // --- ESTRATEGIA MODULAR ---
            // Agrupamos los permisos por la palabra antes del punto.
            // Ejemplo: 'atencion.ver' y 'atencion.procesar' se guardan bajo el grupo 'atencion'
            
            $permissionGroups = $permissions->groupBy(function($perm) {
                // explode divide el string por el punto y toma la primera parte [0]
                return explode('.', $perm->name)[0]; 
            });

            return view('roles.edit', compact('role', 'permissionGroups'));
        }

        // 3. Guardar los cambios (Actualizar permisos)
        public function update(Request $request, $id)
        {
            $role = Role::find($id);

            // Sincronizamos: Borra los permisos viejos y pone los nuevos que vienen del formulario
            $role->syncPermissions($request->permissions);

            return redirect()->route('roles.index')->with('success', 'Permisos actualizados correctamente');
        }

            // 4. Mostrar formulario de CREACIÓN
        public function create()
        {
            $permissions = Permission::all();

            // Reutilizamos la ESTRATEGIA MODULAR para que la vista de crear sea igual de bonita
            $permissionGroups = $permissions->groupBy(function($perm) {
                return explode('.', $perm->name)[0]; 
            });

            return view('roles.create', compact('permissionGroups'));
        }

        // 5. Guardar el NUEVO rol en la BD
        public function store(Request $request)
        {
            // Validamos que el nombre sea obligatorio y único
            $request->validate([
                'name' => 'required|unique:roles,name',
            ], [
                'name.required' => 'El nombre del rol es obligatorio.',
                'name.unique' => 'Ya existe un rol con este nombre.'
            ]);

            // Creamos el rol
            $role = Role::create(['name' => $request->name]);

            // Asignamos los permisos seleccionados (si existen)
            if($request->has('permissions')){
                $role->syncPermissions($request->permissions);
            }

            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
        }

            // 6. Eliminar un rol existente
        public function destroy($id)
        {
            $role = Role::find($id);

            // --- SEGURIDAD CRÍTICA ---
            // Evitamos que se elimine el rol principal para no romper el sistema
            if ($role->name === 'Super Admin') {
                return redirect()->route('roles.index')
                    ->with('error', '¡No puedes eliminar el rol de Super Admin! Es fundamental para el sistema.');
            }

            // Validar si tiene usuarios asignados (Opcional, buena práctica UX)
            if ($role->users()->count() > 0) {
                return redirect()->route('roles.index')
                    ->with('error', 'No se puede eliminar el rol porque hay usuarios usándolo. Reasígnalos primero.');
            }

            $role->delete();

            return redirect()->route('roles.index')->with('success', 'El rol ha sido eliminado correctamente.');
        }
        
    }