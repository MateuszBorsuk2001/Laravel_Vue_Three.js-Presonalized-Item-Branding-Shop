<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
            'postal_code' => 'required|string',
            'street_name' => 'required|string',
            'house_number' => 'required|string',
            'city' => 'required|string',
            'items' => 'required|array',
            'total_price' => 'required|numeric',
            // 'unit_price' => 'required|numeric',
            // 'combined_unit_price' => 'required|numeric',
        ]);

        // Create order with direct array access
        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'postal_code' => $validated['postal_code'],
            'street_name' => $validated['street_name'],
            'house_number' => $validated['house_number'],
            'city' => $validated['city'],
            'items' => $validated['items'],
            'total_price' => $validated['total_price'],
            // 'unit_price' => $validated['unit_price'],
            // 'combined_unit_price' => $validated['combined_unit_price'],
        ]);
    }
    public function downloadConfirmation(Order $order)
    {    
        $pdf = PDF::loadView('pdf.order-confirmation', [
            'order' => $order,
            'orderId' => $order->id,
            'items' => $order->items,
            'total' => $order->total_price,
            // 'unit_price' => $order->unit_price,
            // 'combined_unit_price' => $order->combined_unit_price,
            'customer' => [
                'name' => $order->name,
                'surname' => $order->surname,
                'email' => $order->email,
                'address' => [
                    'street' => $order->street_name,
                    'house' => $order->house_number,
                    'postal' => $order->postal_code,
                    'city' => $order->city
                ]
            ],
            'orderDate' => $order->created_at->format('Y-m-d H:i:s')
        ]);
    
        return $pdf->download('potwierdzenie-zamowienia.pdf');
    }
    public function getLatestOrder(): JsonResponse
    {
        $latestOrder = Order::latest()->first();
        return response()->json($latestOrder);
    }
}


