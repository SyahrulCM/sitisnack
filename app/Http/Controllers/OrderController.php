<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('reseller_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('nama_produk')->get();
        return view('orders.create', compact('products'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'catatan_reseller' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            $order = Order::create([
                'kode_pemesanan' => 'ORD-' . now()->format('YmdHis'),
                'tanggal_pemesanan' => now()->toDateString(),
                'reseller_id' => $request->user()->id,
                'total_estimasi' => 0,
                'status_pemesanan' => 'MENUNGGU',
                'catatan_reseller' => $request->catatan_reseller,
            ]);

            $total = 0;

            foreach ($request->items as $row) {
                $product = Product::findOrFail($row['product_id']);
                $qty = (int) $row['qty'];
                $harga = (int) $product->harga;
                $subtotal = $qty * $harga;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_estimasi' => $total]);
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }
}
