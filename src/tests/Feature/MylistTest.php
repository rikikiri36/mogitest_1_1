<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::create([
            'name' => 'ログインユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $otherUser = User::create([
            'name' => 'その他',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品をそれぞれ作成
        $likedItem = Item::factory()->create([
            'name' => 'いいねした商品',
            'user_id' => $otherUser->id,
        ]);

        $notLikedItem = Item::factory()->create([
            'name' => 'いいねしていない商品',
            'user_id' => $otherUser->id,
        ]);

        // いいね登録
        Like::create([
            'item_id' => $likedItem ->id,
            'user_id' => $user ->id,
        ]);

        // ログイン
        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertSee($likedItem->name);
        $response->assertDontSee($notLikedItem->name);
    }

    /** @test */
    public function 購入済みの商品には_sold_と表示される()
    {
        $user = User::create([
            'name' => 'ログインユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $otherUser = User::create([
            'name' => 'その他',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品を作成
        $item = Item::factory()->create([
            'name' => '注文した商品',
            'user_id' => $otherUser->id,
        ]);

        // いいね登録
        Like::create([
            'item_id' => $item ->id,
            'user_id' => $user ->id,
        ]);

        // 注文データを作成
        Order::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment' => 2,
            'zipcode' => '111-1111',
            'adress' => '東京都',
        ]);

        // ログイン
        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        // 商品名と「Sold」が表示されていることを確認
        $response->assertSee($item->name);
        $response->assertSee('Sold');

    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get('/?tab=mylist');

        // 一覧が表示されていない
        $response->assertDontSee('item-list');
    }
}
