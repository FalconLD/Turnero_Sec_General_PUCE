<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Limpiar caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definición de Permisos
        $permissions = [
            // --- MÓDULO: ATENCIÓN ---
            'atencion.ver_calendario' => 'Ver el calendario de atención y cubículos',
            // 'atencion.procesar' => ELIMINADO PORQUE NO SE USA

            // --- MÓDULO: DESBLOQUEO ---
            'desbloqueo.acceder'      => 'Entrar al módulo de desbloqueo',
            'desbloqueo.ejecutar'     => 'Ejecutar la acción de desbloquear',

            // --- MÓDULO: ROLES ---
            'roles.ver'               => 'Ver lista de roles',
            'roles.crear'             => 'Crear nuevos roles',
            'roles.editar'            => 'Modificar permisos de roles',
            'roles.eliminar'          => 'Eliminar roles',

            // --- MÓDULO: PAGOS ---
            'pagos.ver'               => 'Ver dashboard y lista de pagos',
            'pagos.validar'           => 'Botón: Validar pago',
            'pagos.rechazar'          => 'Botón: Rechazar pago',

            // --- MÓDULO: USUARIOS ---
            'usuarios.ver'            => 'Ver lista de usuarios',
            'usuarios.crear'          => 'Crear nuevos usuarios',
            'usuarios.editar'         => 'Editar usuarios',
            'usuarios.eliminar'       => 'Eliminar usuarios',

            // --- MÓDULO: CUBÍCULOS ---
            'cubiculos.ver'           => 'Ver lista de cubículos',
            'cubiculos.crear'         => 'Crear nuevo cubículo',
            'cubiculos.editar'        => 'Editar cubículo',
            'cubiculos.eliminar'      => 'Eliminar cubículo',

            // --- MÓDULO: HORARIOS ---
            'horarios.ver'            => 'Ver configuraciones de horarios',
            'horarios.crear'          => 'Crear configuración horaria',
            'horarios.editar'         => 'Editar configuración',
            'horarios.eliminar'       => 'Eliminar configuración',
            
            // --- SUB-MÓDULO: TURNOS ---
            'horarios.gestionar_turnos' => 'Ver/Generar turnos específicos',
            'horarios.eliminar_turnos'  => 'Eliminar turnos individuales',

            // --- MÓDULO: PARÁMETROS ---
            'parametros.ver'          => 'Ver configuraciones globales',
            'parametros.editar'       => 'Modificar textos y variables',

            // --- MÓDULO: REPORTES ---
            'reportes.ver'            => 'Visualizar gráficos',
            'reportes.exportar'       => 'Botones de Exportar',
        ];

        // 3. Crear Permisos
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 4. Asignación de Roles

        // SUPER ADMIN
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // RECEPCIÓN
        $roleRecepcion = Role::firstOrCreate(['name' => 'Recepcion']);
        $roleRecepcion->givePermissionTo([
            'atencion.ver_calendario', 
            'pagos.ver',
            'pagos.validar',
            'pagos.rechazar',
            'desbloqueo.acceder',      
            'desbloqueo.ejecutar',
            'horarios.gestionar_turnos' 
        ]);

        // PSICÓLOGO
        $rolePsicologo = Role::firstOrCreate(['name' => 'Psicologo']);
        $rolePsicologo->givePermissionTo([
            'atencion.ver_calendario',
            'cubiculos.ver',
            'horarios.ver'
        ]);
    }
}