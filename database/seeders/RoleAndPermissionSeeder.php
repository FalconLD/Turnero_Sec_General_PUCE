<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Limpiar la caché de permisos de Spatie para evitar conflictos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definición Maestra de Recursos y sus Acciones CRUD
        $resources = [
            'facultades'   => ['ver', 'crear', 'editar', 'eliminar'],
            'carreras'     => ['ver', 'crear', 'editar', 'eliminar'],
            'areas'        => ['ver', 'crear', 'editar', 'eliminar'],
            'usuarios'     => ['ver', 'crear', 'editar', 'eliminar'],
            'asignaciones' => ['ver', 'crear', 'editar', 'eliminar'],
            'cubiculos'    => ['ver', 'crear', 'editar', 'eliminar'],
            'horarios'     => ['ver', 'crear', 'editar', 'eliminar'],
            'roles'        => ['ver', 'crear', 'editar', 'eliminar'],
            'reportes'     => ['ver', 'exportar'],
            'desbloqueo'   => ['ver', 'ejecutar'],
            'atencion'     => ['ver_calendario'],
        ];

        // Crear cada permiso en la base de datos
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$resource.$action"]);
            }
        }

        // 2. Definición de Roles Principales

        // SUPER ADMIN: Obtiene el llavero maestro con todos los permisos
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // OPERADOR: Obtiene solo los permisos necesarios para la atención diaria
        $roleOperador = Role::firstOrCreate(['name' => 'Operador']);
        $roleOperador->syncPermissions([
            'atencion.ver_calendario',
            'horarios.ver',
            'desbloqueo.ver',
        ]);
    }
}
