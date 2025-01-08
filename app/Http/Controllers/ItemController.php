<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string',
            'model' => 'string',
            'logos' => 'array',
            'logos.*.texture' => 'string',
            'logos.*.position.x' => 'numeric',
            'logos.*.position.y' => 'numeric',
            'logos.*.size.width' => 'numeric',
            'logos.*.size.height' => 'numeric',
            'logos.*.rotation' => 'numeric'
        ]);
        
        if (!Auth::check()) {
            return response()->json(['message' => 'User must be authenticated'], 401);
        }
    
        // First try to find existing item
        $item = Item::where('name', $validated['name'])->first();
    
        if ($item) {
            // Update existing item
            $item->update([
                'model' => $validated['model'],
                'logos' => json_encode($validated['logos']),
                'user_id' => Auth::id()
            ]);
        } else {
            // Create new item
            $item = Item::create([
                'name' => $validated['name'],
                'model' => $validated['model'],
                'logos' => json_encode($validated['logos']),
                'user_id' => Auth::id()
            ]);
        }
    
        return response()->json($item);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        $item->logos = json_decode($item->logos);
        return response()->json($item);
    }

    public function getLatest()
{
    $item = Item::latest()->first();
    if ($item) {
        $item->logos = json_decode($item->logos);
    }
    return response()->json($item);
}

}
