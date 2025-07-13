<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\ItemCategory;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class ItemController extends Controller
{
    // 一覧表示 //////////////////
    public function index(Request $request){
        // 初期値として空のコレクションを設定
        $items = collect();  

        // マイリストタブで未ログイン時は即座にreturn
        if (!empty($request->tab) && !auth()->check()) {
            return view('index', ['items' => $items]);
        }

        // 検索条件の有無をチェック
        $hasSearch = $request->has('search_item') && !empty($request->search_item);

        // おすすめタブ /////////
        if (empty($request->tab)) {
            $query = Item::with('order');

                // ログイン済は自分が出品した商品を除外
                if (auth()->check()) {
                    $query->where('user_id', '<>', auth()->id())->orderBy('created_at', 'desc');
                }

                if ($hasSearch) {
                    $query->where('name', 'like', '%' . $request->search_item . '%')->orderBy('created_at', 'desc');
                }

                $items = $query->paginate(12);

        // マイリストタブ（ログイン済みの場合）
        }else {
            $query = Like::with('item.order')->where('user_id', auth()->id());
    
            if ($hasSearch) {
                $query->whereHas('item', function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search_item . '%')->orderBy('created_at', 'desc');
                });
            }
    
            $items = $query->paginate(12);
        }
    
        return view('index', ['items' => $items]);
    }

    // 詳細表示 //////////////////
    public function show($id){
        $comments = collect();
        $commentsCount = 0;
        $likesCount = 0;
        $orderExist = false;
        $hasLiked = false;
        $hasItemMyselled = false;

        // 商品とコンディション名称を取得
        $item = Item::with('condition')->findOrFail($id);
        // 商品の説明に改行を反映
        $item->description = nl2br(e($item->description));
        
        // 商品に紐づくカテゴリーをすべて取得
        $categories = ItemCategory::with('category')->where('item_id', $id)->get();

        // (ログイン時のみ) 
        if (auth()->check()) {
            $loginId = auth()->id();

            // ログインユーザーがいいねをしたか？
            $hasLiked = Like::where('item_id', $id)->where('user_id', $loginId)->exists();
            // ログインユーザーが出品した商品か？
            $hasItemMyselled = Item::where('id', $id)->where('user_id', $loginId)->exists();
        }

        // いいね数を取得
        $likesCount = Like::where('item_id', $id)->count();
        
        // コメントを取得（最新順）
        $comments = Comment::with('user.profile')->where('item_id', $id)->orderBy('created_at', 'desc')->get();

        // コメント数を取得
        $commentsCount = $comments->count();
    
        // 購入済みかチェック（購入ボタン非活性用）
        $orderExist = Order::where('item_id', $id)->exists();

        return view('item', compact('item', 'categories', 'hasLiked', 'likesCount', 'comments', 'commentsCount', 'orderExist', 'hasItemMyselled'));
    }

    // 商品出品表示 //////////////////
    public function sellEdit(){

        // すべてのカテゴリーを取得
        $categories = Category::all();

        // すべての商品の状態を取得
        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }

    // 商品登録処理 //////////////////
    public function store(ItemRequest $request){
        // dd($request->all());
        $loginId = auth()->id();

        if (App::environment('testing')) {
            // テスト環境なら固定画像パス
            $path = 'test.png';
        } else {
            // 本番・ローカル環境では画像アップロード処理を行う
            $path = $request->file('image')->store('images/items', 'public');
        }
        
        // Itemsテーブルに登録
        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $path,
            'condition_id' => $request->condition_id,
            'brand' => $request->brand,
            'description' => $request->description,
            'user_id' => $loginId,
        ]);


        // Item_categoriesテーブルに登録
        foreach ($request->categories as $categoryId) {
            ItemCategory::create([
                'item_id' => $item->id,
                'category_id' => $categoryId,
            ]);
        }
        return redirect('/sell')->with('status', '出品が完了しました！');
    }
}