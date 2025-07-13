<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    //いいね　をする/解除する（新規作成/削除）
    public function store(Request $request){

        // ログイン時
        if (auth()->check()) {
            $loginId = auth()->id();
            //いいね済なら、削除する
            if($request->hasLiked){  
                Like::where('item_id', $request->item_id)->where('user_id', $loginId)->delete();
            }
            //いいねがまだなら、新規作成する
            else{
                Like::create([
                    'user_id' => $loginId,
                    'item_id' => $request->item_id,
                ]);
            }

            return back();
        }
    }
}
