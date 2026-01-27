<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cubiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo_atencion',
        'user_id',
        'enlace_o_ubicacion',
        'operating_area_id',
    ];

    // Relación con usuario
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }
    public function horarios()
    {
//        return $this->hasMany(Horario::class, 'cubiculo_hor');
        return $this->belongsToMany(Schedule::class, 'cubiculos_schedules', 'schedule_id','cubiculo_id','id','id_hor');
    }
    public function shifts()
{
    // Relación: Un cubículo tiene muchos turnos
    return $this->hasMany(Shift::class, 'cubicle_shift', 'id');
}

/**
 * Verifica si el cubículo tiene un turno ocupado en este preciso momento
 */
public function getIsOccupiedAttribute()
{
    $now = now();
    return $this->shifts()
        ->where('date_shift', $now->format('Y-m-d'))
        ->where('status_shift', 0) // 0 = Ocupado
        ->where('start_shift', '<=', $now->format('H:i:s'))
        ->where('end_shift', '>=', $now->format('H:i:s'))
        ->exists();
}
}
