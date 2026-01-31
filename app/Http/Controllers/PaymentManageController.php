<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentManageController extends Controller
{
    public function index(Request $r)
    {
        $status = $r->query('status', 'MENUNGGU');

        $payments = Payment::with(['order', 'reseller'])
            ->when($status, fn($q) => $q->where('status_validasi', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('manage_payments.index', compact('payments', 'status'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['items.product', 'order', 'reseller']);
        return view('manage_payments.show', compact('payment'));
    }

    public function validatePayment(Request $r, Payment $payment)
    {
        $r->validate([
            'status_validasi' => 'required|in:VALID,BUTUH_REVISI,DITOLAK',
            'catatan_validasi' => 'nullable|string',
        ]);

        $payment->update([
            'status_validasi' => $r->status_validasi,
            'catatan_validasi' => $r->catatan_validasi,
            'verified_by' => $r->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Validasi pembayaran berhasil disimpan.');
    }
}
