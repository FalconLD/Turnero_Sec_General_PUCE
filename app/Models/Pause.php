<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Pause.php
class Pause extends Model
{
    use HasFactory;
    protected $fillable = ['schedule_id', 'hora_inicio', 'hora_fin'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}