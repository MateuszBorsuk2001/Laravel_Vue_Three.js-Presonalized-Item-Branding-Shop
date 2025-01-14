<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Basket;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function index()
    {
        $basketItems = Basket::where('user_id', Auth::id())
            ->with('item')
            ->get();
            
        return response()->json($basketItems);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $item = Item::where('name', $validated['name'])->firstOrFail();

        $existingItem = Basket::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $validated['quantity'];
            $existingItem->save();
            return response()->json($existingItem);
        }

        $basketItem = Basket::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'quantity' => $validated['quantity']
        ]);

        return response()->json($basketItem, 201);
    }
    public function destroy($id)
    {
        $basketItem = Basket::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $basketItem->delete();

        return response()->json(null, 204);
    }
}
