<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        // ユーザーを作成
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

        //Item作成前に、conditionsテーブルを作成しておく 
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        // 商品を作成
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        $this->actingAs($user1);

        $response = $this->post('/comment', [
            'item_id' => $item->id,
            'detail' => 'とても素敵な商品ですね！',
        ]);

        // コメント後、元のページなどへリダイレクトされる想定なら
        $response->assertRedirect();

        // commentsテーブルにコメントが追加されたことを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'detail' => 'とても素敵な商品ですね！',
        ]);
    }

    /** @test */
    public function 未ログインのユーザーはコメント送信できない()
    {
        // ユーザー、商品を作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user1->id,
        ]);

        // ログインせずにPOST
        $response = $this->post('/comment', [
            'item_id' => $item->id,
            'detail' => '未ログインでコメント',
        ]);

        // authミドルウェアにより/loginへリダイレクトされることを確認
        $response->assertRedirect('/login');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'detail' => '未ログインでコメント',
        ]);
    }

    /** @test */
    public function コメントが空の場合、バリデーションメッセージが表示される()
    {
        // ユーザー、商品を作成
        $user = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // コメント送信（空）
        $response = $this->from('/item/' . $item->id)->post('/comment', [
            'item_id' => $item->id,
            'detail' => '',
        ]);

        // 元のページにリダイレクトされることを確認
        $response->assertRedirect('/item/' . $item->id);

        // 再アクセスしてエラーメッセージの表示を確認
        $response = $this->get('/item/' . $item->id);

        // Bladeで表示されるエラーメッセージを確認
        $response->assertSee('商品のコメントを入力してください');
    }

    /** @test */
    public function コメントが255文字を超えるとバリデーションメッセージが表示される()
    {
        // ユーザー、商品を作成
        $user = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // 256文字のコメントを作成
        $longComment = str_repeat('あ', 256);

        $response = $this->from('/item/' . $item->id)->post('/comment', [
            'item_id' => $item->id,
            'detail' => $longComment,
        ]);

        // リダイレクトを確認
        $response->assertRedirect('/item/' . $item->id);

        // 再アクセスしてバリデーションメッセージの表示を確認
        $response = $this->get('/item/' . $item->id);

        $response->assertSee('商品のコメントは255文字以下で入力してください');
    }
}
