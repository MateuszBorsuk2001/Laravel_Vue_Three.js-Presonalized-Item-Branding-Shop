<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function getProduct($id): JsonResponse
    {

        $product = Product::with('image')->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description'=>$product->image->description,
            'image_url' => asset($product->image->path)  
        ]);
    }
    
}
