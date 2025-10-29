<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
// 1. Importamos tanto la Interfaz como el Trait CORRECTAMENTE
use OwenIt\Auditing\Contracts\Auditable as AuditableContract; 
use OwenIt\Auditing\Auditable; // ← CORREGIDO: 'OwenIt' no 'Owenlt'
use App\Models\Turno;

class Schedule extends Model implements AuditableContract
{
    // 2. Usamos el trait Auditable. Como implementamos la interfaz con un alias,
    //    ya no hay conflicto de nombres aquí.
    use HasUuids, Auditable;


    protected $table = 'schedules';
    
    protected $primaryKey = 'id_hor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'start_time',        // previously inicio_hor
        'end_time',          // previously fin_hor
        'valid_from',        // previously vigencia_hor
        'break_minutes',     // previously descanso_hor
        'attention_minutes', // previously atencion_hor
    ];

    protected static function booted()
    {
        static::creating(function ($schedule) {
            if (empty($schedule->{$schedule->getKeyName()})) {
                $schedule->{$schedule->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
    
    // El resto de tu modelo...
    
    protected $appends = ['until'];

    /**
     * Get the latest day for this schedule.
     */
    public function getUntilAttribute()
    {
        return optional($this->maxDate()->first())->date_day;
    }

    /**
     * Relationship: Schedule has many Days
     */
    public function days()
    {
        return $this->hasMany(Day::class, 'schedule_day', 'id_hor');
    }

    /**
     * Get the latest date among all days
     */
    public function maxDate()
    {
        return $this->hasOne(Day::class, 'schedule_day', 'id_hor')
                    ->latest('date_day')
                    ->select('date_day');
    }

    /**
     * Relationship: Schedule has many Shifts
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'schedule_shift', 'id_hor');
    }

    /**
     * Relationship: Schedule has many Breaks
     */
    public function breaks()
    {
        return $this->hasMany(ScheduleBreak::class, 'schedule_id', 'id_hor');
    }

    /**
     * Relationship: Schedule has many occupied shifts (assigned to a person)
     */
    public function occupiedShifts()
    {
        return $this->hasMany(Shift::class, 'schedule_shift', 'id_hor')
                    ->whereNotNull('person_shift');
    }

    /**
     * Relationship: Schedule belongs to many cubicles
     */
    public function cubicles()
    {
        return $this->belongsToMany(
            Cubiculo::class,
            'cubiculos_schedules',  
            'schedule_id',
            'cubiculo_id'
        );
    }
    


}