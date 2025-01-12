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
    
        // If item exists, get existing logos to reuse filenames
        $existingLogos = $item ? json_decode($item->logos, true) : [];
        
        // Process each logo
        $processedLogos = array_map(function($logo, $index) use ($userFolder, $existingLogos) {
            $base64Image = $logo['texture'];
            
            // Reuse existing filename if available, otherwise create new one
            $filename = isset($existingLogos[$index]) 
                ? basename($existingLogos[$index]['texture'])
                : Str::random(40) . '.jpg';
                
            $path = "{$userFolder}/{$filename}";
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            Storage::disk('public')->put($path, $imageData);
    
            return [
                'texture' => "/storage/{$path}",
                'position' => $logo['position'],
                'size' => $logo['size'],
                'rotation' => $logo['rotation']
            ];
        }, $validated['logos'], array_keys($validated['logos']));
    
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
