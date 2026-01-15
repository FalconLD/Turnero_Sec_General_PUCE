<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definir un ID único para el horario maestro
        $scheduleId = (string) Str::uuid();

        // 2. Crear el Horario Maestro (Ej: Jornada de Mañana)
        DB::table('schedules')->insert([
            'id_hor' => $scheduleId,
            'start_time' => '08:00:00',
            'end_time' => '13:00:00',
            'valid_from' => now()->startOfDay(), // Válido desde hoy
            'break_minutes' => 15, // Receso entre citas
            'attention_minutes' => 20, // Duración de cada atención virtual
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Asignar días específicos a este horario (Ej: Esta semana)
        $days = [];
        for ($i = 0; $i < 5; $i++) {
            $days[] = [
                'schedule_day' => $scheduleId,
                'date_day' => now()->addDays($i)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('days')->insert($days);

        // 4. Definir un receso obligatorio (Ej: Hora de café)
        DB::table('schedule_breaks')->insert([
            'schedule_id' => $scheduleId,
            'start_break' => '10:30:00',
            'end_break' => '11:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Vincular este horario a los cubículos virtuales que creamos
        // Asumiendo que los cubículos creados tienen IDs 1 y 2
        $cubicles = DB::table('cubiculos')->pluck('id');

        foreach ($cubicles as $id) {
            DB::table('cubiculos_schedules')->insert([
                'cubiculo_id' => $id,
                'schedule_id' => $scheduleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
