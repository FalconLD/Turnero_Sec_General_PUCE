<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 
 
use App\Models\User;
 
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
{
    $users = [
        ['name' => 'Darling Arroyo', 'email' => 'darroyo606@puce.edu.ec', 'password' => '12345678'],
        ['name' => 'Cristian Freire', 'email' => 'cafreirel@puce.edu.ec', 'password' => '4321'],
        ['name' => 'Cristhofer Lopez', 'email' => 'clopez559@puce.edu.ec', 'password' => '7410'],
    ];

    foreach ($users as $user) {
        User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
        ]);
    }
}
}
 