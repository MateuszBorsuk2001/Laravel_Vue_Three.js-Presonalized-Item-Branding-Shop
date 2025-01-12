<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Inertia\Inertia;
use Illuminate\Http\Request;

class YourShopController extends Controller
{
    public function index()
    {
        $items = Item::where('user_id', auth()->id())->get()->map(function($item) {
            $modelTranslations = [
                'mug' => 'Kubek',
                'cap' => 'Czapka z daszkiem',
                'tshirt' => 'T-Shirt'
            ];

            return [
                'id' => $item->id,
                'model' => $modelTranslations[$item->model] ?? $item->model,
                'description' => $item->description,
                'screenshot_path' => asset($item->screenshot_path)
            ];
        });

        return Inertia::render('YourShop', [
            'items' => $items
        ]);
    }
}
