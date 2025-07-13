<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'id' => 1,
            'name' => '良好',
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'id' => 2,
            'name' => '目立った傷や汚れなし',
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'id' => 3,
            'name' => 'やや傷や汚れあり',
        ];
        DB::table('conditions')->insert($param);
        $param = [
            'id' => 4,
            'name' => '状態が悪い',
        ];
        DB::table('conditions')->insert($param);
    }
}
