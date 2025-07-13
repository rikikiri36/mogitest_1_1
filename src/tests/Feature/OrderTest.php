<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入ボタンをタップすると購入が完了する()
    {
        // ユーザー、商品を作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'testuser2@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        $this->actingAs($user1);

        $response = $this->post('/purchase/checkout', [
            'item_id' => $item->id,
            'payment' => 1,
            'zipcode' => '111-1111',
            'adress' => '東京都',
            'building' => 'テストビル103',
            'item_name' => $item->name,
            'item_price' => $item->price,
        ]);

        // 購入処理（注文登録）が完了したらリダイレクトされる想定
        $response->assertRedirect('/');

        // ordersテーブルに注文データが存在することを確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 購入済みの商品は商品一覧にてSoldと表示される()
    {
        // ユーザー、商品を作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'testuser2@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        // 購入処理（ordersテーブルに登録）
        \App\Models\Order::create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'zipcode' => '111-1111',
            'adress' => '東京都',
            'payment' => 2,
        ]);

        $this->actingAs($user1);

        $response = $this->get('/');

        // 商品名とともに「Sold」が表示されていることを確認
        $response->assertSee($item->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function 購入完了後プロフィールの購入一覧に商品が表示される()
    {
        // ユーザー、商品を作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'testuser2@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        // 購入処理（ordersテーブルに登録）
        Order::create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'payment' => 1,
            'zipcode' => '111-1111',
            'adress' => '東京都',
            'building' => 'テストビル103',
        ]);

        $this->actingAs($user1);

        // 「購入した商品一覧」にアクセス
        $response = $this->get('/mypage?tab=buy');

        // 購入済みの商品名が表示されていることを確認
        $response->assertSee('商品１');
    }
}