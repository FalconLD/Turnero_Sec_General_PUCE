<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        // 1. Super Admin
        $admin = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@puce.edu.ec',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Super Admin');

        $admin = User::create([
            'name' => 'Fabian',
            'email' => 'fabiann@puce.edu.ec',
            'password' => Hash::make('fabian'),
        ]);
        $admin->assignRole('Super Admin');


        // 2. Super Administrador de Apoyo
        $admin2 = User::create([
            'name'     => 'Soporte DSW',
            'email'    => 'didesarrollo@puce.edu.ec',
            'password' => Hash::make('password'),
        ]);
        $admin2->assignRole('Super Admin');

        // 3. Lista Combinada (Operadores reales)
        $csvUsers = [
            ['name' => 'Belén Salazar', 'email' => 'jbsalazare@puce.edu.ec', 'dni' => '1724990922'],
            ['name' => 'Daniel Vinueza', 'email' => 'devinueza@puce.edu.ec', 'dni' => '1725080673'],
            ['name' => 'Paola Rivas', 'email' => 'srivas314@puce.edu.ec', 'dni' => null],
            ['name' => 'Denisse Barragan', 'email' => 'dsbarraganm@puce.edu.ec', 'dni' => '1726775156'],
            ['name' => 'Santiago Arroyo', 'email' => 'sarroyo946@puce.edu.ec', 'dni' => '0401487053'],
            ['name' => 'Wendy Aguilar', 'email' => 'wtaguilar@puce.edu.ec', 'dni' => '1250119417'],
            ['name' => 'Patricia Llerena', 'email' => 'pklarrea@puce.edu.ec', 'dni' => '1709453094'],
            ['name' => 'Lorena Guaman', 'email' => 'lguaman@puce.edu.ec', 'dni' => '2300139801'],
            ['name' => 'Sandra Rodríguez', 'email' => 'smrodriguezz@puce.edu.ec', 'dni' => '1724151939'],
            ['name' => 'Maria Eugenia Obando', 'email' => 'mobando@puce.edu.ec', 'dni' => null],
            ['name' => 'Cinthya Cruz', 'email' => 'cacruzq@puce.edu.ec', 'dni' => '1726664426'],
            ['name' => 'Freddy Garces', 'email' => 'fpgarces@puce.edu.ec', 'dni' => '1713674040'],
            ['name' => 'Karla Vásconez', 'email' => 'kevasconez@puce.edu.ec', 'dni' => '1725295644'],
            ['name' => 'Javier Romero', 'email' => 'fjromero@puce.edu.ec', 'dni' => '1714065164'],
            ['name' => 'Santiago Garcia', 'email' => 'sgarcia738@puce.edu.ec', 'dni' => '1722223862'],
            ['name' => 'Verónica Carrillo', 'email' => 'vgcarrillo@puce.edu.ec', 'dni' => '603881145'],
        ];

        foreach ($csvUsers as $u) {
            $pass = explode('@', $u['email'])[0]; // Contraseña es el usuario del correo

            $user = User::create([
                'name'     => $u['name'],
                'email'    => $u['email'],
                'DNI'      => $u['dni'],
                'password' => Hash::make($pass),
            ]);

            $user->assignRole('Operador');
        }
    }
}
