<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductKey extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','key','used_at'];
    protected $dates = ['used_at'];

    public function product(){ return $this->belongsTo(Product::class); }
}
