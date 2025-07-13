<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => '123123123',
            'password_confirmation' => '123123123',
        ]);

        $response->assertSessionHasErrors('name');

        $errors = session('errors');
        $this->assertTrue($errors->get('name')[0] === 'お名前を入力してください');
    }

    /** @test */
    public function メールアドレスが未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => '123123123',
            'password_confirmation' => '123123123',
        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertTrue($errors->get('email')[0] === 'メールアドレスを入力してください');
    }

    /** @test */
    public function パスワードが未入力だとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '123123123',
        ]);

        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertTrue($errors->get('password')[0] === 'パスワードを入力してください');
    }

    /** @test */
    public function パスワードが短すぎるとエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertTrue($errors->get('password')[0] === 'パスワードは8文字以上で入力してください');
    }

    /** @test */
    public function パスワード確認用と一致しないとバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);
    
        // エラーがセッションにあることを確認
        $response->assertSessionHasErrors('password_confirmation');
    
        // エラーメッセージの中身を確認
        $errors = session('errors');
        $this->assertSame('パスワードと一致しません', $errors->get('password_confirmation')[0]);
    }

    /** @test */
    public function 登録成功でプロフィール画面に遷移する()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}
