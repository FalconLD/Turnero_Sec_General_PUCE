<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftUnlock extends Model
{
    use HasFactory;

    protected $table = 'shifts'; // Tabla que vamos a consultar
    protected $primaryKey = 'id_shift';
    public $timestamps = false;

    protected $fillable = [
        'person_shift',
        'status_shift',
    ];
}
