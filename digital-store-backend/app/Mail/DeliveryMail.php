<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;

class DeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public Product $product;
    public string $license;

    public function __construct(Product $product, string $license)
    {
        $this->product = $product;
        $this->license = $license;
    }

    public function build()
    {
        return $this->subject('Your digital product')
            ->view('emails.delivery');
    }
}
