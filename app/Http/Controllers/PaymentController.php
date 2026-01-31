<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $userId = $r->user()->id;

        $payments = Payment::with('order')
            ->where('reseller_id', $userId)
            ->latest()
            ->paginate(10);

        // tampilkan order yang boleh dibayar DAN belum pernah dibuat payment
        $orders = Order::where('reseller_id', $r->user()->id)
            ->whereIn('status_pemesanan', ['DIKIRIM','SELESAI'])
            ->whereDoesntHave('payments') // <= ini kuncinya
            ->latest()
            ->get();

        return view('payments.index', compact('payments', 'orders'));
    }

    public function create(Request $r, Order $order)
    {
        abort_if($order->reseller_id !== $r->user()->id, 403);

        // ✅ kalau sudah pernah dibayar, jangan boleh buat lagi
        if ($order->payments()->exists()) {
            return redirect()->route('payments.index')
                ->with('error', 'Pesanan ini sudah pernah dibuat pembayaran.');
        }

        $order->load(['items.product']);

        return view('payments.create', compact('order'));
    }

    public function store(Request $r, Order $order)
    {
        $order->load('returnRequest');

        if (!$order->returnRequest || $order->returnRequest->status_validasi !== 'VALID') {
            return redirect()->route('returns.create', $order)
                ->with('error', 'Sebelum pembayaran, ajukan retur dulu dan tunggu validasi (status VALID).');
        }
        abort_if($order->reseller_id !== $r->user()->id, 403);

        // ✅ cegah dobel submit/payment
        if ($order->payments()->exists()) {
            return redirect()->route('payments.index')
                ->with('error', 'Pesanan ini sudah pernah dibuat pembayaran.');
        }

        $r->validate([
            'metode_pembayaran' => 'required|in:TRANSFER,TUNAI',

            // ✅ bukti wajib kalau TRANSFER
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_terjual' => 'required|integer|min:0',
            'items.*.qty_sisa' => 'required|integer|min:0',
        ]);

        if ($r->metode_pembayaran === 'TRANSFER' && !$r->hasFile('bukti_transfer')) {
            return back()->withErrors(['bukti_transfer' => 'Bukti transfer wajib diupload jika memilih TRANSFER.'])->withInput();
        }

        $order->load(['items']); // order_items

        // validasi qty: terjual + sisa harus sama dengan qty order per produk
        $orderQtyByProduct = $order->items->pluck('qty', 'product_id'); // [product_id => qty_order]
        $hargaByProduct = $order->items->pluck('harga', 'product_id');  // [product_id => harga_snapshot]

        foreach ($r->items as $it) {
            $pid = (int) $it['product_id'];
            $qtyOrder = (int) ($orderQtyByProduct[$pid] ?? 0);

            if ($qtyOrder <= 0) {
                return back()->withErrors(['items' => 'Produk tidak ditemukan di order.'])->withInput();
            }

            $sum = (int)$it['qty_terjual'] + (int)$it['qty_sisa'];
            if ($sum !== $qtyOrder) {
                return back()->withErrors([
                    'items' => 'Qty terjual + qty sisa harus sama dengan qty order untuk setiap produk.'
                ])->withInput();
            }

            // ✅ pastikan harga ada (biar total tidak 0 tanpa sadar)
            $harga = (int) ($hargaByProduct[$pid] ?? 0);
            if ($harga <= 0) {
                return back()->withErrors([
                    'items' => 'Harga produk di order masih 0/kosong. Pastikan harga tersimpan di order_items.'
                ])->withInput();
            }
        }

        $payment = DB::transaction(function () use ($r, $order, $hargaByProduct) {

            $path = null;
            if ($r->hasFile('bukti_transfer')) {
                $path = $r->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $payment = Payment::create([
                'kode_pembayaran' => 'PAY-' . now()->format('YmdHis'),
                'tanggal_input' => now()->toDateString(),
                'order_id' => $order->id,
                'reseller_id' => $r->user()->id,
                'total_penjualan' => 0,
                'metode_pembayaran' => $r->metode_pembayaran,
                'bukti_transfer' => $path,
                'status_validasi' => 'MENUNGGU',
            ]);

            $total = 0;

            foreach ($r->items as $it) {
                $pid = (int) $it['product_id'];
                $harga = (int) ($hargaByProduct[$pid] ?? 0);
                $qtyTerjual = (int) $it['qty_terjual'];

                $subtotal = $qtyTerjual * $harga;
                $total += $subtotal;

                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'product_id' => $pid,
                    'qty_terjual' => (int) $it['qty_terjual'],
                    'qty_sisa' => (int) $it['qty_sisa'],
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ]);
            }

            $payment->update(['total_penjualan' => $total]);

            return $payment;
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pembayaran berhasil dikirim, menunggu validasi.');
    }

    public function show(Request $r, Payment $payment)
    {
        abort_if($payment->reseller_id !== $r->user()->id, 403);

        $payment->load(['order', 'items.product']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Request $r, Payment $payment)
    {
        abort_if($payment->reseller_id !== $r->user()->id, 403);

        // hanya boleh revisi kalau butuh revisi
        if ($payment->status_validasi !== 'BUTUH_REVISI') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Pembayaran ini tidak bisa direvisi.');
        }

        $payment->load(['order.items.product', 'items']); // order_items + payment_items

        return view('payments.edit', compact('payment'));
    }

    public function update(Request $r, Payment $payment)
    {
        abort_if($payment->reseller_id !== $r->user()->id, 403);

        if ($payment->status_validasi !== 'BUTUH_REVISI') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Pembayaran ini tidak bisa direvisi.');
        }

        $r->validate([
            'metode_pembayaran' => 'required|in:TRANSFER,TUNAI',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_terjual' => 'required|integer|min:0',
            'items.*.qty_sisa' => 'required|integer|min:0',
        ]);

        if ($r->metode_pembayaran === 'TRANSFER' && !$payment->bukti_transfer && !$r->hasFile('bukti_transfer')) {
            return back()->withErrors(['bukti_transfer' => 'Bukti transfer wajib jika memilih TRANSFER.'])->withInput();
        }

        $order = $payment->order()->with('items')->first();
        $orderQtyByProduct = $order->items->pluck('qty', 'product_id');
        $hargaByProduct = $order->items->pluck('harga', 'product_id');

        foreach ($r->items as $it) {
            $pid = (int)$it['product_id'];
            $qtyOrder = (int)($orderQtyByProduct[$pid] ?? 0);

            if ($qtyOrder <= 0) {
                return back()->withErrors(['items' => 'Produk tidak ditemukan di order.'])->withInput();
            }

            $sum = (int)$it['qty_terjual'] + (int)$it['qty_sisa'];
            if ($sum !== $qtyOrder) {
                return back()->withErrors(['items' => 'Qty terjual + qty sisa harus sama dengan qty order.'])->withInput();
            }

            if ((int)($hargaByProduct[$pid] ?? 0) <= 0) {
                return back()->withErrors(['items' => 'Harga produk di order masih 0/kosong.'])->withInput();
            }
        }

        DB::transaction(function () use ($r, $payment, $hargaByProduct) {

            // kalau upload bukti baru, replace
            if ($r->hasFile('bukti_transfer')) {
                $payment->bukti_transfer = $r->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $payment->metode_pembayaran = $r->metode_pembayaran;

            // reset validasi jadi menunggu lagi
            $payment->status_validasi = 'MENUNGGU';
            $payment->catatan_validasi = null;
            $payment->verified_by = null;
            $payment->verified_at = null;

            // hapus item lama lalu input ulang biar simpel
            $payment->items()->delete();

            $total = 0;

            foreach ($r->items as $it) {
                $pid = (int)$it['product_id'];
                $harga = (int)($hargaByProduct[$pid] ?? 0);
                $qtyTerjual = (int)$it['qty_terjual'];

                $subtotal = $qtyTerjual * $harga;
                $total += $subtotal;

                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'product_id' => $pid,
                    'qty_terjual' => (int)$it['qty_terjual'],
                    'qty_sisa' => (int)$it['qty_sisa'],
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ]);
            }

            $payment->total_penjualan = $total;
            $payment->save();
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Revisi berhasil dikirim. Menunggu validasi ulang.');
    }
}
