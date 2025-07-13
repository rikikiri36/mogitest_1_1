<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use App\Models\Trade;
use App\Models\Message;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    // マイページ画面表示 //////
    public function index(Request $request){

        $loginId = auth()->id();
        $tab = $request->query('tab');
        $profile = collect();
        $sells = collect();
        $orders = collect();
        $trades = collect();

        // プロフィール情報
        $profile = Profile::where('user_id', $loginId)->first();

        // 評価
        // 購入者としての評価を配列で取得
        $buyerRanks = Trade::where('buyer_id', $loginId)
        ->Where('seller_completed', 1)
        ->pluck('rank_by_seller');

        // 出品者としての評価を配列で取得
        $sellerRanks = Trade::where('seller_id', $loginId)
        ->Where('buyer_completed', 1)
        ->pluck('rank_by_buyer');

        // 両方を結合
        $allRanks = $buyerRanks->merge($sellerRanks);

        $rank = $allRanks->count() > 0 ? round($allRanks->avg()) : null;

        // 自分宛メッセージの未読取引件数を取得
        $unreadTradeCount = Message::where('receiver_id', $loginId)
        ->where('is_watched', 0)
        ->whereHas('trade', function ($query) use ($loginId) {
            $query->where(function ($q) use ($loginId) {
                $q->where('buyer_id', $loginId)
                  ->where('buyer_completed', 0);
            })->orWhere(function ($q) use ($loginId) {
                $q->where('seller_id', $loginId)
                  ->where('seller_completed', 0);
            });
        })
        ->select('trade_id')
        ->distinct()
        ->count('trade_id');

        // 出品した商品タブ
        if($tab =='sell'){
            // 出品情報
            $sells = Item::where('user_id', $loginId)->paginate(12);
        // 購入した商品タブ
        }elseif($tab == 'buy'){
            // 注文情報
            $orders = Order::with('item')->where('user_id', $loginId)->paginate(12);
        // 取引中の商品タブ
        }else{
            // 取引情報
            $trades = Trade::with('item')
            ->addSelect([                                               // 自分が送信・受信者関係なく、送受信が発生した取引を降順に並べる用に取得 
                'message_at' => Message::selectRaw('MAX(updated_at)')
                    ->whereColumn('trade_id', 'trades.id')
            ])
            ->withCount([                                               // 自分が受信したメッセージの未読件数を取得
                'messages as unread_count' => function ($query) use ($loginId) {
                    $query->where('receiver_id', $loginId)
                        ->where('is_watched', 0);
                }
            ])
            ->where(function ($query) use ($loginId) {
                $query->where(function ($q) use ($loginId) {
                    $q->where('buyer_id', $loginId)
                      ->Where('buyer_completed', 0);
                })
                ->orWhere(function ($q) use ($loginId) {
                    $q->where('seller_id', $loginId)
                      ->Where('seller_completed', 0);
                });
            })
            ->orderByDesc('message_at')
            ->paginate(12);
        }

        return view('mypage/index', compact('profile', 'rank', 'sells', 'orders', 'trades', 'unreadTradeCount'));
    }

    // プロフィール編集画面表示 //////
    public function edit(){

        $loginId = auth()->id();

        // プロフィール情報
        $profile = Profile::where('user_id', $loginId)->first() ?? new Profile();
        return view('mypage/profile', ['profile' => $profile]);
    }

    // 更新処理 //////
    public function update(ProfileRequest $request)
    {
        $loginId = auth()->id();
        $hasprofile = Profile::where('user_id', $loginId)->exists();
        $path = null;

        // 商品画像をアップロード
        if($request->file('image')){
            $path = $request->file('image')->store('images/profiles', 'public');
        }
        // すでにプロフィールが存在する場合はUPDATE
        if($hasprofile){

            $profile = Profile::find($request->id);
            $profile->name = $request->name;
            $profile->zipcode = $request->zipcode;
            $profile->adress  = $request->adress;
            $profile->building = $request->building;

            if ($path) {
                $profile->image = $path;
            }
        
            $profile->save();

        // 存在しない場合はINSERT
        }else{

            $data = [
                'name' => $request->name,
                'user_id' => $loginId,
                'zipcode' => $request->zipcode,
                'adress' => $request->adress,
                'building' => $request->building,
            ];
        
            if ($path) {
                $data['image'] = $path;
            }

            Profile::create($data);
        }
        return redirect('/mypage/profile')->with('status', '設定が完了しました！');
    }

    // 住所編集画面表示 //////
    public function addressEdit(){

        $loginId = auth()->id();
        $profile = collect();
        $sells = collect();
        $orders = collect();

        // プロフィール情報
        $profile = Profile::where('user_id', $loginId)->first() ?? new Profile();
        return view('address', compact('profile'));
    }

    // 住所で入力された値を注文画面へ戻す（プロフィールテーブルは更新しない） //////
    public function AddressUpdate(AddressRequest $request)
    {
        return redirect()->to("/purchase/{$request->item_id}?zipcode={$request->zipcode}&adress={$request->adress}&building={$request->building}&payment={$request->payment}");
    }
}