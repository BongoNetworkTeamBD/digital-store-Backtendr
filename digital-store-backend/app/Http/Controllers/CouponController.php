<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index()
    {
        return Coupon::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1'
        ]);
        return Coupon::create($data);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'type' => 'in:percent,fixed',
            'value' => 'numeric|min:0',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1'
        ]);
        $coupon->update($data);
        return $coupon;
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['message' => 'deleted']);
    }

    public function validateCode(Request $request)
    {
        $data = $request->validate(['code' => 'required']);
        $coupon = Coupon::where('code', $data['code'])->first();
        if (!$coupon) return response()->json(['valid' => false], 200);

        $now = Carbon::now();
        if (($coupon->start_at && $now->lt($coupon->start_at)) || ($coupon->end_at && $now->gt($coupon->end_at))) {
            return response()->json(['valid' => false, 'reason' => 'expired'], 200);
        }
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['valid' => false, 'reason' => 'limit reached'], 200);
        }
        return response()->json(['valid' => true, 'coupon' => $coupon]);
    }
}
