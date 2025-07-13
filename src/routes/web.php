<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TradeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 決済完了・キャンセル
Route::get('/purchase/success', [OrderController::class, 'success'])
     ->name('purchase.success');
Route::get('/purchase/cancel',  [OrderController::class, 'cancel'])
     ->name('purchase.cancel');

// Checkout 処理
Route::post('/purchase/checkout', [OrderController::class, 'checkOut'])
     ->middleware('auth');

// 購入画面表示
Route::get('/purchase/{id}', [OrderController::class, 'orderShow'])
     ->middleware('auth');


// ホーム
Route::get('/', [ItemController::class, 'index']);

// 商品詳細
Route::get('/item/{id}', [ItemController::class, 'show']);


// マイページトップ
Route::get('/mypage', [ProfileController::class, 'index'])
     ->middleware('auth');

// プロフィール編集
Route::get('/mypage/profile', [ProfileController::class, 'edit'])
     ->middleware('auth');

// プロフィール更新
Route::post('/profile/update', [ProfileController::class, 'update'])
     ->middleware('auth');


// 住所編集画面
Route::get('/address', [ProfileController::class, 'addressEdit'])
     ->middleware('auth');

// 住所更新
Route::post('/address/update', [ProfileController::class, 'AddressUpdate'])
     ->middleware('auth');


// いいね
Route::post('/like',    [LikeController::class,    'store'])
     ->middleware('auth');

// コメント
Route::post('/comment', [CommentController::class, 'create'])
     ->middleware('auth');


// 出品フォーム表示
Route::get('/sell',         [ItemController::class, 'sellEdit'])
     ->middleware('auth');

// 出品処理
Route::post('/sell/store',  [ItemController::class, 'store'])
     ->middleware('auth');

// 取引チャット画面
Route::get('/mypage/trade/{id}', [TradeController::class, 'index'])
     ->middleware('auth');

// 取引チャットメッセージ自動保存処理
Route::post('/mypage/trade/autosave', [TradeController::class, 'autosave'])
     ->name('mypage.trade.autosave');

// チャットメッセージ新規作成・編集処理
Route::post('/mypage/trade/create', [TradeController::class, 'createUpdate'])
     ->middleware('auth');

// チャットメッセージ削除処理
Route::get('/mypage/trade/delete/{id}', [TradeController::class, 'delete'])
     ->middleware('auth');

// 取引完了処理
Route::post('/mypage/trade/finish', [TradeController::class, 'finish'])
     ->middleware('auth');