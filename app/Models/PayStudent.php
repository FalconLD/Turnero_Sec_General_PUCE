<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayStudent extends Model
{
    protected $fillable = [
        'cedula',
        'valor_pagar',
        'forma_pago',
        'comprobante',
        'student_registration_id',
    ];

    public function student()
    {
        return $this->belongsTo(StudentRegistration::class, 'student_registration_id');
    }
}
