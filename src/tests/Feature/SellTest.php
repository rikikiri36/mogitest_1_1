<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品出品画面で必要な情報が保存できること()
    {
        // ユーザーを作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        //カテゴリー・コンディションを作成
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);

        $this->actingAs($user1);

        $categoryIds = \App\Models\Category::pluck('id')->take(2)->toArray(); // 複数選択

        $response = $this->post('/sell/store', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 5000,
            'condition_id' => 1,
            'categories' => $categoryIds,
            'image' => 'test.png',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/sell');
    $response->assertSessionHas('status', '出品が完了しました！');

        // itemsテーブルに保存されたことを確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 5000,
            'condition_id' => 1,
            'user_id' => $user1->id,
        ]);

        // item_categoryも確認
        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('item_categories', [
                'category_id' => $categoryId,
            ]);
        }
    }
}