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
        'edad',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'correo_puce',
        'facultad',
        'carrera',
        'nivel',
        'motivo',
        'nivel_instruccion',
        'beca_san_ignacio',
        'valor_pagar',
        'forma_pago',
        'acepta_terminos',
        'comprobante',
    ];
}
