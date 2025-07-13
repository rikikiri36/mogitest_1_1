<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 住所を変更すると購入画面に反映される()
    {
        // ユーザー、プロフィール、商品作成
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

        $profile = \App\Models\Profile::create([
            'user_id' => $user1->id,
            'name' => 'テストユーザー',
            'zipcode' => '111-1111',
            'adress' => '東京都',
            'building' => '建物１'
        ]);

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        $this->actingAs($user1);

        // 住所を更新（redirect先も設定）
        $response = $this->post('/address/update', [
        'id' => $profile->id,
        'zipcode' => '123-4567',
        'adress' => '北海道',
        'building' => '変更後ビル',
        'redirect' => '/purchase/' . $item->id,
        'payment' => 1,
        'item_id' => $item->id
        ]);

        // リダイレクトを確認（購入画面に戻る）
        $response->assertRedirect('/purchase/' . $item->id . '?zipcode=123-4567&adress=北海道&building=変更後ビル&payment=1');

        // 購入画面にアクセス
        $purchaseResponse = $this->get('/purchase/' . $item->id . '?zipcode=123-4567&adress=北海道&building=変更後ビル');

        // 更新後の住所が表示されているか確認
        $purchaseResponse->assertSee('123-4567');
        $purchaseResponse->assertSee('北海道');
        $purchaseResponse->assertSee('変更後ビル');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
        // ユーザー、商品作成
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

        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        $item = Item::factory()->create([
            'name' => '商品１',
            'user_id' => $user2->id,
        ]);

        $this->actingAs($user1);

        // POST購入処理
        $response = $this->post('/purchase/checkout', [
            'item_id' => $item->id,
            'payment' => 1,
            'zipcode' => '987-6543',
            'adress' => '大阪府大阪市',
            'building' => 'テストビル',
            'item_name' => $item->name,
            'item_price' => $item->price,
        ]);

        // リダイレクト確認（Stripeなどでなければ '/' でOK）
        $response->assertRedirect('/');

        // ordersテーブルに登録されていること
        $this->assertDatabaseHas('orders', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'zipcode' => '987-6543',
            'adress' => '大阪府大阪市',
            'building' => 'テストビル',
        ]);
    }
}
