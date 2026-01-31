<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama Produk</label>
                        <input name="nama_produk" value="{{ old('nama_produk',$product->nama_produk) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Satuan</label>
                        <input name="satuan" value="{{ old('satuan',$product->satuan) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Harga</label>
                        <input type="number" min="0" name="harga" value="{{ old('harga',$product->harga) }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded border">Kembali</a>
                        <button class="px-4 py-2 rounded bg-gray-900 text-white">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
