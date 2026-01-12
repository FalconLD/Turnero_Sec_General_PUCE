<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Career extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'career_code', 'operating_area_id'];

    // Relación: Una carrera pertenece a un área operativa
    public function operatingArea()
    {
        return $this->belongsTo(OperatingArea::class, 'operating_area_id');
    }
}
