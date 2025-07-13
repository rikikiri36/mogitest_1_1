@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
@endsection

{{-- 
@section('link')
<a class="header__link" href="/login">login</a>
@endsection
--}}

@section('content')
<div class="address-form">
  <h2 class="content__heading">住所の変更</h2>
  <div class="address-form__inner">
    <form class="address-form__form" action="/address/update" method="post">
      @csrf
      <div class="address-form__group">
        <label class="address-form__label" for="zipcode">郵便番号</label>
        <input class="address-form__input" type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $profile->zipcode) }}">
        <p class="address-form__error-message">
          @error('zipcode')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="address-form__group">
        <label class="address-form__label" for="adress">住所</label>
        <input class="address-form__input" type="text" name="adress" id="adress" value="{{ old('adress', $profile->adress) }}">
        <p class="address-form__error-message">
          @error('adress')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="address-form__group">
        <label class="address-form__label" for="building">建物名</label>
        <input class="address-form__input" type="text" name="building" id="building" value="{{ old('building', $profile->building) }}">
        <p class="address-form__error-message">
          @error('building')
          {{ $message }}
          @enderror
        </p>
      </div>
      <input class="address-form__btn btn__big" type="submit" value="更新する">
      <p>
        <a class="back__link link" href="{{ url('purchase/' . request('item_id')) }}">戻る</a>
      </p>
      <input type="hidden" name="id" id="id" value="{{ old('id', $profile->id) }}">
      <input type="hidden" name="redirect" value="{{ request('redirect') }}">
      <input type="hidden" name="payment" value="{{ request('payment') }}">
      <input type="hidden" name="item_id" value="{{ request('item_id') }}">
    </form>
  </div>
</div>
@endsection('content')