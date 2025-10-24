<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Contracts\Auditable;

class Shift extends Model implements Auditable
{
    use HasUuids, \OwenIt\Auditing\Auditable;

    protected $table = 'shifts';
    protected $primaryKey = 'id';
    protected $appends = ['code'];

    public function getCodeAttribute()
    {
        return md5($this->id . $this->person_id);
    }

    public function assignment()
    {
        return $this->hasMany(Asignacion::class, 'cubiculo_id', 'cubiculo_id');
    }

    public function cubicle()
    {
        return $this->hasOne(Cubiculo::class, 'id', 'cubiculo_id');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedule_id');
    }

    public function person()
    {
        return $this->hasOne(StudentRegistration::class, 'id');
    }

   /* public function responses()
    {
        return $this->hasMany(Response::class, 'shift_id', 'id');
    }

    public function response()
    {
        return $this->hasOne(Response::class, 'shift_id', 'id');
    }
*/
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            self::createAudit('Shift created', $model->attributes);
        });

        static::updated(function ($model) {
            self::createAudit('Shift updated', $model->attributes);
        });

        static::deleted(function ($model) {
            self::createAudit('Shift deleted', $model->attributes);
        });
    }

   /* protected static function createAudit($event, $model)
    {
        \App\Models\Audit\Shift::insert([
            'status' => $model['status'],
            'details' => $event,
            'person_id' => $model['person_id'],
            'shift_id' => $model['id'],
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'created_at' => now(),
        ]);
    }*/
}
