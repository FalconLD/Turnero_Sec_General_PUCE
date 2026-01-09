<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        // 1. Super Admin
        $admin = User::create([
            'id' => 1, 'name' => 'Super Administrador', 'email' => 'admin@puce.edu.ec', 'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Super Admin');

        // 2. Recepción
        $recep = User::create([
            'id' => 2, 'name' => 'Recepción Turnero', 'email' => 'recepcion@puce.edu.ec', 'password' => Hash::make('password'),
        ]);
        $recep->assignRole('Recepcion');

        $csvUsers = [
            ['id' => 3, 'name' => 'Belén Salazar', 'email' => 'jbsalazare@puce.edu.ec', 'dni' => '1724990922'],
            ['id' => 4, 'name' => 'Daniel Vinueza', 'email' => 'devinueza@puce.edu.ec', 'dni' => '1725080673'],
            ['id' => 5, 'name' => 'Paola Rivas', 'email' => 'srivas314@puce.edu.ec', 'dni' => null],
            ['id' => 6, 'name' => 'Denisse Barragan', 'email' => 'dsbarraganm@puce.edu.ec', 'dni' => '1726775156'],
            ['id' => 7, 'name' => 'Santiago Arroyo', 'email' => 'sarroyo946@puce.edu.ec', 'dni' => '0401487053'],
            ['id' => 8, 'name' => 'Wendy Aguilar', 'email' => 'wtaguilar@puce.edu.ec', 'dni' => '1250119417'],
            ['id' => 9, 'name' => 'Patricia Llerena', 'email' => 'pklarrea@puce.edu.ec', 'dni' => '1709453094'],
            ['id' => 10, 'name' => 'Lorena Guaman', 'email' => 'lguaman@puce.edu.ec', 'dni' => '2300139801'],
            ['id' => 11, 'name' => 'Sandra Rodríguez', 'email' => 'smrodriguezz@puce.edu.ec', 'dni' => '1724151939'],
            ['id' => 12, 'name' => 'Maria Eugenia Obando', 'email' => 'mobando@puce.edu.ec', 'dni' => null],
            ['id' => 13, 'name' => 'Cinthya Cruz', 'email' => 'cacruzq@puce.edu.ec', 'dni' => '1726664426'],
            ['id' => 14, 'name' => 'Freddy Garces', 'email' => 'fpgarces@puce.edu.ec', 'dni' => '1713674040'],
            ['id' => 15, 'name' => 'Karla Vásconez', 'email' => 'kevasconez@puce.edu.ec', 'dni' => '1725295644'],
            ['id' => 16, 'name' => 'Javier Romero', 'email' => 'fjromero@puce.edu.ec', 'dni' => '1714065164'],
            ['id' => 17, 'name' => 'Santiago Garcia', 'email' => 'sgarcia738@puce.edu.ec', 'dni' => '1722223862'],
            ['id' => 18, 'name' => 'Verónica Carrillo', 'email' => 'vgcarrillo@puce.edu.ec', 'dni' => '603881145'],
        ];

        foreach ($csvUsers as $u) {
            $pass = explode('@', $u['email'])[0]; // ej: jbsalazare
            $user = User::create([
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'DNI' => $u['dni'], // DNI en mayúsculas por la migración
                'password' => Hash::make($pass),
            ]);
            $user->assignRole('Operador');
        }
    }
}