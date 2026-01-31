<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengiriman</h2>

            <form method="GET" action="{{ route('shipments.index') }}" class="flex gap-2">
                <select name="status"
                        class="border rounded-md bg-white px-3 py-2 pr-12 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                        onchange="this.form.submit()">
                    @foreach(['MENUNGGU','DIJADWALKAN','DIKIRIM','DITERIMA'] as $st)
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

            <div class="bg-white p-6 rounded shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Kode Order</th>
                                <th class="text-left py-2">Reseller</th>
                                <th class="text-left py-2">Jadwal Kirim</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $s)
                                <tr class="border-b">
                                    <td class="py-2">{{ $s->order->kode_pemesanan ?? '-' }}</td>
                                    <td class="py-2">{{ $s->order->reseller->nama_lengkap ?? '-' }}</td>
                                    <td class="py-2">{{ $s->tanggal_jadwal_kirim ? \Carbon\Carbon::parse($s->tanggal_jadwal_kirim)->format('d-m-Y') : '-' }}</td>
                                    <td class="py-2">{{ $s->status_pengiriman }}</td>
                                    <td class="py-2">
                                        <a href="{{ route('shipments.edit', $s) }}" class="px-3 py-1 rounded border">
                                            Detail / Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $shipments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
