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
{{--設定完了メッセージ--}}
@if (session('status'))
  <div class="alert-success">
    {{ session('status') }}
  </div>
@endif
<div class="profile-form">
  <h2 class="content__heading">プロフィール設定</h2>
  <div class="profile-form__inner">
    <form class="profile-form__form" action="/profile/update" method="post" enctype="multipart/form-data">
      @csrf
      <div class="profile-form__group first_label">
        <div class="image-upload-row">
          <div class="profile__img-container">
            <img id="preview" class="preview-img {{ !empty($profile->image) ? 'show' : '' }}" src="{{ !empty($profile->image) ? asset('storage/' . $profile->image) : '' }}">
          </div>
          <label for="imageInput" class="profile-form__img__button">画像を選択</label>
        </div>
        <input type="file" name="image" id="imageInput" class="hidden-file-input">
        <p class="profile-form__error-message">
          @error('image')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="profile-form__group">
        <label class="profile-form__label" for="name">ユーザー名</label>
        <input class="profile-form__input" type="text" name="name" id="name" value="{{ old('name', $profile->name) }}">
        <p class="profile-form__error-message">
          @error('name')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="profile-form__group">
        <label class="profile-form__label" for="zipcode">郵便番号</label>
        <input class="profile-form__input" type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $profile->zipcode) }}">
        <p class="profile-form__error-message">
          @error('zipcode')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="profile-form__group">
        <label class="profile-form__label" for="adress">住所</label>
        <input class="profile-form__input" type="text" name="adress" id="adress" value="{{ old('adress', $profile->adress) }}">
        <p class="profile-form__error-message">
          @error('adress')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="profile-form__group">
        <label class="profile-form__label" for="building">建物名</label>
        <input class="profile-form__input" type="text" name="building" id="building" value="{{ old('building', $profile->building) }}">
        <p class="profile-form__error-message">
          @error('building')
          {{ $message }}
          @enderror
        </p>
      </div>
      <input class="profile-form__btn btn__big" type="submit" value="更新する">
      <input type="hidden" name="id" id="id" value="{{ $profile->id }}">
    </form>
  </div>
</div>
@endsection('content')
@section('js')
<script>
  document.getElementById('imageInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.add('show');
      };

      reader.readAsDataURL(file);
    }
  });
</script>
@endsection