<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Validasi Retur</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Kode Retur</th>
                            <th class="text-left py-2">Order</th>
                            <th class="text-left py-2">Reseller</th>
                            <th class="text-left py-2">Tanggal</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $r)
                            <tr class="border-b">
                                <td class="py-2">{{ $r->kode_retur }}</td>
                                <td class="py-2">{{ $r->order->kode_pemesanan ?? '-' }}</td>
                                <td class="py-2">{{ $r->order->reseller->nama_lengkap ?? '-' }}</td>
                                <td class="py-2">{{ \Carbon\Carbon::parse($r->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                <td class="py-2">{{ $r->status_validasi }}</td>
                                <td class="py-2">
                                    <a href="{{ route('returns.validasi.show', $r) }}"
                                       class="px-3 py-1 rounded border bg-white shadow-sm hover:shadow">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">Belum ada retur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $returns->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
