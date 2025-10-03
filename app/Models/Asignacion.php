<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table ='asignaciones';

    protected $fillable = ['cubiculo_id', 
                            'form_id', 
                            'fecha_actualizacion'];
    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * @var array
     */
    protected $casts = [
        'fecha_actualizacion' => 'datetime', // <-- SINTAXIS CORREGIDA
    ];                        

    public function cubiculo()
    {
        return $this->belongsTo(Cubiculo::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
