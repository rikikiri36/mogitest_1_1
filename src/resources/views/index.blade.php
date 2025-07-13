@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
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
{{--注文完了メッセージ--}}
@if (session('status'))
  <div class="alert-success">
    {{ session('status') }}
  </div>
@endif

<div class="toppage-list">
  {{--検索BOXに入力があれば、search?search_item=入力値--}}
  <a href="{{ request('search_item') ? '?search_item=' . request('search_item') : '/' }}" class="toppage-list__tab link {{ request('tab') == null ? 'active' : '' }}">おすすめ</a>
  <a href="{{ request('search_item') ? '?tab=mylist&search_item=' . request('search_item') : '/?tab=mylist' }}" class="toppage-list__tab link {{ request('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

{{--おすすめ--}}
@if (request('tab') === '' || !request('tab'))
{{--データ無し--}}
  @if ($items->isEmpty())
    <p class="nodata">商品がありません</p>
  @else     
    <div class="item-list">
      @foreach($items as $item)
        <div class="item-group">
          @if ($item->order)
            <div class="sold-card">
              <span>Sold</span>
            </div>
          @endif
          <a href="{{ url('item/' . $item->id) }}">
            <img src="{{ asset('storage/' . $item->image) }}" class="item-image" alt="{{ $item->name }}">
          </a>
          <p class="item-name">{{ $item->name }}</p>
        </div>
      @endforeach
    </div>
    {{ $items->appends(request()->query())->links('vendor.pagination.custom') }}
  @endif
{{--マイリスト--}}
@else
  {{--ログイン済--}}
  @if (Auth::check())
    {{--データ無し--}}
    @if ($items->isEmpty())
      <p class="nodata">商品がありません</p>
    @else
      <div class="item-list">
        @foreach($items as $item)
          <div class="item-group">
            @if ($item->item && $item->item->order)
              <div class="sold-card">
                <span>Sold</span>
              </div>
            @endif
            <a href="{{ url('item/' . $item->item->id) }}">
              <img src="{{ asset('storage/' . $item->item->image) }}" class="item-image" alt="{{ $item->item->name }}">
            </a>
            <p class="item-name">{{ $item->item->name }}</p>
          </div>
        @endforeach
      </div>
      {{ $items->appends(request()->query())->links('vendor.pagination.custom') }}
    @endif
  @endif
@endif
@endsection('content')