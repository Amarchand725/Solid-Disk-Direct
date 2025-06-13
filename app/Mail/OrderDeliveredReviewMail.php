<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDeliveredReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $reviewLink;
    public $storeName;

    public function __construct($customerName, $reviewLink, $storeName)
    {
        $this->customerName = $customerName;
        $this->reviewLink = $reviewLink;
        $this->storeName = $storeName;
    }

    public function build()
    {
        return $this->subject('We’d love your feedback on your order!')
                    ->markdown('emails.order.review');
    }
}
