<?php

namespace App\Models;
use Illuminate\Support\HtmlString;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cubiculo;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable

{
    use HasApiTokens, HasFactory, Notifiable;

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

    // ESTA ES LA NUEVA FUNCIÓN
    public function adminlte_desc()
    {
        $role = $this->role ?? 'Usuario';
        $description = $this->email . '<br>' . $role;
        return new HtmlString($description);
    }
    
    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }

    public function adminlte_image()
    {
        // Puedes personalizar la ruta de la imagen según tus necesidades
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=random';
    }
}
