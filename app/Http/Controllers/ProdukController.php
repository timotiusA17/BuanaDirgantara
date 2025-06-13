<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Produk;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

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


        if ($request->hasFile('gambar')) {
            $path = 'katalog';
            $file_extension = $request->file('gambar')->getClientOriginalName();
            $fileName = pathinfo($file_extension, PATHINFO_FILENAME);
            $publicId = date('Y-m-d_His') . '_' . $fileName;

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);

            try {
                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('gambar')->getRealPath(),
                    [
                        'folder' => $path,
                        'public_id' => $publicId,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error: ' . $e->getMessage());
                return back()->with('error', 'Gagal upload gambar ke Cloudinary.');
            }
        }

        Produk::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'satuan' => $request->satuan,
            'gambar' => $uploadedFile['secure_url'], 
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
        $produk = Barang::where('KODEB', $id)->first();
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $produk->NAMAB = $request->nama;
        $produk->HJUALB = $request->harga;
        $produk->SATUANB = $request->satuan;
        if ($request->hasFile('gambar')) {
            $path = 'promo';
            $produk->gambar = $path;
            $file_extension = $request->file('gambar')->getClientOriginalName();
            $fileName = pathinfo($file_extension, PATHINFO_FILENAME);
            $publicId = date('Y-m-d_His') . '_' . $fileName;

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);

            try {
                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('gambar')->getRealPath(),
                    [
                        'folder' => $path,
                        'public_id' => $publicId,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error: ' . $e->getMessage());
                return back()->with('error', 'Gagal upload gambar ke Cloudinary.');
            }
        }
        $produk->gambar = $uploadedFile['secure_url'];
        $produk->save();

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }
}
