@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css')}}">
@endsection

@section('search')
<div class="header__search">
  <form action="/" method="GET">
    <div class="search__wrapper">
      <i class="fas fa-search search-icon"></i>
      <input type="text" name="search_item" id="search_item" value="{{request('search_item')}}" class="search__input" placeholder="なにをお探しですか？">        
    </div>
    <input type="hidden" name="tab" value="{{ request('tab') }}">
  </form>
</div>
@endsection

@section('link')
<div class="header__link">
  @if (Auth::check())
    <form action="/logout" method="POST">
      @csrf
      <button class="header-logout__btn">ログアウト</button>
    </form>
  @else
    <a class="header__link link" href="/login">ログイン</a>
  @endif
  <a class="header__link link" href="/login">マイページ</a>
  <a class="header-sell__link link" href="/sell">出品</a>
</div>
@endsection

@section('content')
<div class="item-container">
  <div class="item-left">
    <img src="{{ asset('storage/' . $item->image) }}" class="item-image" alt="{{ $item->name }}">
  </div>
  <div class="item-right">
    <h1 class="item-name">{{ $item->name }}</h1>
    <p class="item-brand">{{ $item->brand }}</p>
    <p class="item-price"><span class="item-price-en">¥</span>{{ number_format($item->price) }}<span class="item-price-zeikomi">(税込)</span></p>
    
    {{--いいね と コメント--}}
    <form action="/like" method="POST">
      @csrf
      <input type="hidden" name="item_id" value="{{ $item->id }}">
      <input type="hidden" name="hasLiked" value="{{ $hasLiked }}">
      <div class="like-container">
        <div class="icon-group">
          <div class="like-group">
            <button type="submit" class="like-icon {{ $hasLiked ? 'active' : '' }}{{ auth()->check() ? '' : 'like-icon-nouser' }}" id="likeIcon" {{ auth()->check() ? '' : 'disabled' }}>
              <i class="fas fa-star"></i>
            </button>
            <span class="like-count" id="likeCount">{{ $likesCount }}</span>
          </div>
          <div class="comment-group">
            <a href="#comment-title" class="comment-icon">
              <i class="fas fa-comment-dots"></i>
            </a>
            <span class="comment-count">{{ $commentsCount }}</span>
          </div>
        </div>
      </div>
    </form>

    {{--売り切れていないなら購入ボタン--}}
    @if (!$orderExist)
      @if (!$hasItemMyselled)
        <form action="/purchase/{{ $item->id }}" method="GET">
           <button class="item__button">購入手続きへ</button>
        </form>
      @else
        <button class="soldout" disabled>あなたが出品した商品です</button>
      @endif
      
    @else
      <button class="soldout" disabled>完売しました</button>
    @endif
    <h2 class="sub-title">商品説明</h2>
    <div class="item-description">{!! $item->description !!}</div>
    <h2 class="sub-title">商品の情報</h2>
    <div class="item-groups">
      <h3 class="label-title">カテゴリー</h3>
      <div class="category-tags">
        @foreach($categories as $category)
          <span class="category-name">{{ $category->category->name }}</span>
        @endforeach
      </div>
    </div>
    <div class="item-groups">
      <h3 class="label-title">商品の状態</h3>
      <div class="item_condition">
      {{ $item->condition->name }}
      </div>
    </div>   
    <p class="comment-title" id="comment-title">コメント ({{ $commentsCount }})</p>
    {{--コメント無し--}}
    @if ($comments->isEmpty())
      <p class="nodata">まだコメントがありません</p>
    @else
      @foreach ($comments as $comment)
        <div class="comment-groups">
          <div class="comment__inner">
            <div class="comment-profile__img-container {{ !$comment->user->profile->image ? 'no-image' : '' }}">
              @if ($comment->user->profile->image)
                <img src="{{ asset('storage/' . $comment->user->profile->image) }}" class="comment-profile__img" alt="プロフィール画像">
              @endif
          </div>
            <p class="comment-profile__name">{{ $comment->user->profile->name }}</p>
          </div>
          <p class="comment-detail">{{ $comment->detail }}</p>
        </div>
      @endforeach
    @endif

    <h3 class="label-title">商品へのコメント</h3>
    <form action="/comment" method="post">
      @csrf
      <textarea class="comment__textarea" placeholder="コメントを入力してください" name="detail" id="detail">{{ old('detail') }}</textarea>
      <p class="comment__error-message">
        @error('detail')
          {{ $message }}
        @enderror
      </p>

      <button class="item__button">コメントを送信する</button>
      <input type="hidden" name="item_id" value="{{ $item->id }}">
    </form>

  </div>
</div>
@endsection('content')

@section('js')
<script>
    // いいねアイコンのクリックイベント
    document.getElementById('likeIcon').addEventListener('click', function() {
      this.classList.toggle('active');
    });
</script>
@endsection('js')

