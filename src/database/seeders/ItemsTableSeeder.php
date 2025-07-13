<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '腕時計',
            'price' => 15000,
            'image' => 'images/items/Armani+Mens+Clock.jpg',
            'condition_id' => 1,
            'brand' => 'American',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'user_id' => 1
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'HDD',
            'price' => 5000,
            'image' => 'images/items/HDD+Hard+Disk.jpg',
            'condition_id' => 2,
            'brand' => 'American',
            'description' => '高速で信頼性の高いハードディスク',
            'user_id' => 1
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => '玉ねぎ3束',
            'price' => 300,
            'image' => 'images/items/iLoveIMG+d.jpg',
            'condition_id' => 3,
            'brand' => 'American',
            'description' => '新鮮な玉ねぎ3束のセット',
            'user_id' => 1
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => '革靴',
            'price' => 4000,
            'image' => 'images/items/Leather+Shoes+Product+Photo.jpg',
            'condition_id' => 4,
            'brand' => 'American',
            'description' => 'クラシックなデザインの革靴',
            'user_id' => 1
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'ノートPC',
            'price' => 45000,
            'image' => 'images/items/Living+Room+Laptop.jpg',
            'condition_id' => 1,
            'brand' => '',
            'description' => '高性能なノートパソコン',
            'user_id' => 1
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'マイク',
            'price' => 8000,
            'image' => 'images/items/Music+Mic+4632231.jpg',
            'condition_id' => 2,
            'brand' => '',
            'description' => '高音質のレコーディング用マイク',
            'user_id' => 2
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'image' => 'images/items/Purse+fashion+pocket.jpg',
            'condition_id' => 3,
            'brand' => '',
            'description' => 'おしゃれなショルダーバッグ',
            'user_id' => 2
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'タンブラー',
            'price' => 500,
            'image' => 'images/items/Tumbler+souvenir.jpg',
            'condition_id' => 4,
            'brand' => '',
            'description' => '使いやすいタンブラー',
            'user_id' => 2
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'コーヒーミル',
            'price' => 4000,
            'image' => 'images/items/Waitress+with+Coffee+Grinder.jpg',
            'condition_id' => 1,
            'brand' => '',
            'description' => '手動のコーヒーミル',
            'user_id' => 2
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'メイクセット',
            'price' => 2500,
            'image' => 'images/items/外出メイクアップセット.jpg',
            'condition_id' => 2,
            'brand' => '',
            'description' => '便利なメイクアップセット',
            'user_id' => 2
        ];
        DB::table('items')->insert($param);

    }
}
