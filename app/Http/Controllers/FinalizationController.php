<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class FinalizationController extends Controller
{
    public function index()
    {
        return Inertia::render('Finalization', [
            'user' => Auth::user(),
            'basketItems' => session()->get('basket', []),
            'prices' => [
                'mug' => 1,
                'tshirt' => 2,
                'cap' => 3
            ]
        ]);
    }
}
