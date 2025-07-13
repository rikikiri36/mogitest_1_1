<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function create(CommentRequest $request){

        $loginId = auth()->id();

        Comment::create([
            'item_id' => $request->item_id,
            'user_id' => $loginId,
            'detail' => $request->detail,
        ]);

        return redirect()->back();
    }
}
