<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CubicleSchedule extends Model
{
    protected $table = 'cubiculos_schedules'; // 👈 tu nombre real de tabla

    protected $fillable = [
        'cubiculo_id',
        'schedule_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
