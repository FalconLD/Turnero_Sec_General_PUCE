<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;

// Comentado para evitar conflictos con CubiculoSeeder
// class ScheduleSeeder extends Seeder
// {
//     public function run(): void
//     {
//         $cubiculos = DB::table('cubiculos')->get();

//         // Definimos dos jornadas de ejemplo
//         $jornadas = [
//             ['inicio' => '08:00:00', 'fin' => '12:00:00'],
//             ['inicio' => '13:00:00', 'fin' => '17:00:00']
//         ];

//         foreach ($cubiculos as $index => $cubiculo) {
//             $scheduleId = (string) Str::uuid(); // Usamos UUID como en tu migración
//             $jornada = $jornadas[$index % 2]; // Alterna mañana/tarde

//             // 1. Crear el registro del Horario
//             DB::table('schedules')->insert([
//                 'id_hor' => $scheduleId,
//                 'start_time' => $jornada['inicio'],
//                 'end_time' => $jornada['fin'],
//                 'valid_from' => now()->startOfDay(),
//                 'break_minutes' => 10,
//                 'attention_minutes' => 20,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);

//             // 2. Crear los días de atención (Lunes a Viernes de esta semana)
//             $days = [];
//             for ($i = 0; $i < 5; $i++) {
//                 $days[] = [
//                     'schedule_day' => $scheduleId,
//                     'date_day' => now()->startOfWeek()->addDays($i)->format('Y-m-d'),
//                     'created_at' => now(),
//                     'updated_at' => now(),
//                 ];
//             }
//             DB::table('days')->insert($days);

//             // 3. Vincular este horario con el cubículo correspondiente
//             DB::table('cubiculos_schedules')->insert([
//                 'cubiculo_id' => $cubiculo->id,
//                 'schedule_id' => $scheduleId,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);
//         }
//     }
// }
