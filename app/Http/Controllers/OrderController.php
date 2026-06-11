<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío');
        }
        
        $total = collect($cart)->sum(function ($item) {
            return $item['precio'] * $item['cantidad'];
        });
        
        return view('checkout', compact('cart', 'total'));
    }
    
    public function store(StoreOrderRequest $request)
    {
        $request->validated();
        
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío');
        }
        
        $total = collect($cart)->sum(function ($item) {
            return $item['precio'] * $item['cantidad'];
        });
        
        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_phone' => $request->shipping_phone,
            'items' => $cart,
        ]);
        
        // Clear cart
        Session::forget('cart');
        
        return redirect()->route('order.show', $order->id)->with('success', 'Pedido realizado correctamente');
    }
    
    public function show($id)
    {
        $order = Order::with('user')->findOrFail($id);
        
        // Only the order owner can view it
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('order.show', compact('order'));
    }
    
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('order.index', compact('orders'));
    }

    /**
     * Genera y descarga la factura del pedido en PDF (feature 13).
     */
    public function factura($id)
    {
        $order = Order::with('user')->findOrFail($id);

        // Solo el dueño del pedido puede descargar su factura.
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('order.factura', compact('order'))
            ->setPaper('a4');

        return $pdf->download('factura-pedido-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }
}
