<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Produk</h2>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700">
                + Tambah Produk
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
                <form class="mb-4" method="GET" action="{{ route('products.index') }}">
                    <div class="flex gap-2">
                        <input type="text" name="q" value="{{ $q }}"
                               class="border rounded p-2 w-full" placeholder="Cari nama produk...">
                        <button class="px-4 py-2 rounded bg-gray-900 text-white">Cari</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Nama</th>
                                <th class="text-left py-2">Satuan</th>
                                <th class="text-left py-2">Harga</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                                <tr class="border-b">
                                    <td class="py-2">{{ $p->nama_produk }}</td>
                                    <td class="py-2">{{ $p->satuan }}</td>
                                    <td class="py-2">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                                    <td class="py-2">
                                        @if($p->is_active)
                                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">AKTIF</span>
                                        @else
                                            <span class="px-2 py-1 rounded bg-gray-200 text-gray-800 text-xs">NONAKTIF</span>
                                        @endif
                                    </td>
                                    <td class="py-2 flex gap-2">
                                        <a href="{{ route('products.edit', $p) }}"
                                           class="px-3 py-1 rounded border">Edit</a>

                                        <form method="POST" action="{{ route('products.toggle', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="px-3 py-1 rounded border">
                                                {{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Belum ada produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
