<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = $r->query('q');

        $products = Product::query()
            ->when($q, fn($s) => $s->where('nama_produk', 'like', "%{$q}%"))
            ->orderBy('nama_produk')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'q'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'nama_produk' => 'required|string|max:255|unique:products,nama_produk',
            'satuan' => 'required|string|max:30',
            'harga' => 'required|integer|min:0',
        ]);

        Product::create([
            'nama_produk' => $r->nama_produk,
            'satuan' => $r->satuan,
            'harga' => $r->harga,
            'is_active' => true,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $r, Product $product)
    {
        $r->validate([
            'nama_produk' => 'required|string|max:255|unique:products,nama_produk,' . $product->id,
            'satuan' => 'required|string|max:30',
            'harga' => 'required|integer|min:0',
        ]);

        $product->update([
            'nama_produk' => $r->nama_produk,
            'satuan' => $r->satuan,
            'harga' => $r->harga,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success', 'Status produk berhasil diubah.');
    }
}
