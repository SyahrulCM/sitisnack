<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->query('status', 'MENUNGGU');

        $shipments = Shipment::with(['order.reseller'])
            ->when($status, fn($q) => $q->where('status_pengiriman', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('shipments.index', compact('shipments', 'status'));
    }

    public function edit(Shipment $shipment)
    {
        $shipment->load(['order.items.product', 'order.reseller']);
        return view('shipments.edit', compact('shipment'));
    }

    public function update(Request $r, Shipment $shipment)
    {
        $r->validate([
            'tanggal_jadwal_kirim' => 'nullable|date',
            'alamat_kirim' => 'nullable|string',
            'kurir_driver' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $shipment->update([
            'tanggal_jadwal_kirim' => $r->tanggal_jadwal_kirim,
            'alamat_kirim' => $r->alamat_kirim,
            'kurir_driver' => $r->kurir_driver,
            'catatan' => $r->catatan,
            'updated_by' => $r->user()->id,
        ]);

        // kalau sudah diisi jadwal, otomatis DIJADWALKAN (kalau masih MENUNGGU)
        if ($shipment->status_pengiriman === 'MENUNGGU' && $r->tanggal_jadwal_kirim) {
            $shipment->update(['status_pengiriman' => 'DIJADWALKAN']);
        }

        return back()->with('success', 'Data pengiriman berhasil diupdate.');
    }

    public function updateStatus(Request $r, Shipment $shipment)
    {
        $r->validate([
            'status_pengiriman' => 'required|in:MENUNGGU,DIJADWALKAN,DIKIRIM,DITERIMA',
        ]);

        $newStatus = $r->status_pengiriman;

        DB::transaction(function () use ($r, $shipment, $newStatus) {
            $shipment->update([
                'status_pengiriman' => $newStatus,
                'updated_by' => $r->user()->id,
            ]);

            // set tanggal otomatis
            if ($newStatus === 'DIKIRIM' && !$shipment->tanggal_kirim) {
                $shipment->update(['tanggal_kirim' => now()->toDateString()]);
            }

            if ($newStatus === 'DITERIMA' && !$shipment->tanggal_diterima) {
                $shipment->update(['tanggal_diterima' => now()->toDateString()]);
            }

            // sinkron status order
            $order = $shipment->order;

            if ($newStatus === 'DIKIRIM') {
                if (in_array($order->status_pemesanan, ['DITERIMA','DIPROSES','DIKIRIM'], true)) {
                    $order->update(['status_pemesanan' => 'DIKIRIM']);
                }
            }

            if ($newStatus === 'DITERIMA') {
                $order->update(['status_pemesanan' => 'SELESAI']);
            }
        });

        return back()->with('success', 'Status pengiriman diupdate.');
    }
}
