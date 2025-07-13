<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール画面に必要な情報が表示される()
    {
        // ユーザー、プロフィール、商品作成
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

        $profile = Profile::create([
            'user_id' => $user1->id,
            'name' => 'テストユーザー',
            'image' => 'profile.png',
            'zipcode' => '123-4567',
            'adress' => '東京都',
            'building' => 'テストビル'
        ]);

        // 出品商品
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $sellItem = Item::factory()->create([
            'user_id' => $user1->id,
            'name' => '出品商品',
        ]);

        // 購入商品（別のユーザーの商品を購入）
        $buyItem = Item::factory()->create([
            'user_id' => $user2->id,
            'name' => '購入商品',
        ]);

        Order::create([
            'item_id' => $buyItem->id,
            'user_id' => $user1->id,
            'payment' => 1,
            'zipcode' => '123-4567',
            'adress' => '東京都',
            'building' => '購入先ビル'
        ]);

        $this->actingAs($user1);

        // マイページ（出品タブ）へアクセス
        $response = $this->get('/mypage?tab=sell');

        // プロフィール画像・プロフィール名・出品商品が表示されている
        $response->assertSee('profile.png');
        $response->assertSee('テストユーザー');
        $response->assertSee('出品商品');

        // 購入タブにアクセスして購入商品が表示されている
        $buyResponse = $this->get('/mypage?tab=buy');
        $buyResponse->assertSee('購入商品');
    }
}
