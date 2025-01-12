<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScreenshotController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'screenshot' => 'required|string',
            'name' => 'required|string'
        ]);

        $userId = Auth::id();
        $userFolder = "users/{$userId}/screenshots";
        $item = Item::where('name', $validated['name'])->first();

        $base64Image = $validated['screenshot'];
        $filename = 'screenshot-' . $validated['name'] . '.png';
        $path = "{$userFolder}/{$filename}";
        
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        Storage::disk('public')->put($path, $imageData);

        if ($item) {
            // Delete old screenshot if exists
            if ($item->screenshot_path) {
                Storage::disk('public')->delete($item->screenshot_path);
            }

            $item->update([
                'screenshot_path' => "/storage/{$path}"
            ]);
        }

        return response()->json([
            'path' => "/storage/{$path}",
            'item' => $item
        ]);
    }
}
