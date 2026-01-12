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
        return $this->belongsTo(Faculty::class);
    }
}
