<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\Message;
use App\Http\Requests\MessageRequest;
use Illuminate\Http\Request;
use App\Mail\TradeFinished;
use Illuminate\Support\Facades\Mail;

class TradeController extends Controller
{

    // 取引チャット画面表示 //////
    public function index($id, Request $request){

        $loginId = auth()->id();
        $oterTrades = collect();
        $watchBtn = false;  // 購入者の取引完了するボタン制御用
        $watchModal = false;// 出品者の取引完了モーダル制御用
        $editMessage = null;// 編集モード制御用

        // 受信者であれば未読を既読に更新
        $unwatchedMessages = Message::where('trade_id', $id)
        ->where('receiver_id', $loginId)
        ->where('is_watched', 0)
        ->get();
    
        foreach ($unwatchedMessages as $message) {
            $message->is_watched = 1;
            $message->timestamps = false;
            $message->save();
        }

        // その他取引 取得
        $oterTrades = Trade::with('item')
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
        ->where('id', '<>', $id)
        ->orderByDesc('id')        
        ->get();

        // 取引・プロフィール情報 取得
        $trade = Trade::with('item')->findOrFail($id);
        if ($loginId === $trade->seller_id) {
            // 自分が出品者 → 相手は購入者
            $userType = 'seller';
            $myProfile = $trade->seller->profile;
            $otherProfile = $trade->buyer->profile;
            // 購入者が取引完了していたら取引完了モーダルを表示する
            if ($trade->buyer_completed && !$trade->seller_completed){
                $watchModal =  true;
            }

        } else {
            // 自分が購入者 → 相手は出品者
            $userType = 'buyer';
            $myProfile = $trade->buyer->profile;
            $otherProfile = $trade->seller->profile;
            // 取引が完了していなかったら取引完了ボタンを表示する
            if (!$trade->buyer_completed){
                $watchBtn =  true;
            }
        }

        // 商品情報 取得
        $item = Trade::with('item')->findOrFail($id);

        // チャットメッセージ 取得
        $messages = Message::where('trade_id', $id)
        ->orderBy('created_at')
        ->get();

        // 「編集モード」時、対象のメッセージを取得
        if ($request->has('edit')) {
            $editMessage = Message::find($request->edit);
        }

        return view('mypage/trade', compact('loginId', 'oterTrades','trade','userType', 'myProfile', 'otherProfile', 'watchModal', 'watchBtn', 'messages', 'editMessage'));
    }

    // メッセージ新規作成・編集処理 //////
    public function createUpdate(MessageRequest $request){

        $loginId = auth()->id();
        $path = null;

        if($request->file('image')){
            $path = $request->file('image')->store('images/messages', 'public');
        }

        // 編集用のメッセージIDがある場合は更新
        if ($request->filled('edit_message_id')) {

            $message = Message::find($request->edit_message_id);
            $message->detail = $request->detail;
            if ($path) {
                $message->image = $path;
            }
            $message->save();

            // チャット入力情報のセッションクリア
            session()->forget('detail');

            return redirect('/mypage/trade/' . $request->trade_id . '#message-' . $request->edit_message_id);

        // ない場合は新規作成
        }else{

            $data = [
                'trade_id' => $request->trade_id,
                'sender_id' => $loginId,
                'receiver_id' => $request->receiver_id,
                'detail' => $request->detail,
            ];
        
            if ($path) {
                $data['image'] = $path;
            }
        
            Message::create($data);

            // 一番新しいメッセージを表示するために取得
            $lastMessage = Message::where('trade_id', $request->trade_id)
            ->orderByDesc('id')
            ->first();

            return redirect('/mypage/trade/' . $request->trade_id . '#message-' . $lastMessage->id);
        }
    }

    // チャット削除処理 //////
    public function delete($id){
        Message::find($id)->delete();
        return back()->with('status', 'メッセージを削除しました');
    }

    // 取引完了・評価送信処理 //////
    public function finish(Request $request){

        $tradeId = $request -> trade_id;

        // 購入者の場合
        if($request->userType==="buyer"){

            $trade = Trade::with(['seller.profile', 'buyer.profile', 'item'])->findOrFail($tradeId);

            // 評価更新
            $trade->rank_by_buyer = $request->rating;
            $trade->buyer_completed = 1;
            $trade->save();

            // メール送信に必要な情報を取得
            $sellerEmail = $trade->seller->email;
            $sellerName = $trade->seller->profile->name;
            $buyerName = $trade->buyer->profile->name;
            $itemName = $trade->item->name;

            // メール配信
            Mail::to($sellerEmail)->send(new TradeFinished($tradeId, $sellerName, $buyerName, $itemName));

        // 出品者の場合
        }else{
            $trade = Trade::find($request->trade_id);
            $trade->rank_by_seller = $request->rating;
            $trade->seller_completed = 1;
            $trade->save();
        }

        return redirect('/')->with('status', '評価を送信しました');
    }

    // メッセージ自動保存処理 //////
    public function autosave(Request $request){
        session(['detail' => $request->input('detail')]);
        return response()->json(['status' => 'ok']);
    }

}