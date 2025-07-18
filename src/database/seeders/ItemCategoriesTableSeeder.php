<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'item_id' => 1,
            'category_id' => 1
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 1,
            'category_id' => 5
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 1,
            'category_id' => 12
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 2,
            'category_id' => 2
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 3,
            'category_id' => 10
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 4,
            'category_id' => 1
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 4,
            'category_id' => 5
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 7,
            'category_id' => 1
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 7,
            'category_id' => 4
        ];
        DB::table('item_categories')->insert($param);
        $param = [
            'item_id' => 9,
            'category_id' => 10
        ];
        DB::table('item_categories')->insert($param);
    }
}
