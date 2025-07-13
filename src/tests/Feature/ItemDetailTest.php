<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Like;
use App\Models\Comment;
use App\Models\ItemCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細で必要な情報が表示される()
    {
        // 出品者を作成
        $seller1 = User::create([
            'name' => '出品者１',
            'email' => 'seller1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        // コメント者を作成
        $commenter1 = User::create([
            'name' => 'コメント１',
            'email' => 'comment1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        Profile::create([
            'user_id' => $commenter1->id,
            'name' => 'コメントユーザー',
            'image' => 'profile.png',
            'zipcode' => '123-4567',
            'adress' => '東京都',
        ]);

        //商品作成
         $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
         $condition = \App\Models\Condition::first();

        $item = \App\Models\Item::factory()->create([
            'user_id' => $seller1->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 9999,
            'description' => 'これは説明文です',
            'condition_id' => $condition->id,
            'image' => 'itemtest.png',
        ]);

        // いいね登録
        Like::create([
            'item_id' => $item->id,
            'user_id' => $commenter1->id,
        ]);

        // コメント登録
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commenter1->id,
            'detail' => 'これはコメントです',
        ]);

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $category = \App\Models\Category::first();
        ItemCategory::create([
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/item/' . $item->id);

    $response->assertStatus(200);

        // 各要素が含まれているか確認
        $response->assertSee('テスト商品'); // 商品名
        $response->assertSee('テストブランド'); // ブランド
        $response->assertSee('itemtest.png'); // 商品画像
        $response->assertSee('9,999'); // 価格
        $response->assertSee('これは説明文です'); // 説明
        $response->assertSee($category->name); // カテゴリ
        $response->assertSee($condition->name); // 商品の状態

        $response->assertSee('<span class="like-count" id="likeCount">1</span>', false);  // いいね数
        $response->assertSee('コメント (1)'); // コメント数
        $response->assertSee('コメントユーザー'); // コメントしたユーザー名
        $response->assertSee('profile.png'); // コメントしたユーザー画像
        $response->assertSee('これはコメントです'); // コメント内容
    }
}
