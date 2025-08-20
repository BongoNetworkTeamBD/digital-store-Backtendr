<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;

class WalletController extends Controller
{
    // Mock add money (admin gateways can verify and then call this)
    public function add(Request $request)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:1']);
        $user = $request->user();
        $user->balance += $data['amount'];
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'type' => 'credit',
            'meta' => ['reason' => 'manual_add']
        ]);

        $user->notify(new \App\Notifications\GenericMessage('Add Money Success', 'Your wallet has been credited.'));
        return response()->json(['message' => 'Balance added', 'balance' => $user->balance]);
    }

    public function adminAdjust(Request $request, User $user)
    {
        $data = $request->validate(['amount' => 'required|numeric']);
        $user->balance += $data['amount'];
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'type' => $data['amount'] >= 0 ? 'credit' : 'debit',
            'meta' => ['reason' => 'admin_adjust']
        ]);

        return response()->json(['message' => 'User balance updated', 'balance' => $user->balance]);
    }

    public function transactions(Request $request)
    {
        return Transaction::where('user_id', $request->user()->id)->latest()->get();
    }
}
