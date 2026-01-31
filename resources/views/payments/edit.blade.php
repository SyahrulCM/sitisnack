<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Revisi Pembayaran - {{ $payment->kode_pembayaran }}
            </h2>

            <a href="{{ route('payments.show', $payment) }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
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

            <form method="POST" action="{{ route('payments.update', $payment) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white p-6 rounded shadow mb-4">
                    <h3 class="font-semibold mb-3">Perbaiki Terjual & Sisa</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Produk</th>
                                    <th class="text-left py-2">Qty Order</th>
                                    <th class="text-left py-2">Terjual</th>
                                    <th class="text-left py-2">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->order->items as $i => $it)
                                    @php
                                        $oldItem = $payment->items->firstWhere('product_id', $it->product_id);
                                    @endphp
                                    <tr class="border-b">
                                        <td class="py-2">{{ $it->product->nama_produk }}</td>
                                        <td class="py-2">{{ $it->qty }}</td>

                                        <td class="py-2">
                                            <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $it->product_id }}">
                                            <input type="number" min="0" max="{{ $it->qty }}"
                                                   name="items[{{ $i }}][qty_terjual]"
                                                   class="border rounded p-2 w-28"
                                                   value="{{ old("items.$i.qty_terjual", $oldItem->qty_terjual ?? 0) }}">
                                        </td>

                                        <td class="py-2">
                                            <input type="number" min="0" max="{{ $it->qty }}"
                                                   name="items[{{ $i }}][qty_sisa]"
                                                   class="border rounded p-2 w-28"
                                                   value="{{ old("items.$i.qty_sisa", $oldItem->qty_sisa ?? $it->qty) }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        Catatan: Untuk setiap produk, <b>terjual + sisa harus sama dengan qty order</b>.
                    </p>
                </div>

                <div class="bg-white p-6 rounded shadow mb-4">
                    <h3 class="font-semibold mb-3">Metode Pembayaran</h3>

                    <div class="flex flex-wrap gap-4 items-center">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="metode_pembayaran" value="TRANSFER" required
                                {{ old('metode_pembayaran', $payment->metode_pembayaran) === 'TRANSFER' ? 'checked' : '' }}>
                            <span>TRANSFER</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="radio" name="metode_pembayaran" value="TUNAI" required
                                {{ old('metode_pembayaran', $payment->metode_pembayaran) === 'TUNAI' ? 'checked' : '' }}>
                            <span>TUNAI</span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Upload Bukti Transfer (opsional kalau mau ganti)</label>
                        <input type="file" name="bukti_transfer" class="border rounded p-2 w-full">
                        @if($payment->bukti_transfer)
                            <p class="text-xs text-gray-500 mt-2">
                                Bukti saat ini:
                                <a class="text-blue-600 underline" target="_blank"
                                   href="{{ asset('storage/'.$payment->bukti_transfer) }}">Lihat</a>
                            </p>
                        @endif
                    </div>
                </div>

                <button class="px-4 py-2 rounded bg-gray-900 text-white font-semibold shadow">
                    Kirim Revisi
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
