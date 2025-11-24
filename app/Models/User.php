<?php

namespace App\Models;

use Illuminate\Support\HtmlString; // Importante para el salto de línea <br>
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cubiculo;
use Spatie\Permission\Traits\HasRoles; // El Trait de Spatie

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    // Este Trait habilita los métodos como getRoleNames(), hasRole(), etc.
    use HasRoles; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'DNI',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function cubiculos()
    {
        return $this->hasMany(Cubiculo::class);
    }

    // --- AQUÍ ESTÁ LA CORRECCIÓN PARA ADMINLTE ---
    public function adminlte_desc()
    {
        // 1. Obtenemos los nombres de los roles usando Spatie
        // getRoleNames() devuelve una colección (array) de strings
        $roles = $this->getRoleNames();

        // 2. Verificamos si tiene roles asignados
        if ($roles->isNotEmpty()) {
            // Tomamos el primer rol y ponemos la primera letra mayúscula
            // Si quieres mostrar todos separados por coma usa: $roles->implode(', ')
            $roleName = ucfirst($roles->first()); 
        } else {
            $roleName = 'Sin Rol Asignado';
        }

        // 3. Concatenamos: Email + Salto de línea + Nombre del Rol
        // Esto mostrará el correo y DEBAJO el rol (ej: Super Admin)
        $description = $this->email . '<br><strong>' . $roleName . '</strong>';

        return new HtmlString($description);
    }
    
    public function adminlte_profile_url()
    {
        // Asegúrate de que esta ruta exista en tu web.php
        return route('profile.edit');
    }

    public function adminlte_image()
    {
        // Genera un avatar con las iniciales del usuario
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}