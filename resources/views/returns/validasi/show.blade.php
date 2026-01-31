<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Retur - {{ $retur->kode_retur }}
            </h2>

            <a href="{{ route('returns.validasi.index') }}"
               class="px-4 py-2 rounded border bg-white shadow-sm hover:shadow">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><b>Order:</b> {{ $retur->order->kode_pemesanan ?? '-' }}</div>
                    <div><b>Status Retur:</b> {{ $retur->status_validasi }}</div>
                    <div><b>Reseller:</b> {{ $retur->order->reseller->nama_lengkap ?? '-' }}</div>
                    <div><b>Tanggal:</b> {{ \Carbon\Carbon::parse($retur->tanggal_pengajuan)->format('d-m-Y') }}</div>
                    <div class="md:col-span-2"><b>Catatan Reseller:</b> {{ $retur->catatan_reseller ?? '-' }}</div>
                </div>

                @if($retur->bukti_foto)
                    <div class="mt-4">
                        <b class="text-sm">Bukti Foto:</b>
                        <div class="mt-2">
                            <a target="_blank" href="{{ asset('storage/'.$retur->bukti_foto) }}"
                               class="px-3 py-1 rounded border bg-white shadow-sm hover:shadow inline-block">
                                Lihat Bukti
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Item Retur</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Produk</th>
                            <th class="text-left py-2">Qty Retur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retur->items as $it)
                            <tr class="border-b">
                                <td class="py-2">{{ $it->product->nama_produk ?? '-' }}</td>
                                <td class="py-2">{{ $it->qty_retur }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-3">Validasi Retur</h3>

                <form method="POST" action="{{ route('returns.validasi.status', $retur) }}">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-wrap items-center gap-2">
                        <select name="status_validasi" class="border rounded px-3 py-2 bg-white shadow-sm">
                            @foreach(['MENUNGGU','VALID','BUTUH_REVISI','DITOLAK'] as $st)
                                <option value="{{ $st }}" {{ $retur->status_validasi===$st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>

                        <button class="px-4 py-2 rounded bg-gray-900 text-white shadow hover:opacity-90">
                            Simpan
                        </button>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium mb-1">Catatan Penjualan (opsional)</label>
                        <textarea name="catatan_penjualan" rows="3"
                                  class="w-full border rounded p-2">{{ old('catatan_penjualan', $retur->catatan_penjualan) }}</textarea>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
