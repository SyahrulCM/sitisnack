<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran - {{ $payment->kode_pembayaran }}
            </h2>

            <a href="{{ route('manage.payments.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info pembayaran -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><b>Order:</b> {{ $payment->order->kode_pemesanan ?? '-' }}</div>
                    <div><b>Reseller:</b> {{ $payment->reseller->nama_lengkap ?? '-' }}</div>
                    <div><b>Tanggal Input:</b> {{ \Carbon\Carbon::parse($payment->tanggal_input)->format('d-m-Y') }}</div>
                    <div><b>Total:</b> Rp {{ number_format($payment->total_penjualan,0,',','.') }}</div>
                    <div><b>Metode:</b> {{ $payment->metode_pembayaran ?? '-' }}</div>
                    <div>
                        <b>Status:</b>
                        <span class="px-2 py-1 rounded text-xs border bg-gray-50 shadow-sm">
                            {{ $payment->status_validasi }}
                        </span>
                    </div>

                    <div class="md:col-span-2">
                        <b>Bukti Transfer:</b>
                        @if($payment->bukti_transfer)
                            <a class="text-blue-600 underline"
                               href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank">
                                Lihat Bukti
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

            <!-- Items -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Detail Penjualan (Terjual & Sisa)</h3>

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

            <!-- Validasi (card clean) -->
            <div class="rounded-lg border bg-gray-50 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Validasi Pembayaran</h3>
                    <span class="text-xs px-2 py-1 rounded bg-white border text-gray-700 shadow-sm">
                        {{ $payment->status_validasi }}
                    </span>
                </div>

                <form method="POST" action="{{ route('manage.payments.validate', $payment) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium mb-1">Status Validasi</label>
                        <select name="status_validasi"
                                class="w-full border rounded-md bg-white px-3 py-2 pr-12 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                            @foreach(['VALID','BUTUH_REVISI','DITOLAK'] as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                        <input type="text" name="catatan_validasi"
                               class="w-full border rounded-md bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                               placeholder="Contoh: angka terjual tidak sesuai, mohon revisi">
                    </div>

                    <div class="md:col-span-2">
                        <button class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700">
                            Simpan Validasi
                        </button>
                    </div>
                </form>

                <p class="mt-3 text-xs text-gray-500">
                    Tips: Gunakan <b>BUTUH_REVISI</b> kalau reseller perlu perbaiki input; gunakan <b>DITOLAK</b> kalau bukti/isi tidak valid.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
