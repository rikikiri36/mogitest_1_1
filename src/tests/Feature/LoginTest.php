<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function メールアドレスが未入力だとバリデーションエラーになる()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => '123123123',
        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertTrue($errors->get('email')[0] === 'メールアドレスを入力してください');
    }

    /** @test */
    public function パスワードが未入力だとバリデーションエラーになる()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertTrue($errors->get('password')[0] === 'パスワードを入力してください');
    }

    /** @test */
    public function ログイン情報が間違っている場合バリデーションエラーが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '123',
        ]);

        $errors = session('errors');
        $this->assertTrue($errors->get('email')[0] === 'ログイン情報が登録されていません');
    }

    /** @test */
    public function 正しい情報でログインするとホームにリダイレクトされる()
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('123123123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '123123123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
