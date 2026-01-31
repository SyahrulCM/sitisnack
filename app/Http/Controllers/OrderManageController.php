<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManageController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->query('status', 'MENUNGGU');

        $orders = Order::with(['items.product'])
            ->when($status, fn($q) => $q->where('status_pemesanan', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('manage_orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'reseller']);
        return view('manage_orders.show', compact('order'));
    }

    public function approve(Request $r, Order $order)
    {
        $r->validate([
            'catatan_penjualan' => 'nullable|string',
        ]);

        if ($order->status_pemesanan !== 'MENUNGGU') {
            return back()->with('error', 'Pesanan sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($r, $order) {
            $order->update([
                'status_pemesanan' => 'DITERIMA',
                'catatan_penjualan' => $r->catatan_penjualan,
            ]);

            // buat shipment otomatis (kalau belum ada)
            Shipment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'status_pengiriman' => 'MENUNGGU',
                    'alamat_kirim' => $order->reseller->alamat ?? null,
                    'updated_by' => $r->user()->id,
                ]
            );
        });

        return redirect()->route('manage.orders.show', $order)->with('success', 'Pesanan diterima.');
    }

    public function reject(Request $r, Order $order)
    {
        $r->validate([
            'catatan_penjualan' => 'required|string',
        ]);

        if ($order->status_pemesanan !== 'MENUNGGU') {
            return back()->with('error', 'Pesanan sudah diproses sebelumnya.');
        }

        $order->update([
            'status_pemesanan' => 'DITOLAK',
            'catatan_penjualan' => $r->catatan_penjualan,
        ]);

        return redirect()->route('manage.orders.show', $order)->with('success', 'Pesanan ditolak.');
    }
}
