<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OperatingArea extends Model
{
    use HasFactory;

    protected $fillable = ['faculty_id', 'name', 'description'];

    // Relación: Una Área pertenece a una Facultad
public function faculty()
{
    // Según tu imagen de la DB, la tabla es 'faculties' y la llave es 'faculty_id'
    return $this->belongsTo(Faculty::class, 'faculty_id');
}
}