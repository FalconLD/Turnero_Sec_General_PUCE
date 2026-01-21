<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'names',
        'cedula',
        'banner_id',
        'edad',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'correo_puce',
        'facultad',
        'carrera',
        'nivel',
        'plan_estudio',
        'motivo',
        'nivel_instruccion',
        'acepta_terminos',
        'tomado',
    ];
}
