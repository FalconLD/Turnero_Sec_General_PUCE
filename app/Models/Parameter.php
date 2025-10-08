<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $fillable = ['clave', 'descripcion', 'parametro'];
}
