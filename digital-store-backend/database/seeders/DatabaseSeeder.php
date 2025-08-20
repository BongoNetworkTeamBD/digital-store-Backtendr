<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Product, ProductKey, Coupon};
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'balance' => 0
        ]);
        User::updateOrCreate(['email' => 'user@example.com'], [
            'name' => 'User',
            'password' => Hash::make('password'),
            'role' => 'user',
            'balance' => 100
        ]);

        $p = Product::create([
            'name' => 'ChatGPT Plus 1-Month',
            'price' => 20.00,
            'category' => 'AI',
            'description' => '1-month subscription code'
        ]);

        foreach (['CODE-AAA-111','CODE-BBB-222','CODE-CCC-333'] as $k) {
            ProductKey::create(['product_id' => $p->id, 'key' => $k]);
        }

        Coupon::create(['code' => 'NEW10','type' => 'percent','value' => 10,'start_at' => Carbon::now()->subDay()]);
        Coupon::create(['code' => 'FLAT5','type' => 'fixed','value' => 5,'start_at' => Carbon::now()->subDay()]);
    }
}
