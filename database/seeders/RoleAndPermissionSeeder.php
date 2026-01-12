<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Limpiar caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definición de Permisos
        $permissions = [
            'atencion.ver_calendario', 'desbloqueo.acceder', 'desbloqueo.ejecutar',
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
            'pagos.ver', 'pagos.validar', 'pagos.rechazar',
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'cubiculos.ver', 'cubiculos.crear', 'cubiculos.editar', 'cubiculos.eliminar',
            'horarios.ver', 'horarios.crear', 'horarios.editar', 'horarios.eliminar',
            'horarios.gestionar_turnos', 'horarios.eliminar_turnos',
            'parametros.ver', 'parametros.editar',
            'reportes.ver', 'reportes.exportar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Crear Roles y Asignar Permisos

        // SUPER ADMIN: Todo
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // RECEPCIÓN
        $roleRecepcion = Role::firstOrCreate(['name' => 'Recepcion']);
        $roleRecepcion->syncPermissions([
            'atencion.ver_calendario', 'pagos.ver', 'pagos.validar', 'pagos.rechazar',
            'desbloqueo.acceder', 'desbloqueo.ejecutar', 'horarios.gestionar_turnos'
        ]);

        // OPERADOR
        $roleOperador = Role::firstOrCreate(['name' => 'Operador']);
        $roleOperador->syncPermissions([
            'atencion.ver_calendario',
            'horarios.ver' // Permiso básico para ver sus horarios asignados
        ]);
    }
}
