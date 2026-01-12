<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder {
    public function run() {
        $assignments = [
            ['user_id' => 3, 'operating_area_id' => 11], ['user_id' => 4, 'operating_area_id' => 21],
            ['user_id' => 5, 'operating_area_id' => 31], ['user_id' => 6, 'operating_area_id' => 41],
            ['user_id' => 13, 'operating_area_id' => 41], ['user_id' => 7, 'operating_area_id' => 51],
            ['user_id' => 14, 'operating_area_id' => 51], ['user_id' => 16, 'operating_area_id' => 51],
            ['user_id' => 8, 'operating_area_id' => 61], ['user_id' => 9, 'operating_area_id' => 71],
            ['user_id' => 10, 'operating_area_id' => 74], ['user_id' => 15, 'operating_area_id' => 74],
            ['user_id' => 18, 'operating_area_id' => 74], ['user_id' => 17, 'operating_area_id' => 74],
            ['user_id' => 10, 'operating_area_id' => 72], ['user_id' => 15, 'operating_area_id' => 72],
            ['user_id' => 18, 'operating_area_id' => 72], ['user_id' => 17, 'operating_area_id' => 72],
            ['user_id' => 11, 'operating_area_id' => 73], ['user_id' => 4, 'operating_area_id' => 81],
            ['user_id' => 12, 'operating_area_id' => 91]
        ];
        DB::table('area_user')->insert($assignments);
    }
}
