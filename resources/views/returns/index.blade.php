<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Retur
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-3">Riwayat Retur</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Kode Retur</th>
                                <th class="text-left py-2">Order</th>
                                <th class="text-left py-2">Tanggal</th>
                                <th class="text-left py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $rt)
                                @php
                                    $st = $rt->status_validasi;
                                    $badge = 'bg-gray-50 text-gray-700 border-gray-200';
                                    if ($st === 'VALID') $badge = 'bg-green-50 text-green-700 border-green-200';
                                    if ($st === 'BUTUH_REVISI') $badge = 'bg-yellow-50 text-yellow-800 border-yellow-200';
                                    if ($st === 'DITOLAK') $badge = 'bg-red-50 text-red-700 border-red-200';
                                @endphp

                                <tr class="border-b">
                                    <td class="py-2">{{ $rt->kode_retur }}</td>
                                    <td class="py-2">{{ $rt->order->kode_pemesanan ?? '-' }}</td>
                                    <td class="py-2">
                                        {{ $rt->tanggal_pengajuan ? \Carbon\Carbon::parse($rt->tanggal_pengajuan)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs border shadow-sm {{ $badge }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">
                                        Belum ada retur.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $returns->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
