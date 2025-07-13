<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名で部分一致検索ができる()
    {

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        // 商品作成
        $item1 = Item::factory()->create([
            'name' => 'ショルダーバッグ',
            'user_id' => $user->id
        ]);
        $item2 = Item::factory()->create([
            'name' => '時計',
            'user_id' => $user->id
        ]);

        // 検索で「バッグ」を含む商品を探す
        $response = $this->get('/?search_item=バッグ');

        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('時計');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        // 商品作成
        $item1 = Item::factory()->create([
            'name' => 'ショルダーバッグ',
            'user_id' => $user->id
        ]);
        $item2 = Item::factory()->create([
            'name' => '時計',
            'user_id' => $user->id
        ]);

        // いいね登録
        Like::create([
            'item_id' => $item1 ->id,
            'user_id' => $user ->id,
        ]);

        Like::create([
            'item_id' => $item2 ->id,
            'user_id' => $user ->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist&search_item=バッグ');

        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('時計');
    }
}
