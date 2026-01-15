<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperatingAreaSeeder extends Seeder {
    public function run() {
        $areas = [
            ['id' => 11, 'faculty_id' => 1, 'name' => 'Facultad De Aprendizaje, Lenguas Y Comunicación'],
            ['id' => 21, 'faculty_id' => 2, 'name' => 'Facultad De Ciencias Exactas, Naturales Y Ambientales '],
            ['id' => 31, 'faculty_id' => 3, 'name' => 'Facultad De Ciencias Filosófico - Teológicas'],
            ['id' => 41, 'faculty_id' => 4, 'name' => 'Facultad De Derecho Y Sociedad'],
            ['id' => 51, 'faculty_id' => 5, 'name' => 'Facultad De Economía Y Gestión Empresarial'],
            ['id' => 61, 'faculty_id' => 6, 'name' => 'Facultad De Hábitat, Infraestructura Y Creatividad'],
            ['id' => 71, 'faculty_id' => 7, 'name' => 'Fisioterapia, Nutricion Y Terapia Física'],
            ['id' => 72, 'faculty_id' => 7, 'name' => 'Laboratorio Clínico, Enfermería, Medicina Veterinaria Y Bioquímica Clínica'],
            ['id' => 73, 'faculty_id' => 7, 'name' => 'Piscología Clínica, Psicología Educativa, Psicologia Organizacional, Psicología General'],
            ['id' => 74, 'faculty_id' => 7, 'name' => 'Medicina'],
            ['id' => 81, 'faculty_id' => 8, 'name' => 'PUCETEC'],
            ['id' => 91, 'faculty_id' => 9, 'name' => 'ICAM']
        ];
        DB::table('operating_areas')->insert($areas);
    }
}
