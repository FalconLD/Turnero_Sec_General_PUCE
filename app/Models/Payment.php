<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Permitir asignación masiva de todos los campos
    protected $guarded = [];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el estudiante (registro de inscripción)
     */
    public function studentRegistration()
    {
        return $this->belongsTo(StudentRegistration::class);
    }

    /**
     * Relación con el usuario que verificó el pago
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * 🔹 Método de ayuda para generar enlace de visualización del comprobante
     */
    public function getComprobanteUrlAttribute()
    {
        if ($this->comprobante_base64) {
            return route('payments.verComprobante', $this->id);
        }

        if ($this->comprobante_path) {
            return asset('storage/' . $this->comprobante_path);
        }

        return null;
    }

    /**
     * 🔹 Método de ayuda para generar enlace de descarga del comprobante
     */
    public function getComprobanteDownloadUrlAttribute()
    {
        if ($this->comprobante_base64) {
            return route('payments.descargarComprobante', $this->id);
        }

        if ($this->comprobante_path) {
            return asset('storage/' . $this->comprobante_path);
        }

        return null;
    }
}
