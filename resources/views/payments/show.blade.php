<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran - {{ $payment->kode_pembayaran }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('payments.index') }}"
                class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                    Kembali
                </a>

                @if($payment->status_validasi === 'BUTUH_REVISI')
                    <a href="{{ route('payments.edit', $payment) }}"
                    class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                        Revisi
                    </a>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><b>Order:</b> {{ $payment->order->kode_pemesanan ?? '-' }}</div>
                    <div><b>Tanggal:</b> {{ \Carbon\Carbon::parse($payment->tanggal_input)->format('d-m-Y') }}</div>
                    <div><b>Total:</b> Rp {{ number_format($payment->total_penjualan,0,',','.') }}</div>
                    <div>
                        <b>Status Validasi:</b>
                        <span class="px-2 py-1 rounded text-xs border bg-gray-50 shadow-sm">
                            {{ $payment->status_validasi }}
                        </span>
                    </div>

                    <div><b>Metode:</b> {{ $payment->metode_pembayaran ?? '-' }}</div>
                    <div>
                        <b>Bukti Transfer:</b>
                        @if($payment->bukti_transfer)
                            <a class="text-blue-600 underline"
                               href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank">
                                Lihat
                            </a>
                        @else
                            -
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <b>Catatan Validasi:</b> {{ $payment->catatan_validasi ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-3">Detail Terjual & Sisa</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Produk</th>
                                <th class="text-left py-2">Terjual</th>
                                <th class="text-left py-2">Sisa</th>
                                <th class="text-left py-2">Harga</th>
                                <th class="text-left py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->items as $it)
                                <tr class="border-b">
                                    <td class="py-2">{{ $it->product->nama_produk ?? '-' }}</td>
                                    <td class="py-2">{{ $it->qty_terjual }}</td>
                                    <td class="py-2">{{ $it->qty_sisa }}</td>
                                    <td class="py-2">Rp {{ number_format($it->harga,0,',','.') }}</td>
                                    <td class="py-2">Rp {{ number_format($it->subtotal,0,',','.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
