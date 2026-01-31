<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ajukan Retur - {{ $order->kode_pemesanan }}
            </h2>

            <a href="{{ route('payments.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
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

            <form method="POST" action="{{ route('returns.store', $order) }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white p-6 rounded shadow mb-4">
                    <h3 class="font-semibold mb-3">Input Qty Retur (Sisa)</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Produk</th>
                                    <th class="text-left py-2">Qty Order</th>
                                    <th class="text-left py-2">Qty Retur (Sisa)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $i => $it)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $it->product->nama_produk ?? '-' }}</td>
                                        <td class="py-2">{{ $it->qty }}</td>
                                        <td class="py-2">
                                            <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $it->product_id }}">

                                            <input type="number"
                                                   min="0"
                                                   max="{{ $it->qty }}"
                                                   name="items[{{ $i }}][qty_retur]"
                                                   class="border rounded p-2 w-28"
                                                   value="{{ old("items.$i.qty_retur", 0) }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        Isi <b>Qty Retur</b> sesuai barang sisa yang akan dikembalikan. Tidak boleh melebihi Qty Order.
                    </p>
                </div>

                <div class="bg-white p-6 rounded shadow mb-4">
                    <h3 class="font-semibold mb-3">Bukti & Catatan</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Upload Bukti Retur (Foto) <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_foto" class="border rounded p-2 w-full" required>
                        <p class="text-xs text-gray-500 mt-2">Format: JPG/PNG, max 2MB.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan Reseller (opsional)</label>
                        <textarea name="catatan_reseller" rows="3"
                                  class="w-full border rounded p-2"
                                  placeholder="Contoh: Sisa barang tidak terjual, kondisi masih baik...">{{ old('catatan_reseller') }}</textarea>
                    </div>
                </div>

                <button class="px-4 py-2 rounded bg-gray-900 text-white font-semibold shadow hover:shadow-md">
                    Kirim Pengajuan Retur
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
