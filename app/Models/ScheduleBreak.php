<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleBreak extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'start_break', 'end_break'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id_hor');
    }
}
