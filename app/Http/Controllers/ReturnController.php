<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    // =========================
    // RESELLER
    // =========================

    public function index(Request $r)
    {
        $returns = ReturnRequest::with('order')
            ->where('reseller_id', $r->user()->id)
            ->latest()
            ->paginate(10);

        return view('returns.index', compact('returns'));
    }

    public function create(Request $r, Order $order)
    {
        abort_if($order->reseller_id !== $r->user()->id, 403);

        $order->load(['items.product', 'returnRequest.items']);

        if ($order->returnRequest && in_array($order->returnRequest->status_validasi, ['MENUNGGU', 'VALID'], true)) {
            return redirect()->route('returns.index')
                ->with('error', 'Retur untuk pesanan ini sedang diproses.');
        }

        if ($order->returnRequest && $order->returnRequest->status_validasi === 'DITOLAK') {
            return redirect()->route('returns.index')
                ->with('error', 'Retur ditolak. Hubungi penjualan.');
        }

        return view('returns.create', compact('order'));
    }

    public function store(Request $r, Order $order)
    {
        abort_if($order->reseller_id !== $r->user()->id, 403);

        $r->validate([
            'bukti_foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'catatan_reseller' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_retur' => 'required|integer|min:0',
        ]);

        $order->load(['items', 'returnRequest']);
        $existing = $order->returnRequest;

        // ❌ BLOKIR RETUR AKTIF
        if ($existing && in_array($existing->status_validasi, ['MENUNGGU', 'VALID', 'DITOLAK'], true)) {
            return redirect()->route('returns.index')
                ->with('error', 'Retur untuk pesanan ini sudah ada.');
        }

        DB::transaction(function () use ($r, $order, $existing) {
            $path = $r->file('bukti_foto')->store('bukti_retur', 'public');

            // =========================
            // 🔁 REVISI RETUR
            // =========================
            if ($existing && $existing->status_validasi === 'BUTUH_REVISI') {

                $existing->update([
                    'tanggal_pengajuan' => now()->toDateString(),
                    'bukti_foto' => $path,
                    'catatan_reseller' => $r->catatan_reseller,
                    'status_validasi' => 'MENUNGGU',
                ]);

                $existing->items()->delete();

                foreach ($r->items as $it) {
                    ReturnItem::create([
                        'return_id' => $existing->id,
                        'product_id' => $it['product_id'],
                        'qty_retur' => $it['qty_retur'],
                    ]);
                }

            } else {

                // =========================
                // 🆕 RETUR BARU
                // =========================
                $retur = ReturnRequest::create([
                    'kode_retur' => 'RET-' . now()->format('YmdHis'),
                    'order_id' => $order->id,
                    'reseller_id' => $r->user()->id,
                    'tanggal_pengajuan' => now()->toDateString(),
                    'bukti_foto' => $path,
                    'catatan_reseller' => $r->catatan_reseller,
                    'status_validasi' => 'MENUNGGU',
                ]);

                foreach ($r->items as $it) {
                    ReturnItem::create([
                        'return_id' => $retur->id,
                        'product_id' => $it['product_id'],
                        'qty_retur' => $it['qty_retur'],
                    ]);
                }
            }
        });

        return redirect()->route('payments.index')
            ->with('success', 'Retur berhasil dikirim. Menunggu validasi.');
    }

    // =========================
    // PENJUALAN
    // =========================

    public function indexValidasi()
    {
        $returns = ReturnRequest::with(['order.reseller'])
            ->latest()
            ->paginate(10);

        return view('returns.validasi.index', compact('returns'));
    }

    public function showValidasi(ReturnRequest $retur)
    {
        $retur->load(['order.reseller', 'items.product']);
        return view('returns.validasi.show', compact('retur'));
    }

    public function updateStatus(Request $r, ReturnRequest $retur)
    {
        $r->validate([
            'status_validasi' => 'required|in:MENUNGGU,VALID,BUTUH_REVISI,DITOLAK',
            'catatan_penjualan' => 'nullable|string',
        ]);

        $retur->update([
            'status_validasi' => $r->status_validasi,
            'catatan_penjualan' => $r->catatan_penjualan,
        ]);

        return back()->with('success', 'Status retur berhasil diperbarui.');
    }
}
