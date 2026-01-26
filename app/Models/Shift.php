<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Contracts\Auditable;


class Shift extends Model implements Auditable
{
    const STATUS_OCCUPIED = 0;    // Ocupado
    const STATUS_AVAILABLE = 1;   // Disponible
    const STATUS_CANCELLED = 2;   // Cancelado

    use HasUuids, \OwenIt\Auditing\Auditable;

    protected $table = 'shifts';
    protected $primaryKey = 'id_shift';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['code'];

    // Ajuste para code
    public function getCodeAttribute()
    {
        return md5($this->id_shift . ($this->person_shift ?? ''));
    }

    // Relaciones
    public function assignment()
    {
        return $this->hasMany(Asignacion::class, 'cubiculo_id', 'cubicle_shift');
    }

    public function cubicle()
    {
        return $this->hasOne(Cubiculo::class, 'id', 'cubicle_shift');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedule_shift');
    }

    public function person()
    {
        return $this->hasOne(StudentRegistration::class, 'cedula', 'person_shift');
    }
    public function scopeAvailable($query)
    {
        return $query->where('status_shift', self::STATUS_AVAILABLE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status_shift', self::STATUS_OCCUPIED);
    }
        
    // Eliminamos los eventos manuales de auditoría
    // OwenIt\Auditing se encarga automáticamente
}
