<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>評価依頼メール</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333;">
  <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px;">
    <p style="font-size: 16px;">
        {{ $sellerName }} さん
    </p>

    <p style="font-size: 16px;">
      coachtechフリマをご利用ありがとうございます。<br>
      下記の取引をした <strong>{{ $buyerName }}</strong> さんがあなたを評価しました。<br>
      マイページの「取引中の商品」から、<strong>{{ $buyerName }}</strong> さんの評価をお願いします。
    </p>

    <hr style="margin: 20px 0;">

    <p style="font-size: 16px;">
      <strong>◾️商品名：</strong><br>
      {{ $itemName }}<br>
      <strong>◾️評価ページ（マイページ）：</strong><br>
      <a href="{{ url('/mypage/trade/' . $tradeId) }}" style="color: #1a73e8;">
        {{ url('/mypage/trade/' . $tradeId) }}
      </a>
    </p>

    <hr style="margin: 20px 0;">

    <p style="font-size: 14px; color: #999;">
      coachtechフリマ
    </p>
  </div>
</body>
</html>