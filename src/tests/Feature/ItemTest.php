<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウト時、全商品が表示される()
    {
        // 出品者2人を作成
        $seller1 = User::create([
            'name' => '出品者１',
            'email' => 'seller1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $seller2 = User::create([
            'name' => '出品者２',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品をそれぞれ作成
        Item::factory()->create([
            'name' => '商品１',
            'user_id' => $seller1->id,
        ]);

        Item::factory()->create([
            'name' => '商品２',
            'user_id' => $seller2->id,
        ]);

        // ログアウト状態でアクセス
        $response = $this->get('/');

        // 両方の商品が見える
        $response->assertSee('商品１');
        $response->assertSee('商品２');
    }

    /** @test */
    public function 購入済みにはSoldと表示する()
    {
        $user = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        // 商品をファクトリで生成（出品者をログインユーザー以外にする）
        $item = Item::factory()->create([
            'user_id' => $seller->id
        ]);

        // 注文データを作成
        DB::table('orders')->insert([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 2,
            'zipcode' => '111-1111',
            'adress' => '東京都',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ログイン
        $this->actingAs($user);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // 商品名と「Sold」が表示されていることを確認
        $response->assertSee($item->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function ログイン時、自分が出品した商品は表示されない()
    {
        // 出品者と他人
        $user = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $otherUser = User::create([
            'name' => 'その他',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        // 自分の商品と他人の商品を出品
        Item::factory()->create([
            'name' => '自分の商品',
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'name' => '他人の商品',
            'user_id' => $otherUser->id,
        ]);

        // ログイン
        $this->actingAs($user);

        $response = $this->get('/');

        // 他人の商品は見える、自分の商品は見えない
        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }
}
