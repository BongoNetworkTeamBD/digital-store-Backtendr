<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Order, Product, ProductKey, Coupon};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryMail;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::with('product')->where('user_id', $request->user()->id)->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'coupon_code' => 'nullable|string'
        ]);
        $user = $request->user();
        $product = Product::findOrFail($data['product_id']);

        return DB::transaction(function () use ($user, $product, $data) {
            $price = $product->price;
            $coupon = null;
            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();
                if ($coupon && $coupon->isValid()) {
                    $price = $coupon->apply($price);
                    $coupon->increment('used_count');
                }
            }
            if ($user->balance < $price) {
                return response()->json(['message' => 'Insufficient balance'], 422);
            }

            $key = ProductKey::where('product_id', $product->id)->whereNull('used_at')->lockForUpdate()->first();
            if (!$key) {
                return response()->json(['message' => 'Out of stock'], 422);
            }

            $user->balance -= $price;
            $user->save();

            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'amount' => $price,
                'coupon_id' => $coupon?->id,
                'status' => 'paid',
                'delivered_at' => Carbon::now()
            ]);

            $key->used_at = Carbon::now();
            $key->save();

            // send email
            Mail::to($user->email)->send(new DeliveryMail($product, $key->key));

            // in-app notification
            $user->notify(new \App\Notifications\GenericMessage('Order Confirmed', 'Your order has been delivered via email.'));

            return $order->load('product');
        });
    }
}
