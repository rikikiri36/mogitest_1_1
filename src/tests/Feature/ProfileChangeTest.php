<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 変更項目が初期値として設定されている()
    {
        // ユーザー、プロフィール作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'testuser1@example.com',
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

        $this->actingAs($user1);

        // マイページ（出品タブ）へアクセス
        $response = $this->get('/mypage/profile');

        // プロフィール画像・プロフィール名・郵便番号・住所が表示されている
        $response->assertSee('profile.png');
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都');
        $response->assertSee('テストビル');
    }
}
