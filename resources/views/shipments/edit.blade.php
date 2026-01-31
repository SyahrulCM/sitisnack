<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pengiriman - {{ $shipment->order->kode_pemesanan ?? '-' }}
            </h2>

            <a href="{{ route('shipments.index') }}"
            class="inline-flex items-center px-4 py-2 rounded-md bg-white text-gray-800 text-sm font-semibold shadow hover:shadow-md border">
                ← Kembali
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

            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><b>Reseller:</b> {{ $shipment->order->reseller->nama_lengkap ?? '-' }}</div>
                    <div><b>Status Order:</b> {{ $shipment->order->status_pemesanan ?? '-' }}</div>
                    <div><b>Status Pengiriman:</b> {{ $shipment->status_pengiriman }}</div>
                    <div><b>Total:</b> Rp {{ number_format($shipment->order->total_estimasi ?? 0,0,',','.') }}</div>
                    <div class="md:col-span-2"><b>Alamat Reseller:</b> {{ $shipment->order->reseller->alamat ?? '-' }}</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Item Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Produk</th>
                                <th class="text-left py-2">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipment->order->items as $it)
                                <tr class="border-b">
                                    <td class="py-2">{{ $it->product->nama_produk ?? '-' }}</td>
                                    <td class="py-2">{{ $it->qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Update Data Pengiriman</h3>

                <form method="POST" action="{{ route('shipments.update', $shipment) }}" class="mb-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal Jadwal Kirim</label>
                            <input type="date" name="tanggal_jadwal_kirim" value="{{ $shipment->tanggal_jadwal_kirim }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Driver/Kurir</label>
                            <input type="text" name="kurir_driver" value="{{ $shipment->kurir_driver }}"
                                   class="w-full border rounded p-2" placeholder="Nama kurir/driver">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Alamat Kirim (opsional override)</label>
                            <textarea name="alamat_kirim" class="w-full border rounded p-2" rows="2">{{ $shipment->alamat_kirim }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Catatan</label>
                            <textarea name="catatan" class="w-full border rounded p-2" rows="2">{{ $shipment->catatan }}</textarea>
                        </div>
                    </div>

                    <button class="mt-4 px-4 py-2 rounded bg-gray-900 text-white">
                        Simpan Data
                    </button>
                </form>

                <h3 class="font-semibold mb-2">Update Status</h3>
                <div class="mt-6 rounded-lg border bg-gray-50 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Update Status</h3>
                    <span class="text-xs px-2 py-1 rounded bg-white border text-gray-700 shadow-sm">
                        {{ $shipment->status_pengiriman }}
                    </span>
                </div>

                <form method="POST" action="{{ route('shipments.status', $shipment) }}" class="flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')

                    <select name="status_pengiriman"
                            class="border rounded-md bg-white p-2 pr-10 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                        @foreach(['MENUNGGU','DIJADWALKAN','DIKIRIM','DITERIMA'] as $st)
                            <option value="{{ $st }}" {{ $shipment->status_pengiriman===$st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>

                    <button class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700">
                        Update Status
                    </button>
                </form>

                <p class="mt-3 text-xs text-gray-500">
                    Catatan: Status <b>DIKIRIM</b> akan mengubah status order menjadi <b>DIKIRIM</b>,
                    dan <b>DITERIMA</b> akan mengubah order menjadi <b>SELESAI</b>.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
