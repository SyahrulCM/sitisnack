<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Konfirmasi Pesanan</h2>

            <form method="GET" action="{{ route('manage.orders.index') }}" class="flex gap-2">
                <select name="status" class="border rounded p-2" onchange="this.form.submit()">
                    @foreach(['MENUNGGU','DITERIMA','DITOLAK','DIPROSES','DIKIRIM','SELESAI'] as $st)
                        <option value="{{ $st }}" {{ $status===$st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Kode</th>
                                <th class="text-left py-2">Tanggal</th>
                                <th class="text-left py-2">Reseller</th>
                                <th class="text-left py-2">Total</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr class="border-b">
                                    <td class="py-2">{{ $o->kode_pemesanan }}</td>
                                    <td class="py-2">{{ \Carbon\Carbon::parse($o->tanggal_pemesanan)->format('d-m-Y') }}</td>
                                    <td class="py-2">{{ optional($o->reseller)->nama_lengkap }}</td>
                                    <td class="py-2">Rp {{ number_format($o->total_estimasi,0,',','.') }}</td>
                                    <td class="py-2">{{ $o->status_pemesanan }}</td>
                                    <td class="py-2">
                                        <a class="px-3 py-1 rounded border"
                                           href="{{ route('manage.orders.show', $o) }}">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
