@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
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
  <a class="header__link link" href="/login">マイページ</a>
  <a class="header-sell__link link" href="/login">出品</a>
</div>
@endsection

@section('content')
<form  class="item-container" action="/purchase/checkout" method="POST">
  @csrf
  <div class="left">
    <div class="left-child">
      <div class="left-child-left">
        <img src="{{ asset('storage/' . $item->image) }}" class="item-image" alt="{{ $item->name }}">
      </div>
      <div class="left-child-right">
        <p class="item-name">{{ $item->name }}</p>
        <p class="item-price"><span class="item-price-en">¥</span>{{ number_format($item->price) }}<span class="item-price-zeikomi">(税込)</span></p>
      </div>
    </div>
    <div class="left-child">
      <div class="payment-container">
        <label class="left-title">支払い方法</label>
        <div class="select-wrapper">
          <select id="payment" name="payment" class="item-payment">
              <option value="">選択してください</option>
              <option value="1" {{ old('payment', request('payment')) == '1' ? 'selected' : '' }}>コンビニ払い</option>
              <option value="2" {{ old('payment', request('payment')) == '2' ? 'selected' : '' }}>カード払い</option>
          </select>
        </div>
        <p class="order-error-message">
          @error('payment')
            {{ $message }}
          @enderror
        </p>
      </div>
    </div>
    <div class="left-child delivery-block">
      <div class="delivery-container">
        <label class="left-title">配送先</label>
        <a class="address__link link" href="/address?redirect=/purchase/{{ $item->id }}&payment={{ old('payment', request('payment')) }}&item_id={{ $item->id }}">変更する</a>
      </div>
      @if (!$profile->id)
        <span class="profile-address-nodata">ー</span>
      @else
        <p class="profile-address">
        {{ request('zipcode') ?? $profile->formatted_zipcode }}
        </p>
        <p class="profile-address">
        {{ request('adress') ?? $profile->adress }} {{ request('building') ?? $profile->building }}
        </p>
        <input type="hidden" name="zipcode" value="{{ request('zipcode', $profile->zipcode) }}">
        <input type="hidden" name="adress" value="{{ request('adress', $profile->adress) }}">
        <input type="hidden" name="building" value="{{ request('building', $profile->building) }}">
        <input type="hidden" name="item_name" value="{{ $item->name }}">
        <input type="hidden" name="item_price" value="{{ $item->price }}">
        <input type="hidden" name="item_user_id" value="{{ $item->user_id }}">
      @endif
      <p class="order-error-message">
        @error('zipcode')
          {{ $message }}
        @enderror
      </p>
      <p class="order-error-message">
        @error('adress')
          {{ $message }}
        @enderror
      </p>
    </div>
  </div>
  <div class="right">
      <table class="order-table">
        <tr class="order-table-row">
          <td>商品代金</td>
          <td><span class="item-price-en">¥</span>{{ number_format($item->price) }}<span class="item-price-zeikomi">(税込)</span></td>
        </tr>
        <tr class="order-table-row">
          <td>支払い方法</td>
          <td id="paymentMethodCell"></td>
        </tr>
      </table>
      <button class="item__button">購入する</button>
      <input type="hidden" name="item_id" value="{{ $item->id }}">
    </form>
  </div>
</div>
@endsection('content')
@section('js')
<script>
  // 選択された支払い方法を右に反映する
  document.addEventListener('DOMContentLoaded', function() {
  const paymentSelect = document.getElementById('payment');
  const paymentMethodCell = document.getElementById('paymentMethodCell');

  // 初期値の表示（ページロード時）
  if (paymentSelect.selectedIndex > 0) {
    paymentMethodCell.textContent = paymentSelect.options[paymentSelect.selectedIndex].text;
  }

  // 値が変更されたら、表示を更新
  paymentSelect.addEventListener('change', function() {
    paymentMethodCell.textContent = paymentSelect.options[this.selectedIndex].text;
  });
});
</script>
@endsection('js')
