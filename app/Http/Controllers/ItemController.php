<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'model' => 'required|string',
            'logos' => 'required|array',
            'description' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $userFolder = "users/{$userId}/logos";
        $item = Item::where('name', $validated['name'])->first();

        $existingLogos = $item ? json_decode($item->logos, true) : [];
        
        $processedLogos = array_map(function($logo, $index) use ($userFolder, $existingLogos) {
            if (!isset($logo['texture'])) {
                return null;
            }
            
            $texture = stripslashes($logo['texture']);
            
            if (preg_match('#^data:image/\w+;base64,#i', $texture)) {
                $filename = isset($existingLogos[$index]) 
                    ? basename($existingLogos[$index]['texture'])
                    : Str::random(40) . '.jpg';
                    
                $path = "{$userFolder}/{$filename}";
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $texture));
                Storage::disk('public')->put($path, $imageData);
                
                $texture = "/storage/{$path}";
            }

            return [
                'texture' => $texture,
                'position' => $logo['position'],
                'size' => $logo['size'],
                'rotation' => $logo['rotation']
            ];
        }, $validated['logos'], array_keys($validated['logos']));

        $processedLogos = array_filter($processedLogos);

        if ($item) {
            $item->update([
                'model' => $validated['model'],
                'logos' => json_encode($processedLogos),
                'user_id' => $userId,
                'description' => $validated['description'],
            ]);
        } else {
            $item = Item::create([
                'name' => $validated['name'],
                'model' => $validated['model'],
                'logos' => json_encode($processedLogos),
                'user_id' => $userId,
                'description' => $validated['description'],
            ]);
        }

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
