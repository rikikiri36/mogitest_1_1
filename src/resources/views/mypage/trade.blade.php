@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trade.css')}}">
@endsection

@section('content')
@if (session('status'))
  <div class="alert-success">
    {{ session('status') }}
  </div>
@endif

<div class="trade-container">
  <div class="left">
    <div class="title_other-trade">その他の取引</div>
    @foreach($oterTrades as $oterTrade)
      <a class="detail_item-name link" href="{{ url('mypage/trade/' . $oterTrade->id) }}">{{ $oterTrade->item->name }}</a>
    @endforeach
  </div>

  <div class="right">
    <div class="right-child">
      <div class="heaader-profile__img-container {{ !$otherProfile->image ? 'no-image' : '' }}">
        @if ($otherProfile->image)
          <img src="{{ asset('storage/' . $otherProfile->image) }}" class="heaader-profile__img" alt="プロフィール画像">
        @endif
      </div>
      <p class="heaader-profile-name">「{{ $otherProfile->name }}」さんとの取引画面</p>
        @if ($watchBtn)
          <button class="finish-button" onclick="openModal()">取引を完了する</button>
        @endif
    </div>

    <div class="right-child">
      <img src="{{ asset('storage/' . $trade->item->image) }}" class="item-image" alt="{{ $trade->item->name }}"> 
      <div class="item__info">
        <p class="item-name">{{ $trade->item->name }}</p>
        <p class="item-price"><span class="item-price-en">¥</span>{{ number_format($trade->item->price) }}<span class="item-price-zeikomi">(税込)</span></p>
      </div>
    </div>

    <form class="right-child-chat" action="/mypage/trade/create" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="chat-box">
        @foreach ($messages as $message)
            <div id="message-{{ $message->id }}" class="chat-message-wrapper {{ $message->sender_id === $loginId ? 'sender' : 'receiver' }}">
              <div class="chat-user-info">
                @if ($message->sender_id === $loginId)
                  @if ($myProfile->image)
                    <img src="{{ asset('storage/' . $myProfile->image) }}" class="chat-profile-img" alt="プロフィール画像">
                  @else
                    <div class="chat-profile-img no-image"></div>
                  @endif
                  <span class="chat-user-name">{{ $myProfile->name }}</span>
                @else
                  @if ($otherProfile->image)
                    <img src="{{ asset('storage/' . $otherProfile->image) }}" class="chat-profile-img" alt="プロフィール画像">
                  @else
                    <div class="chat-profile-img no-image"></div>
                  @endif
                  <span class="chat-user-name">{{ $otherProfile->name }}</span>
                @endif
              </div>
              <div class="chat-message {{ (!empty($editMessage) && $editMessage->id === $message->id) ? 'editing' : '' }}">{{ $message->detail }}</div>
              @if (!empty($message->image))
                <img src="{{ asset('storage/' . $message->image) }}" class="chat-image" alt="メッセージ画像">
              @endif
              @if ($message->sender_id === $loginId)
                <div class="chat-actions">
                  <a href="{{ url('/mypage/trade/' . $trade->id) . '?edit=' . $message->id . '#message-' . $message->id }}" class="chat-action-link">編集</a>
                  <a href="{{ url('/mypage/trade/delete/' . $message->id) }}" class="chat-action-link">削除</a>
                </div>
              @endif
            </div>
            <input type="hidden" name="message_id" value="{{ $message->id }}">
        @endforeach
      </div>
      <input type="hidden" name="trade_id" value="{{ $trade->id }}">
      <input type="hidden" name="receiver_id" value="{{ $otherProfile->user_id }}">
      <input type="hidden" name="edit_message_id" value="{{ $editMessage->id ?? '' }}">
      <div class="chat-footer">
        <div class="chat-error-wrapper">
          <p class="error-message">
            @error('detail')
              {{ $message }}
            @enderror
            @error('image')
              {{ $message }}</p>
            @enderror
          </p>
        </div>
        <div class="chat-input-wrapper">
          <input type="text" name="detail" id="detail" class="chat-input" placeholder="取引メッセージを記入してください" value="{{ old('detail', $editMessage->detail ?? session('detail', '')) }}">
          <label for="imageInput" class="chat-input-image-wrapper">
            <span class="chat-input-image">画像を追加</span>
            <span class="image-badge" id="image-badge"></span>
          </label>
          <input type="file" name="image" id="imageInput" style="display: none;">
          <button class="send-button" type="submit"><img class="send-button" src="{{ asset('send.jpg') }}" alt="送信"></button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- 取引完了モーダル --}}
<div id="finishModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-child">
      <p class="modal-title">取引が完了しました。</p>
    </div>
      <form action="/mypage/trade/finish" method="POST">
      @csrf      
        <p class="modal-text">今回の取引相手はどうでしたか？</p>
        <div class="star-rating-wrapper">
          <div class="star-rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
            <input type="hidden" name="rating" id="rating" value="0">
          </div>
        </div>

      <div class="modal-actions">
        <button type="submit" class="ranksend-button">送信する</button>
      </div>
      <input type="hidden" name="trade_id" value="{{ $trade->id }}">
      <input type="hidden" name="userType" value="{{ $userType }}">
    </form>
  </div>
</div>
@endsection('content')
@section('js')
<script>
  // 取引完了ボタン押下時にモーダル表示
  function openModal() {
    document.getElementById('finishModal').style.display = 'flex';
  }

  document.addEventListener('DOMContentLoaded', function () {
    const watchModal = @json($watchModal);
    if (watchModal) {
      document.getElementById('finishModal').style.display = 'flex';
    }
  });

  // 取引完了モーダルの星タップ時
  document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        const rating = index + 1;
        ratingInput.value = rating;

        stars.forEach((s, i) => {
          if (i < rating) {
            s.classList.add('selected');
          } else {
            s.classList.remove('selected');
          }
        });
      });
    });
  });

  // 画像追加時にバッジを表示
  document.getElementById('imageInput').addEventListener('change', function (event) {
    const badge = document.getElementById('image-badge');
    if (event.target.files.length > 0) {
      badge.style.display = 'block';
    } else {
      badge.style.display = 'none';
    }
  });

  // 編集モードで画像があればバッジ表示
  @if (!empty($editMessage) && !empty($editMessage->image))
    document.getElementById('image-badge').classList.add('show');
  @endif

  // メッセージの入力値をセッションに保持
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('detail');
    let timeoutId;

    input.addEventListener('input', function () {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        const value = input.value;
        const token = document.querySelector('input[name="_token"]').value;

        fetch('{{ route("mypage.trade.autosave") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
          },
          body: JSON.stringify({ detail: value })
        });
      }, 500);
    });
  });

</script>
@endsection
