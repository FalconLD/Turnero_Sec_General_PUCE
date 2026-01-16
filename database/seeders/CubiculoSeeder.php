<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CubiculoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos todas las asignaciones de operadores a áreas para crear un cubículo por cada una
        $assignments = DB::table('area_user')->get();
        $data = [];
        $counter = 1;

        foreach ($assignments as $assignment) {

            $data[] = [
                // Mantenemos el formato "C -001"
                'nombre' => 'C -' . str_pad($counter, 3, '0', STR_PAD_LEFT),

                // Forzamos a que siempre sea virtual según tu requerimiento
                'tipo_atencion' => 'virtual',

                // Generamos un enlace de Teams único por cada cubículo
                'enlace_o_ubicacion' => 'https://teams.microsoft.com/l/meetup-join/example-session-' . $counter,

                'user_id' => $assignment->user_id,
                'operating_area_id' => $assignment->operating_area_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $counter++;
        }

        // Inserción masiva en la base de datos
        if (!empty($data)) {
            DB::table('cubiculos')->insert($data);
        }
    }
}
