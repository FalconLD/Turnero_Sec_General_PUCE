<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultySeeder extends Seeder {
    public function run() {
        $faculties = [
            ['id' => 1, 'facultad' => 'FACULTAD DE APRENDIZAJE, LENGUAS Y COMUNICACIÓN', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 2, 'facultad' => 'FACULTAD DE CIENCIAS EXACTAS, NATURALES Y AMBIENTALES ', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 3, 'facultad' => 'FACULTAD DE CIENCIAS FILOSÓFICO - TEOLÓGICAS', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 4, 'facultad' => 'FACULTAD DE DERECHO Y SOCIEDAD', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 5, 'facultad' => 'FACULTAD DE ECONOMÍA Y GESTIÓN EMPRESARIAL', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 6, 'facultad' => 'FACULTAD DE HÁBITAT, INFRAESTRUCTURA Y CREATIVIDAD', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 7, 'facultad' => 'FACULTAD DE SALUD Y BIENESTAR', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 8, 'facultad' => 'PUCETEC', 'programa_desc' => 'N/A', 'nivel' => 'N/A'],
            ['id' => 9, 'facultad' => 'ICAM', 'programa_desc' => 'N/A', 'nivel' => 'N/A']
        ];
        DB::table('faculties')->insert($faculties);
    }
}
