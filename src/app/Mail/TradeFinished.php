<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeFinished extends Mailable
{
    use Queueable, SerializesModels;

    public $tradeId;
    public $sellerName;
    public $buyerName;
    public $itemName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tradeId, $sellerName, $buyerName, $itemName)
    {
        $this->tradeId = $tradeId;
        $this->sellerName = $sellerName;
        $this->buyerName = $buyerName;
        $this->itemName = $itemName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('購入者に評価されました')   // タイトル 
            ->view('mails.finish')                        // 本文
            ->with([
                'tradeId' => $this->tradeId,
                'sellerName' => $this->sellerName,
                'buyerName' => $this->buyerName,
                'itemName' => $this->itemName,
            ]);
    }
}
