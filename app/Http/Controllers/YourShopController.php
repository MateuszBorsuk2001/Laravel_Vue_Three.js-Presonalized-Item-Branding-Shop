<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class YourShopController extends Controller
{
    public function index()
    {
        $products = Product::with('image')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->image->description,
                'image_url' => asset('storage/' .$product->image->url)
            ];
        });

        return Inertia::render('YourShop', [
            'products' => $products
        ]);
    }
}
