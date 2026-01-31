<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pemesanan
            </h2>

            <a href="{{ route('orders.create') }}"
            class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700">
                + Buat Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Kode</th>
                            <th class="text-left py-2">Tanggal</th>
                            <th class="text-left py-2">Total</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr class="border-b">
                                <td class="py-2">{{ $o->kode_pemesanan }}</td>
                                <td class="py-2">{{ $o->tanggal_pemesanan->format('d-m-Y') }}</td>
                                <td class="py-2">Rp {{ number_format($o->total_estimasi, 0, ',', '.') }}</td>
                                <td class="py-2">{{ $o->status_pemesanan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    Belum ada pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
