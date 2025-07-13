<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'てすと太郎',
            'user_id' => 1,
            'zipcode' => '100-0000',
            'adress' => '東京都千代田区大手町１−１−１',
            'building' => '建物１',
        ];
        DB::table('profiles')->insert($param);
        $param = [
            'name' => 'てすと花子',
            'user_id' => 2,
            'zipcode' => '100-0000',
            'adress' => '東京都千代田区大手町１−１−１',
            'building' => '建物１',
        ];
        DB::table('profiles')->insert($param);
        $param = [
            'name' => 'てすと三郎',
            'user_id' => 3,
            'zipcode' => '100-0000',
            'adress' => '東京都千代田区大手町１−１−１',
            'building' => '建物１',
        ];
        DB::table('profiles')->insert($param);
    }
}
