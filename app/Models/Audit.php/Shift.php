<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'audit_turnos'; // Table name
    protected $primaryKey = false;     // No primary key

    protected $fillable = [
        'estado_at',    // Status
        'turno_at',     // Shift
        'persona_at',   // Person
        'usuario_at',   // User
        'detalle_at',   // Details
        'created_at',   // Created at
        'updated_at'    // Updated at
    ];
}
