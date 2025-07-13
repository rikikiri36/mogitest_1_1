<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトできること()
    {
        // ユーザー作成
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'testtesttest@example.com',
            'password' => Hash::make('123123123'),
        ]);
        // ログイン状態にする
        $this->actingAs($user);

        // logout実行
        $response = $this->post('/logout');

        // リダイレクトを確認
        $response->assertRedirect('/');

        // ログアウト状態を確認
        $this->assertGuest();
    }
}
