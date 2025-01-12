<?php

namespace App\Http\Controllers;
use App\Models\Item;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function index(Request $request)
    {
        $screenshotPath = str_replace(asset(''), '', $request->query('screenshot_path'));
        
        $item = Item::where('screenshot_path', 'LIKE', '%' . $screenshotPath)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($item) {
            $itemData = [
                'id' => $item->id,
                'name' => $item->name,
                'model' => $item->model,
                'logos' => $item->logos,
                'user_id' => $item->user_id,
                'description' => $item->description,
                'screenshot_path' => asset($item->screenshot_path)
            ];

            return Inertia::render('Editor', [
                'item' => $itemData
            ]);
        }

        return redirect()->back();
    }
}
