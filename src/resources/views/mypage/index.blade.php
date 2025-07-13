@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css')}}">
@endsection

@section('search')
<div class="header__search">
  <form action="/" method="GET">
    <div class="search__wrapper">
      <i class="fas fa-search search-icon"></i>
      <input type="text" name="search_item" id="search_item" value="{{request('search_item')}}" class="search__input" placeholder="なにをお探しですか？">        
    </div>
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
  <a class="header__link link" href="/mypage?tab=sell">マイページ</a>
  <a class="header-sell__link link" href="/sell">出品</a>
</div>
@endsection

@section('content')
{{--プロフィール情報--}}
@if ($profile)
<div class="mypage-profile">
  <div class="mypage-profile__img-container {{ !$profile->image ? 'no-image' : '' }}">
    @if ($profile->image)
      <img src="{{ asset('storage/' . $profile->image) }}" class="mypage-profile__img" alt="プロフィール画像">
    @endif
  </div>
  <div class="mypage-profile__info">
    <p class="mypage-profile__name">{{ $profile->name }}</p>
    <p class="mypage-profile__rank">
      @for ($i = 0; $i < 5; $i++)
        @if ($i < $rank)
          <span class="star-yellow">&#9733;</span>
        @else
          <span class="star-gray">&#9733;</span>
        @endif
      @endfor
    </p>
  </div>
  <a href="mypage/profile" class="mypage-profile__link">プロフィールを編集</a>
</div>
@else
<div class="mypage-profile">
  <a href="mypage/profile" class="mypage-profile__link link">プロフィールを登録</a>
</div>
@endif

<div class="mypage-list">
  {{--検索BOXに入力があれば、search?search_item=入力値--}}
  <a href="/mypage?tab=sell" class="mypage-list__tab link {{ request('tab') == 'sell' ? 'active' : '' }}">出品した商品</a>
  <a href="/mypage?tab=buy" class="mypage-list__tab link {{ request('tab') == 'buy' ? 'active' : '' }}">購入した商品</a>
  <span>
    <a href="/mypage?tab=trade" class="mypage-list__tab link {{ request('tab') == 'trade' ? 'active' : '' }}">取引中の商品</a>
    @if($unreadTradeCount > 0)
      <span class="trade-badge">{{ $unreadTradeCount }}</span>
    @endif
  </span>
</div>

{{--出品した商品--}}
@if (request('tab') === 'sell')
  {{--データ無し--}}
  @if ($sells->isEmpty())          
    <p class="nodata">出品した商品がありません</p>
  @else
    <div class="item-list">
      @foreach($sells as $sell)
        <div class="item-group">
          <a href="{{ url('item/' . $sell->id) }}">
            <img src="{{ asset('storage/' . $sell->image) }}" class="item-image" alt="{{ $sell->name }}">
          </a>
          <p class="item-name">{{ $sell->name }}</p>
         </div>
      @endforeach
    </div>
    {{ $sells->appends(request()->query())->links('vendor.pagination.custom') }}
  @endif
{{--購入した商品--}}
@elseif (request('tab') === 'buy')
  {{--データ無し--}}
  @if ($orders->isEmpty())
    <p class="nodata">購入した商品がありません</p>
  @else
    <div class="item-list">
      @foreach($orders as $order)
        <div class="item-group">
          <a href="{{ url('item/' . $order->item->id) }}">
            <img src="{{ asset('storage/' . $order->item->image) }}" class="item-image" alt="{{ $order->item->name }}">
          </a>
          <p class="item-name">{{ $order->item->name }}</p>
        </div>
      @endforeach
    </div>
    {{ $orders->appends(request()->query())->links('vendor.pagination.custom') }}
  @endif
{{--取引中の商品--}}
@else
  {{--データ無し--}}
  @if ($trades->isEmpty())
    <p class="nodata">取引中の商品がありません</p>
  @else
    <div class="item-list">
      @foreach($trades as $trade)
        <div class="item-group">
          @if ($trade->unread_count > 0)
            <div class="message-badge">
              <span>{{ $trade->unread_count }}</span>
            </div>
          @endif
          <a href="{{ url('/mypage/trade/' . $trade->id) }}">
            <img src="{{ asset('storage/' . $trade->item->image) }}" class="item-image" alt="{{ $trade->item->name }}">
          </a>
          <p class="item-name">{{ $trade->item->name }}</p>
        </div>
      @endforeach
    </div>
    {{ $trades->appends(request()->query())->links('vendor.pagination.custom') }}
  @endif
@endif
@endsection('content')

