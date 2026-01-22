<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CubiculoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza de seguridad
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('shifts')->truncate();
        DB::table('schedule_breaks')->truncate();
        DB::table('schedules')->truncate();
        DB::table('cubiculos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Obtener todas las asignaciones de usuarios a áreas operativas
        $assignments = DB::table('area_user')->get();

        foreach ($assignments as $index => $assignment) {
            
            // 1. CREAR EL CUBÍCULO
            $cubiculoId = DB::table('cubiculos')->insertGetId([
                'nombre' => 'C-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'tipo_atencion' => 'virtual',
                'enlace_o_ubicacion' => 'https://teams.microsoft.com/l/meetup-join/puce-session-' . ($index + 1),
                'user_id' => $assignment->user_id,
                'operating_area_id' => $assignment->operating_area_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generar ID manual para el schedule
            $scheduleId = (DB::table('schedules')->max('id_hor') ?? 0) + 1 + $index;

            DB::table('schedules')->insert([
                'id_hor' => $scheduleId, // ← AGREGAR ESTA LÍNEA
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'valid_from' => Carbon::today()->format('Y-m-d'),
                'break_minutes' => 0,
                'attention_minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. CREAR BREAK DE ALMUERZO (13:00 - 14:00)
            DB::table('schedule_breaks')->insert([
                'schedule_id' => $scheduleId,
                'start_break' => '13:00:00',
                'end_break' => '14:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // 3.5 CREAR DÍAS PARA EL HORARIO (Lunes a Viernes para los próximos 10 días)
            $fechaInicio = Carbon::today();
            for ($dia = 0; $dia < 10; $dia++) {
                $fechaActual = $fechaInicio->copy()->addDays($dia);
                
                // Solo días laborables
                if (!$fechaActual->isWeekend()) {
                    DB::table('days')->insert([
                        'schedule_day' => $scheduleId,
                        'date_day' => $fechaActual->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 4. VINCULAR CUBÍCULOS CON EL HORARIO
            DB::table('cubiculos_schedules')->insert([
                'cubiculo_id' => $cubiculoId,
                'schedule_id' => $scheduleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. GENERAR TURNOS BASADOS EN EL SCHEDULE
            $this->generarTurnosDesdeSchedule($cubiculoId, $scheduleId);
        }

        $this->command->info('✅ Cubículos, horarios y turnos generados exitosamente.');
    }

    /**
     * Genera turnos para un cubículo basándose en su configuración de Schedule
     */
    private function generarTurnosDesdeSchedule($cubiculoId, $scheduleId)
    {
        // Obtener configuración del horario
        $schedule = DB::table('schedules')->where('id_hor', $scheduleId)->first();
        
        if (!$schedule) {
            $this->command->warn("⚠️ No se encontró schedule ID: {$scheduleId}");
            return;
        }

        // Obtener breaks configurados
        $breaks = DB::table('schedule_breaks')
            ->where('schedule_id', $scheduleId)
            ->get();

        $fechaInicio = Carbon::parse($schedule->valid_from);
        $duracionTurno = (int) $schedule->attention_minutes;

        // Generar turnos para los próximos 10 días
        for ($dia = 0; $dia < 10; $dia++) {
            $fechaActual = $fechaInicio->copy()->addDays($dia);

            // Saltar fines de semana
            if ($fechaActual->isWeekend()) {
                continue;
            }

            // Generar turnos para este día
            $this->generarTurnosDelDia(
                $cubiculoId,
                $scheduleId, // ← AGREGAR ESTE PARÁMETRO
                $fechaActual,
                $schedule->start_time,
                $schedule->end_time,
                $duracionTurno,
                $breaks
            );
        }
    }

    /**
     * Genera los turnos de un día específico respetando los breaks
     */
    private function generarTurnosDelDia($cubiculoId, $scheduleId, $fecha, $horaInicio, $horaFin, $duracionMinutos, $breaks)

    {
        $horaActual = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaInicio);
        $horaLimite = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaFin);

        while ($horaActual->lt($horaLimite)) {
            $horaFinTurno = $horaActual->copy()->addMinutes($duracionMinutos);

            // Verificar si el turno cae dentro de un break
            $estaEnBreak = false;
            foreach ($breaks as $break) {
                $inicioBreak = Carbon::parse($fecha->format('Y-m-d') . ' ' . $break->start_break);
                $finBreak = Carbon::parse($fecha->format('Y-m-d') . ' ' . $break->end_break);

                // Si el turno inicia o termina durante el break, saltarlo
                if ($horaActual->between($inicioBreak, $finBreak->subSecond()) || 
                    $horaFinTurno->between($inicioBreak->addSecond(), $finBreak)) {
                    $estaEnBreak = true;
                    break;
                }
            }

            // Si NO está en break, crear el turno
            if (!$estaEnBreak && $horaFinTurno->lte($horaLimite)) {
                DB::table('shifts')->insert([
                    'id_shift' => (string) Str::uuid(),
                    'schedule_shift' => $scheduleId, // ← AGREGAR ESTA LÍNEA
                    'cubicle_shift' => $cubiculoId,
                    'date_shift' => $fecha->format('Y-m-d'),
                    'start_shift' => $horaActual->format('H:i:s'),
                    'end_shift' => $horaFinTurno->format('H:i:s'),
                    'person_shift' => null,
                    'status_shift' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Avanzar al siguiente turno
            $horaActual->addMinutes($duracionMinutos);

            // Si estamos en un break, saltar al final del break
            foreach ($breaks as $break) {
                $inicioBreak = Carbon::parse($fecha->format('Y-m-d') . ' ' . $break->start_break);
                $finBreak = Carbon::parse($fecha->format('Y-m-d') . ' ' . $break->end_break);

                if ($horaActual->between($inicioBreak, $finBreak)) {
                    $horaActual = $finBreak->copy();
                }
            }
        }
    }
}