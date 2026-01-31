<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Pesanan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

                @if($products->count() === 0)
                    <div class="p-4 rounded bg-yellow-100 text-yellow-800">
                        Produk belum ada. Admin/Penjualan belum memasukan produk.
                    </div>
                @endif

                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="mb-4" style="margin-top: 20px">
                        <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                        <textarea name="catatan_reseller" class="w-full border rounded p-2" rows="2"
                            placeholder="Catatan untuk tim penjualan...">{{ old('catatan_reseller') }}</textarea>
                    </div>

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold">Daftar Produk</h3>
                        <button type="button" id="btnAdd"
                            class="px-3 py-2 rounded bg-gray-900 text-white text-sm disabled:opacity-50"
                            {{ $products->count() === 0 ? 'disabled' : '' }}>
                            + Tambah Produk
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border" id="itemsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-2 border">Produk</th>
                                    <th class="p-2 border w-24">Qty</th>
                                    <th class="p-2 border w-40">Harga</th>
                                    <th class="p-2 border w-40">Subtotal</th>
                                    <th class="p-2 border w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Total Estimasi</div>
                            <div class="text-xl font-bold" id="totalText">Rp 0</div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded border">
                            Kembali
                        </a>
                        <button class="px-4 py-2 rounded bg-gray-900 text-white disabled:opacity-50"
                            id="btnSubmit"
                            {{ $products->count() === 0 ? 'disabled' : '' }}>
                            Simpan Pesanan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @php
        $productsForJs = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama_produk,
                'harga' => (int) $p->harga,
                'satuan' => $p->satuan,
            ];
        })->values()->all();
    @endphp

    <script>
        const products = {{ Illuminate\Support\Js::from($productsForJs) }};
        let rowIndex = 0;

        function rupiah(n) {
            n = Number(n || 0);
            return 'Rp ' + n.toLocaleString('id-ID');
        }

        function calcRow(tr) {
            const qty = Number(tr.querySelector('.qty').value || 0);
            const harga = Number(tr.querySelector('.harga').value || 0);
            const subtotal = qty * harga;
            tr.querySelector('.subtotal').textContent = rupiah(subtotal);
            tr.dataset.subtotal = subtotal;
            calcTotal();
        }

        function calcTotal() {
            const rows = document.querySelectorAll('#itemsBody tr');
            let total = 0;
            rows.forEach(r => total += Number(r.dataset.subtotal || 0));
            document.getElementById('totalText').textContent = rupiah(total);
        }

        function productOptionsHtml(selectedId = null) {
            let html = `<option value="">-- Pilih Produk --</option>`;
            products.forEach(p => {
                const selected = (String(p.id) === String(selectedId)) ? 'selected' : '';
                html += `<option value="${p.id}" data-harga="${p.harga}">${p.nama} (Rp ${p.harga.toLocaleString('id-ID')}/${p.satuan})</option>`;
            });
            return html;
        }

        function addRow(data = {}) {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.dataset.subtotal = 0;

            tr.innerHTML = `
                <td class="border p-2">
                    <select name="items[${rowIndex}][product_id]" class="w-full border rounded p-2 produk" required>
                        ${productOptionsHtml(data.product_id ?? null)}
                    </select>
                </td>

                <td class="border p-2">
                    <input type="number" min="1" name="items[${rowIndex}][qty]"
                        class="w-full border rounded p-2 qty"
                        value="${data.qty ?? 1}" required>
                </td>

                <td class="border p-2">
                    <input type="text"
                        class="w-full border rounded p-2 harga_text bg-gray-50"
                        value="Rp 0" readonly>
                    <input type="hidden" class="harga" value="0">
                </td>

                <td class="border p-2 text-right subtotal">Rp 0</td>

                <td class="border p-2 text-center">
                    <button type="button" class="btnRemove px-2 py-1 rounded border">X</button>
                </td>
            `;

            const select = tr.querySelector('.produk');
            const qtyInput = tr.querySelector('.qty');
            const hargaHidden = tr.querySelector('.harga');
            const hargaText = tr.querySelector('.harga_text');

            function setHargaFromSelect() {
                const opt = select.options[select.selectedIndex];
                const h = Number(opt?.dataset?.harga || 0);
                hargaHidden.value = h;
                hargaText.value = rupiah(h);
                calcRow(tr);
            }

            select.addEventListener('change', setHargaFromSelect);
            qtyInput.addEventListener('input', () => calcRow(tr));

            tr.querySelector('.btnRemove').addEventListener('click', () => {
                tr.remove();
                calcTotal();
            });

            tbody.appendChild(tr);
            rowIndex++;

            // init harga kalau sudah kepilih
            setHargaFromSelect();
        }

        document.getElementById('btnAdd').addEventListener('click', () => addRow());

        // baris awal minimal 1 jika produk tersedia
        if (products.length > 0) addRow();
    </script>
</x-app-layout>
