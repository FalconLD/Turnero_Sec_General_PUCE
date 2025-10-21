<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
// IMPORTANTE: Importar la clase Hash para encriptar contraseñas
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Usamos create() directamente, es más limpio.
        // Laravel se encargará de encriptar la contraseña si el modelo User está bien configurado.
        
        User::create([
            'name' => 'Darling Arroyo',
            'email' => 'darroyo686@puce.edu.ec',
            'password' => Hash::make('12345678'), // Usar Hash::make()
        ]);

        User::create([
            'name' => 'Cristian Freire',
            'email' => 'cafreire1@puce.edu.ec',
            'password' => Hash::make('4321'),
        ]);

        User::create([
            'name' => 'Cristofer Lopez',
            'email' => 'clopez559@puce.edu.ec',
            'password' => Hash::make('7410'),
        ]);

        User::create([
            'name' => 'Israel Chavez',
            'email' => 'hichavez@puce.edu.ec',
            'password' => Hash::make('4321'),
        ]);

        User::create([
            'name' => 'Ker Viera',
            'email' => 'kviera@puce.edu.ec',
            'password' => Hash::make('4321'),
        ]);
        
        //ejemplos de usuarios para creacion de cubiculos
        User::create([
            'name' => 'Francisco Romero',
            'email' => 'fjromero@puce.edu.ec',
            'password' => Hash::make('4321'),
        ]);
        // OPCIONAL: Crear usuarios de prueba con datos falsos usando un Factory
        // Esto es muy útil para llenar la base de datos con muchos usuarios de prueba.
        // User::factory(10)->create();
    }
}
 