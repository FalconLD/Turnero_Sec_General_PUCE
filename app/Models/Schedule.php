<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Schedule.php
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'hora_inicio', 'hora_fin', 'descanso', 'atencion', 
        'vigencia_desde', 'vigencia_hasta', 'cubiculo_id'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'vigencia_desde' => 'date', 
        'vigencia_hasta' => 'date', 
    ];

    public function cubiculo()
    {
        return $this->belongsTo(Cubiculo::class);
    }

    public function pauses()
    {
        return $this->hasMany(Pause::class);
    }

    public function days()
    {
        return $this->hasMany(ScheduleDay::class);
    }

    public function getTotalDurationInMinutesAttribute()
    {
        $inicio = \Carbon\Carbon::parse($this->hora_inicio);
        $fin = \Carbon\Carbon::parse($this->hora_fin);

        return $fin->diffInMinutes($inicio);
    }

}
