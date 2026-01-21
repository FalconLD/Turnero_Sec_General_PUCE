<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserta estudiantes de prueba sin campos de pago.
     */
    public function run(): void
    {
        $students = [
            [
                'names' => 'Juan Alberto Pérez',
                'cedula' => '1724990922',
                'edad' => 21,
                'fecha_nacimiento' => '2005-03-15',
                'telefono' => '0987654321',
                'direccion' => 'Av. 12 de Octubre y Patria',
                'correo_puce' => 'japerez@puce.edu.ec',
                'facultad' => 'FACULTAD DE HÁBITAT, INFRAESTRUCTURA Y CREATIVIDAD',
                'carrera' => 'Arquitectura',
                'nivel' => '5to Semestre',
                'plan_estudio' => 'Plan 2022',
                'motivo' => 'Consulta sobre requisitos de titulación.',
                'nivel_instruccion' => 'grado',
                'tomado' => 0,
                'acepta_terminos' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'names' => 'María Belén Rodríguez',
                'cedula' => '1725080673',
                'edad' => 24,
                'fecha_nacimiento' => '2002-11-20',
                'telefono' => '0995554433',
                'direccion' => 'Nayón, Calle de los Olivos',
                'correo_puce' => 'mbrodriguez@puce.edu.ec',
                'facultad' => 'FACULTAD DE ECONOMÍA Y GESTIÓN EMPRESARIAL',
                'carrera' => 'Administración de Empresas',
                'nivel' => '9no Semestre',
                'plan_estudio' => 'Plan 2018',
                'motivo' => 'Solicitud de certificado de notas virtual.',
                'nivel_instruccion' => 'grado',
                'acepta_terminos' => true,
                'tomado' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('student_registrations')->insert($students);
    }
}
