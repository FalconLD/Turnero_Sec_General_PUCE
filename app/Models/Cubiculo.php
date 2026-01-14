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
        'enlace_o_ubicacion', // <-- nuevo campo
        'operating_area_id', // <-- nuevo campo
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
}
