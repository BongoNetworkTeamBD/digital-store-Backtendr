<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code','type','value','start_at','end_at','usage_limit','used_count'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public function isValid(): bool
    {
        $now = Carbon::now();
        if (($this->start_at && $now->lt($this->start_at)) || ($this->end_at && $now->gt($this->end_at))) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function apply($price): float
    {
        if ($this->type === 'fixed') return max(0, $price - $this->value);
        if ($this->type === 'percent') return max(0, round($price * (1 - ($this->value/100)), 2));
        return $price;
    }
}
