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
        'beca_san_ignacio',
        'acepta_terminos',
        'tomado',
    ];

    /**
     * El modelo ya no requiere relaciones con Payment ni PayStudent
     * ya que la lógica de cobros ha sido removida del sistema.
     */
}
