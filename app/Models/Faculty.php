<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'facultad',
        'programa_desc',
        'nivel',
    ];
    // Una facultad tiene muchas áreas operativas
    public function operatingAreas()
    {
        return $this->hasMany(OperatingArea::class);
    }
}
