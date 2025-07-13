<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンをタップでいいねした商品としてを登録する()
    {
        // ユーザーを作成
        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品を作成
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // いいね登録を実行（POSTリクエスト）
        $response = $this->post('/like', [
            'item_id' => $item->id,
            'hasLiked' => false, // まだいいねしていない想定
        ]);

        // likesテーブルにレコードがあることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function いいね済みの商品はアイコンの色が変化する()
    {
        
        // ユーザーを作成
        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品を作成
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user->id,
        ]);

        // いいね済みにする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        // 商品詳細ページへアクセス
        $response = $this->get('/item/' . $item->id);

        // class="like-icon active" がHTMLに含まれていることを確認
        $response->assertSee('class="like-icon active"', false);
    }

    /** @test */
    public function 再度いいねアイコンをタップするといいねが解除される()
    {
        // ユーザーを作成
        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'testuser@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品を作成
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user->id,
        ]);

        // いいね済みにする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        // いいね解除のリクエストを送る
        $response = $this->post('/like', [
            'item_id' => $item->id,
            'hasLiked' => true,
        ]);

        $response->assertRedirect();

        // likesテーブルにそのレコードがなくなっていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
