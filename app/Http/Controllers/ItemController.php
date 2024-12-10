<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'model' => 'required|string',
            'logos' => 'required|array'
        ]);

        $item = Item::create([
            'name' => $validated['name'],
            'model' => $validated['model'],
            'logos' => json_encode($validated['logos']),
            'user_id' => auth()->id()
        ]);

        return response()->json($item);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        $item->logos = json_decode($item->logos);
        return response()->json($item);
    }
}
