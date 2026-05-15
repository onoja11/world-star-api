<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOrderAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order->load(['items.product', 'user']);
    }

    public function build()
    {
        return $this->subject('[!] INBOUND_ORDER_ALERT // REF_#' . $this->order->id)
                    ->markdown('emails.orders.admin_alert');
    }
}