<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ScheduleDay extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'schedule_days';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'weekday', // <-- ESTA LÍNEA ES LA SOLUCIÓN
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}

