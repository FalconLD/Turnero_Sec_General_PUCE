<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CubiculoSeeder extends Seeder
{
    public function run(): void
    {
        $cubiculos = [
            [
                'nombre' => 'Atención Virtual - Secretaría 1',
                'tipo_atencion' => 'virtual',
                'enlace_o_ubicacion' => 'https://teams.microsoft.com/l/meetup-join/ejemplo1',
                'user_id' => 3, // Belén Salazar
                'operating_area_id' => 11, // Área de Aprendizaje
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Atención Virtual - Secretaría 2',
                'tipo_atencion' => 'virtual',
                'enlace_o_ubicacion' => 'https://teams.microsoft.com/l/meetup-join/ejemplo2',
                'user_id' => 4, // Daniel Vinueza
                'operating_area_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cubiculos')->insert($cubiculos);
    }
}
