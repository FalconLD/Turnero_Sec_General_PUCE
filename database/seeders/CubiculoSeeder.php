<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CubiculoSeeder extends Seeder
{
    private $chunkSize = 100;
    private $diasGenerar = 10;
    private $now;

    public function run(): void
    {
        $this->now = now();
        $startTime = microtime(true);

        $this->command->info('🚀 Iniciando seeder');

        // Limpieza de seguridad
        $this->truncarTablas();

        // Obtener todas las asignaciones
        $assignments = DB::table('area_user')->get();
        $totalAssignments = count($assignments);

        $this->command->info("📊 Procesando {$totalAssignments} asignaciones...");

        // Arrays para acumulación masiva
        $allCubiculos = [];
        $allSchedules = [];
        $allBreaks = [];
        $allDays = [];
        $allCubiculoSchedules = [];
        $allShifts = [];

        // Contador para IDs
        $scheduleIdCounter = DB::table('schedules')->max('id_hor') ?? 0;

        foreach ($assignments as $index => $assignment) {
            $currentIndex = $index + 1;
            $cubiculoId = $currentIndex; // Asumimos que ID será autoincremental

            // 1. Acumular datos de cubículos
            $allCubiculos[] = [
                'nombre' => 'C-' . str_pad($currentIndex, 3, '0', STR_PAD_LEFT),
                'tipo_atencion' => 'virtual',
                'enlace_o_ubicacion' => 'https://teams.microsoft.com/l/meetup-join/puce-session-' . $currentIndex,
                'user_id' => $assignment->user_id,
                'operating_area_id' => $assignment->operating_area_id,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            // 2. Acumular schedules
            $scheduleId = ++$scheduleIdCounter;
            $allSchedules[] = [
                'id_hor' => $scheduleId,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'valid_from' => Carbon::today()->format('Y-m-d'),
                'break_minutes' => 0,
                'attention_minutes' => 15,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            // 3. Acumular breaks
            $allBreaks[] = [
                'schedule_id' => $scheduleId,
                'start_break' => '13:00:00',
                'end_break' => '14:00:00',
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            // 4. Generar días para este schedule (optimizado)
            $daysForThisSchedule = $this->generarDiasParaSchedule($scheduleId);
            $allDays = array_merge($allDays, $daysForThisSchedule);

            // 5. Acumular relación cubiculo-schedule
            $allCubiculoSchedules[] = [
                'cubiculo_id' => $cubiculoId,
                'schedule_id' => $scheduleId,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            // 6. Generar turnos para este cubiculo (optimizado)
            $shiftsForThisCubiculo = $this->generarTurnosOptimizado(
                $cubiculoId,
                $scheduleId,
                '08:00:00',
                '17:00:00',
                15,
                [['start' => '13:00:00', 'end' => '14:00:00']]
            );
            $allShifts = array_merge($allShifts, $shiftsForThisCubiculo);

            // Mostrar progreso cada 10 asignaciones
            if (($currentIndex % 10) === 0) {
                $this->command->info("📈 Procesadas {$currentIndex}/{$totalAssignments} asignaciones...");
            }
        }

        // 📦 INSERTAR EN LOTE TODO
        $this->command->info("🗃️ Insertando datos acumulados...");

        // Insertar cubículos
        $this->insertarEnLote('cubiculos', $allCubiculos, 'Cubículos');

        // Insertar schedules
        $this->insertarEnLote('schedules', $allSchedules, 'Schedules');

        // Insertar breaks
        $this->insertarEnLote('schedule_breaks', $allBreaks, 'Breaks');

        // Insertar días (pueden ser muchos)
        $this->insertarEnLote('days', $allDays, 'Días', 200);

        // Insertar relaciones cubiculo-schedule
        $this->insertarEnLote('cubiculos_schedules', $allCubiculoSchedules, 'Relaciones');

        // Insertar turnos (MUCHOS registros)
        $this->insertarEnLote('shifts', $allShifts, 'Turnos', 200);

        $endTime = microtime(true);
        $elapsed = round($endTime - $startTime, 2);

        $this->command->info("✅ Seeder completado en {$elapsed} segundos");
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   - Cubículos: " . count($allCubiculos));
        $this->command->info("   - Schedules: " . count($allSchedules));
        $this->command->info("   - Turnos: " . count($allShifts));
        $this->command->info("   - Días: " . count($allDays));
    }

    private function truncarTablas(): void
    {
        $this->command->info('🧹 Limpiando tablas...');

        // Desactivar foreign key checks para truncado rápido
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Lista de tablas a truncar en orden inverso de dependencias
        $tablas = [
            'shifts',
            'schedule_breaks',
            'cubiculos_schedules',
            'days',
            'schedules',
            'cubiculos',
        ];

        foreach ($tablas as $tabla) {
            DB::table($tabla)->truncate();
            $this->command->info("   - {$tabla} truncada");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function generarDiasParaSchedule(int $scheduleId): array
    {
        $days = [];
        $fechaInicio = Carbon::today();

        for ($dia = 0; $dia < $this->diasGenerar; $dia++) {
            $fechaActual = $fechaInicio->copy()->addDays($dia);

            if (!$fechaActual->isWeekend()) {
                $days[] = [
                    'schedule_day' => $scheduleId,
                    'date_day' => $fechaActual->format('Y-m-d'),
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
            }
        }

        return $days;
    }

    private function generarTurnosOptimizado(
        int $cubiculoId,
        int $scheduleId,
        string $horaInicio,
        string $horaFin,
        int $duracionMinutos,
        array $breaks
    ): array {
        $shifts = [];
        $fechaInicio = Carbon::today();

        // Pre-calcular breaks como objetos Carbon para eficiencia
        $breakObjects = [];
        foreach ($breaks as $break) {
            $breakObjects[] = [
                'start' => Carbon::parse($break['start']),
                'end' => Carbon::parse($break['end']),
            ];
        }

        // Generar para los próximos días
        for ($dia = 0; $dia < $this->diasGenerar; $dia++) {
            $fechaActual = $fechaInicio->copy()->addDays($dia);

            if ($fechaActual->isWeekend()) {
                continue;
            }

            $horaActual = Carbon::parse($fechaActual->format('Y-m-d') . ' ' . $horaInicio);
            $horaLimite = Carbon::parse($fechaActual->format('Y-m-d') . ' ' . $horaFin);

            while ($horaActual->lt($horaLimite)) {
                $horaFinTurno = $horaActual->copy()->addMinutes($duracionMinutos);

                // Verificar breaks
                $enBreak = false;
                foreach ($breakObjects as $break) {
                    $breakStart = $fechaActual->copy()->setTime(
                        $break['start']->hour,
                        $break['start']->minute,
                        $break['start']->second
                    );

                    $breakEnd = $fechaActual->copy()->setTime(
                        $break['end']->hour,
                        $break['end']->minute,
                        $break['end']->second
                    );

                    // ✅ CORRECCIÓN: Permitir turnos que terminan EXACTAMENTE al inicio del break
                    // y turnos que empiezan EXACTAMENTE al final del break
                    $dentroDelBreak = false;

                    // Si el turno está completamente DENTRO del break (no permitir)
                    if ($horaActual->gte($breakStart) && $horaFinTurno->lte($breakEnd)) {
                        $dentroDelBreak = true;
                    }
                    // Si el turno empieza DENTRO del break (excepto al final exacto)
                    else if ($horaActual->gt($breakStart) && $horaActual->lt($breakEnd)) {
                        $dentroDelBreak = true;
                    }
                    // Si el turno termina DENTRO del break (excepto al inicio exacto)
                    else if ($horaFinTurno->gt($breakStart) && $horaFinTurno->lt($breakEnd)) {
                        $dentroDelBreak = true;
                    }
                    // Si el turno cubre completamente el break
                    else if ($horaActual->lt($breakStart) && $horaFinTurno->gt($breakEnd)) {
                        $dentroDelBreak = true;
                    }

                    if ($dentroDelBreak) {
                        $enBreak = true;
                        break;
                    }
                }

                if (!$enBreak && $horaFinTurno->lte($horaLimite)) {
                    $shifts[] = [
                        'id_shift' => Str::uuid()->toString(),
                        'schedule_shift' => $scheduleId,
                        'cubicle_shift' => $cubiculoId,
                        'date_shift' => $fechaActual->format('Y-m-d'),
                        'start_shift' => $horaActual->format('H:i:s'),
                        'end_shift' => $horaFinTurno->format('H:i:s'),
                        'person_shift' => null,
                        'status_shift' => 1,
                        'created_at' => $this->now,
                        'updated_at' => $this->now,
                    ];
                }

                $horaActual->addMinutes($duracionMinutos);

                // ✅ CORRECCIÓN: Saltar breaks solo si la hora actual está dentro del break
                foreach ($breakObjects as $break) {
                    $breakStart = $fechaActual->copy()->setTime(
                        $break['start']->hour,
                        $break['start']->minute,
                        $break['start']->second
                    );

                    $breakEnd = $fechaActual->copy()->setTime(
                        $break['end']->hour,
                        $break['end']->minute,
                        $break['end']->second
                    );

                    // Si estamos dentro del break (excluyendo el inicio exacto)
                    if ($horaActual->gt($breakStart) && $horaActual->lt($breakEnd)) {
                        $horaActual = $breakEnd->copy();
                    }
                }
            }
        }

        return $shifts;
    }

    private function insertarEnLote(string $tabla, array $datos, string $nombre, int $chunkSize = null): void
    {
        $chunkSize = $chunkSize ?: $this->chunkSize;

        if (empty($datos)) {
            $this->command->info("   ⚠️  No hay datos para {$nombre}");
            return;
        }

        $total = count($datos);
        $this->command->info("   📤 Insertando {$total} {$nombre} en lotes de {$chunkSize}...");

        $chunks = array_chunk($datos, $chunkSize);

        foreach ($chunks as $index => $chunk) {
            DB::table($tabla)->insert($chunk);

            if (($index + 1) % 10 === 0 || ($index + 1) === count($chunks)) {
                $insertados = min(($index + 1) * $chunkSize, $total);
                $this->command->info("     {$insertados}/{$total} {$nombre} insertados");
            }
        }
    }
}
