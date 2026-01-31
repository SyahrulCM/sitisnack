<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pesanan - {{ $order->kode_pemesanan }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><b>Reseller:</b> {{ $order->reseller->nama_lengkap ?? '-' }}</div>
                    <div><b>Tanggal:</b> {{ \Carbon\Carbon::parse($order->tanggal_pemesanan)->format('d-m-Y') }}</div>
                    <div><b>Status:</b> {{ $order->status_pemesanan }}</div>
                    <div><b>Total:</b> Rp {{ number_format($order->total_estimasi,0,',','.') }}</div>
                    <div class="md:col-span-2"><b>Catatan Reseller:</b> {{ $order->catatan_reseller ?? '-' }}</div>
                    <div class="md:col-span-2"><b>Catatan Penjualan:</b> {{ $order->catatan_penjualan ?? '-' }}</div>
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
                                <th class="text-left py-2">Harga</th>
                                <th class="text-left py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $it)
                                <tr class="border-b">
                                    <td class="py-2">{{ $it->product->nama_produk ?? '-' }}</td>
                                    <td class="py-2">{{ $it->qty }}</td>
                                    <td class="py-2">Rp {{ number_format($it->harga,0,',','.') }}</td>
                                    <td class="py-2">Rp {{ number_format($it->subtotal,0,',','.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($order->status_pemesanan === 'MENUNGGU')
                <div class="bg-white p-6 rounded shadow">
                    <h3 class="font-semibold mb-3">Aksi Penjualan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('manage.orders.approve', $order) }}" class="border rounded p-4">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                            <textarea name="catatan_penjualan" class="w-full border rounded p-2" rows="2"></textarea>
                            <button class="mt-3 px-4 py-2 rounded bg-green-600 text-white font-semibold">
                                Terima Pesanan
                            </button>
                        </form>

                        <form method="POST" action="{{ route('manage.orders.reject', $order) }}" class="border rounded p-4">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-medium mb-1">Alasan penolakan (wajib)</label>
                            <textarea name="catatan_penjualan" class="w-full border rounded p-2" rows="2" required></textarea>
                            <button class="mt-3 px-4 py-2 rounded bg-red-600 text-white font-semibold">
                                Tolak Pesanan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('manage.orders.index') }}" class="px-4 py-2 rounded border">Kembali</a>
            </div>

        </div>
    </div>
</x-app-layout>
