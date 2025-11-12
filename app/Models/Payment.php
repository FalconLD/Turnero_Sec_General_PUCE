<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    // Campos que podemos llenar masivamente
    protected $guarded = [];

    protected $casts = [
        'verified_at' => 'datetime', // 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtiene el registro de estudiante al que pertenece este pago.
     */
    public function studentRegistration()
    {
        return $this->belongsTo(StudentRegistration::class);
    }

    /**
     * Obtiene el administrador (User) que verificó este pago.
     */
    public function verifier()
    {
        // Asume que tu modelo de admin se llama 'User'
        return $this->belongsTo(User::class, 'verified_by');
    }
}
