<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductKey;

class ProductController extends Controller
{
    public function index()
    {
        return Product::withCount(['keys as stock' => function($q){ $q->whereNull('used_at'); }])->get();
    }

    public function show(Product $product)
    {
        $product->loadCount(['keys as stock' => function($q){ $q->whereNull('used_at'); }]);
        return $product;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'category' => 'nullable',
        ]);
        return Product::create($data);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'sometimes',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable',
            'category' => 'nullable',
        ]);
        $product->update($data);
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'deleted']);
    }

    public function importKeys(Request $request, Product $product)
    {
        $data = $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'required|string'
        ]);
        foreach ($data['keys'] as $k) {
            ProductKey::create([
                'product_id' => $product->id,
                'key' => $k,
            ]);
        }
        return response()->json(['message' => 'keys imported', 'count' => count($data['keys'])]);
    }
}
