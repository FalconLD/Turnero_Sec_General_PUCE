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
    public function run()
    {
        User::create([
            'name'=>'Darling Arroyo',
            'email'=>'darroyo606@puce.edu.ec',
            'password'=> bcrypt('12345678')
        ]);
    }
}
 