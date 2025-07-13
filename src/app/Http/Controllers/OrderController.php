<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use App\Models\Trade;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function orderShow($id){

        $loginId = auth()->id();

        // 商品を取得
        $item = Item::findOrFail($id);

        // プロフィールを取得
        $profile = Profile::where('user_id', $loginId)->first() ?? new Profile();
        return view('purchase', compact('item', 'profile'));
    }

    // 注文処理
    public function checkOut(OrderRequest $request){

        // 必要な情報を取得
        $data = array_merge(
            $request->validated(),
            $request->only(['item_id','payment','building','item_user_id'])
        );

        // 取引データ作成
        Trade::create([
            'item_id'               => $data['item_id'],
            'seller_id'             => $data['item_user_id'],
            'buyer_id'              => auth()->id(),
        ]);

        // コンビニ払いの場合は Stripe 処理せずにそのまま注文登録して終了
        if ($data['payment'] == 1) {
            Order::create([
                'item_id'               => $data['item_id'],
                'user_id'               => auth()->id(),
                'payment'               => $data['payment'],
                'zipcode'               => $data['zipcode'],
                'adress'                => $data['adress'],
                'building'              => $data['building'],
            ]);

            return redirect('/')->with('status', '注文が完了しました！（コンビニ払い）');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'product_data' => ['name' => $request->item_name],
                    'unit_amount'  => $request->item_price,
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('purchase.success', $data). '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.cancel'),
        ]);

        // Stripe Checkout ページにリダイレクト
        return redirect($session->url);
    }

    // 決済完了
    public function success(Request $request)
    {
        // StripeからセッションID取得
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session = Session::retrieve($sessionId);

            // 支払いステータス確認
            if ($session->payment_status !== 'paid') {
                // 支払い未完了：エラー
                return redirect('/')->with('status', '支払いが確認できませんでした');
            }
        }

        // ここまできたら支払い成功：ordersテーブルに登録
        Order::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->id(),
            'payment' => $request->payment,
            'zipcode' => $request->zipcode,
            'adress' => $request->adress,
            'building' => $request->building,
            'stripe_payment_intent' => $session->payment_intent,
        ]);

        //indexに戻る
        return redirect('/')->with('status', '注文が完了しました！（カード払い）');
    }

    // キャンセル
    public function cancel()
    {
        return redirect('/')
            ->with('status', '購入をキャンセルしました。');
    }

}
