@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css')}}">
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
{{--出品完了メッセージ--}}
@if (session('status'))
  <div class="alert-success">
    {{ session('status') }}
  </div>
@endif
<div class="item-form">
  <h2 class="content__heading">商品の出品</h2>
  <div class="item-form__inner">
    <form class="item-form__form" action="/sell/store" method="post" enctype="multipart/form-data">
      @csrf
      <div class="item-form__group">
        <label class="item-form__label first_label" for="name">商品画像</label>
        <div class="image-upload-row">
          <div class="item__img-container">
            <img id="preview" class="preview-img" />
          </div>
          <label for="imageInput" class="item-form__img__button">画像を選択</label>
        </div>
        <input type="file" name="image" id="imageInput" class="hidden-file-input">
      </div>
      <p class="item-form__error-message">
          @error('image')
          {{ $message }}
          @enderror
      </p>

      <h3 class="item_form__biglabel">商品の詳細</h3>
      <div class="item-form__group">
        <label class="item-form__label" for="name">カテゴリー</label>

        <div class="item-form__category-tags">
        @foreach($categories as $category)
        <label class="category-checkbox">
          <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
          <span class="category-name">{{ $category->name }}</span>
        </label>
        @endforeach
        </div>
        <p class="item-form__error-message">
          @error('categories')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="item-form__group">
        <label class="item-form__label" for="condition">商品の状態</label>
        <div class="select-wrapper">
          <select class="item-condition" name="condition_id">
            <option value=""  {{ old('condition_id') == '' ? 'selected' : '' }}>選択してください</option>
            @foreach($conditions as $condition)
            <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
              {{$condition->name }}
            </option>
            @endforeach
          </select>
        </div>
        <p class="item-form__error-message">
          @error('condition_id')
          {{ $message }}
          @enderror
        </p>
      </div>

      <h3 class="item_form__biglabel">商品名と説明</h3>
      <div class="item-form__group">
        <label class="item-form__label" for="item_name">商品名</label>
        <input class="item-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
          <p class="item-form__error-message">
            @error('name')
            {{ $message }}
            @enderror
          </p>
      </div>

      <div class="item-form__group">
        <label class="item-form__label" for="brand">ブランド名</label>
        <input class="item-form__input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
        <p class="item-form__error-message">
          @error('brand')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="item-form__group">
        <label class="item-form__label" for="description">商品の説明</label>
        <textarea class="item-form__textarea" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
        <p class="item-form__error-message">
          @error('description')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="item-form__group">
        <label class="item-form__label" for="price">販売価格</label>
        <div class="yen-input-wrapper">
          <span class="yen-symbol">¥</span>
          <input class="item-form__input input_price" type="text" name="price" id="price" value="{{ old('price') }}">
        </div>
        <p class="item-form__error-message">
          @error('price')
          {{ $message }}
          @enderror
        </p>
      </div>

      <input class="item-form__btn btn__big" type="submit" value="出品する">
      
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