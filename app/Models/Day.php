<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class Day extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'days';

    protected $fillable = ['schedule_day', 'date_day'];

    protected $casts = [
        'date_day' => 'datetime'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_day', 'id_hor');
    }
}
