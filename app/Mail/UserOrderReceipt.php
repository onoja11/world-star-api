<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserOrderReceipt extends Mailable
{
    use SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order->load('items.product');
    }

    public function build()
    {
        return $this->subject('ACQUISITION_RECEIPT // REF_#' . $this->order->id)
                    ->markdown('emails.orders.receipt');
    }
}