<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produks = Produk::all();
        $barangs = Barang::all();
        return view('admin.katalog', compact('produks', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required|in:pcs,dus',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'satuan' => $request->satuan,
            'gambar' => $path,
        ]);

        return redirect()->route('admin.katalog')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function deleteProduk($KODEB)
    {
        $produk = Barang::where('KODEB', $KODEB);
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.katalog')->with('success', 'Produk berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        // Cara 1: Menggunakan first()
        $produk = Barang::where('KODEB', $id)->first();
        // dd($produk);
        // Atau Cara 2: Menggunakan find() jika KODEB adalah primary key
        // $produk = Barang::find($id);

        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $produk->NAMAB = $request->nama;
        $produk->HJUALB = $request->harga;
        $produk->SATUANB = $request->satuan;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $path = $file->store('produk', 'public');
            $produk->gambar = $path;
        }
        $produk->save();

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }
}
