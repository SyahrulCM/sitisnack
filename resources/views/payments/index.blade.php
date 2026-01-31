<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran
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

            {{-- Card: Buat Pembayaran dari Pesanan --}}
            <div class="bg-white p-6 rounded shadow mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Buat Pembayaran dari Pesanan</h3>
                    <p class="text-xs text-gray-500">
                        Alur: Ajukan Retur → Validasi → Baru Pembayaran
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @forelse($orders as $o)
                        @php
                            $retur = $o->returnRequest ?? null;
                            $returValid = $retur && $retur->status_validasi === 'VALID';
                        @endphp

                        @if(!$returValid)
                            <a href="{{ route('returns.create', $o) }}"
                               class="px-4 py-2 rounded bg-white border shadow-sm hover:shadow text-sm">
                                Ajukan Retur - {{ $o->kode_pemesanan }}
                                @if($retur)
                                    <span class="ml-2 text-xs px-2 py-1 rounded border bg-gray-50 shadow-sm">
                                        {{ $retur->status_validasi }}
                                    </span>
                                @else
                                    <span class="ml-2 text-xs px-2 py-1 rounded border bg-gray-50 shadow-sm">
                                        BELUM AJUKAN
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('payments.create', $o) }}"
                               class="px-4 py-2 rounded bg-gray-900 text-white shadow hover:shadow-md text-sm">
                                Bayar - {{ $o->kode_pemesanan }}
                            </a>
                        @endif
                    @empty
                        <div class="text-sm text-gray-500">
                            Belum ada pesanan yang bisa diproses untuk pembayaran (minimal status DIKIRIM/SELESAI).
                        </div>
                    @endforelse
                </div>

                <p class="mt-3 text-xs text-gray-500">
                    Catatan: Jika retur masih MENUNGGU/BUTUH_REVISI/DITOLAK, pembayaran belum bisa dilakukan.
                </p>
            </div>

            {{-- Card: Riwayat Pembayaran --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-3">Riwayat Pembayaran</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Kode</th>
                                <th class="text-left py-2">Order</th>
                                <th class="text-left py-2">Tanggal</th>
                                <th class="text-left py-2">Total</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($payments as $p)
                                <tr class="border-b">
                                    <td class="py-2">{{ $p->kode_pembayaran }}</td>
                                    <td class="py-2">{{ $p->order->kode_pemesanan ?? '-' }}</td>
                                    <td class="py-2">
                                        {{ \Carbon\Carbon::parse($p->tanggal_input)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2">
                                        Rp {{ number_format($p->total_penjualan,0,',','.') }}
                                    </td>
                                    <td class="py-2">
                                        @php
                                            $st = $p->status_validasi;
                                            $badge = 'bg-gray-50 text-gray-700 border-gray-200';
                                            if ($st === 'VALID') $badge = 'bg-green-50 text-green-700 border-green-200';
                                            if ($st === 'BUTUH_REVISI') $badge = 'bg-yellow-50 text-yellow-800 border-yellow-200';
                                            if ($st === 'DITOLAK') $badge = 'bg-red-50 text-red-700 border-red-200';
                                        @endphp

                                        <span class="px-2 py-1 rounded text-xs border shadow-sm {{ $badge }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('payments.show', $p) }}"
                                               class="px-3 py-1 rounded border bg-white shadow-sm hover:shadow">
                                                Detail
                                            </a>

                                            @if(in_array($p->status_validasi, ['BUTUH_REVISI','DITOLAK']))
                                                <a href="{{ route('payments.edit', $p) }}"
                                                   class="px-3 py-1 rounded border bg-white shadow-sm hover:shadow">
                                                    Revisi
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500">
                                        Belum ada pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
