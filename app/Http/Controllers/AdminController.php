<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Promo;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil semua pelanggan beserta user-nya (eager load) dan pembeliannya
        $pelanggans = Pelanggan::with(['user', 'pembelians' => function ($query) {
            $query->orderBy('tanggal_pembelian', 'desc');
        }])->get();

        return view('admin.dashboard', compact('pelanggans'));
    }

    public function tambahPembelian(Request $request, $id)
    {
        $request->validate([
            'jumlah_pembelian' => 'required|string', // jangan numeric karena masih ada titik
            'tanggal_pembelian' => 'required|date_format:d/m/Y'
        ]);

        // Ubah jumlah menjadi integer dari string '1.000.000' → 1000000
        $jumlah = (int) str_replace('.', '', $request->jumlah_pembelian);

        // Ubah tanggal dari dd/mm/yyyy ke yyyy-mm-dd
        $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_pembelian)->format('Y-m-d');

        $pelanggan = Pelanggan::findOrFail($id);

        // Simpan ke tabel pembelians
        $pembelian = new Pembelian([
            'pelanggan_id' => $id,
            'total_pembelian' => $jumlah,
            'tanggal_pembelian' => $tanggal
        ]);
        $pembelian->save();

        // Update total pembelian di tabel pelanggans
        $pelanggan->total_pembelian += $jumlah;
        $pelanggan->save();

        return redirect()->route('admin.dashboard')->with('success', 'Total pembelian berhasil ditambahkan.');
    }


    public function showCreatePelangganForm()
    {
        $users = User::with('pelanggan')->get();
        // Ambil customer yang belum memiliki user (akun)
        $customers = Customer::whereNotIn('PERSC', User::pluck('name'))->get();

        return view('admin.akun', compact('users', 'customers'));
    }


    public function storePelanggan(Request $request)
    {
        // Validasi umum
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,pelanggan',
        ]);

        // Validasi tambahan jika role = pelanggan
        if ($request->role === 'pelanggan') {
            $request->validate([
                'nama_toko' => 'required|string',
                'total_pembelian' => 'required|numeric|min:0',
            ]);

            // Validasi kodec hanya jika bukan input manual
            if ($request->nama_toko !== 'manual') {
                $request->validate([
                    'kodec_toko' => 'required|string|size:4',
                ]);
            }

            // Gunakan nama toko dari input manual jika dipilih
            $namaToko = $request->nama_toko === 'manual' ? $request->manual_nama_toko : $request->nama_toko;
            $kodec = $request->nama_toko === 'manual' ? null : $request->kodec_toko;
        }

        // Buat user
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Jika role-nya pelanggan, buat data tambahan di tabel pelanggan
        if ($request->role === 'pelanggan') {
            Pelanggan::create([
                'user_id' => $user->id,
                'nama_toko' => $namaToko,
                'KODEC_toko' => $kodec,
                'total_pembelian' => $request->total_pembelian,
            ]);
        }

        return redirect()->back()->with('success', 'Akun ' . $request->role . ' berhasil dibuat.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Akun owner tidak bisa dihapus.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }

    public function editPelanggan($id)
    {
        $user = User::findOrFail($id);
        $pelanggan = $user->role === 'pelanggan' ? Pelanggan::where('user_id', $id)->first() : null;

        return view('admin.edit-akun', compact('user', 'pelanggan'));
    }

    public function updatePelanggan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:users,name,' . $user->id,
            'password' => 'nullable|string|min:8',
            'nama_toko' => 'nullable|string',
            'total_pembelian' => 'nullable|numeric|min:0',
        ]);

        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($user->role === 'pelanggan') {
            $pelanggan = Pelanggan::where('user_id', $user->id)->first();
            $pelanggan->nama_toko = $request->nama_toko;
            $pelanggan->total_pembelian = $request->total_pembelian;
            $pelanggan->save();
        }

        return redirect()->route('admin.akun')->with('success', 'Akun berhasil diperbarui.');
    }

    public function updateRewardImage(Request $request)
    {
        $request->validate([
            'reward_image' => 'required|image|max:2048'
        ]);

        $path = 'reward';
        $file_extension = $request->file('reward_image')->getClientOriginalName();
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
                $request->file('reward_image')->getRealPath(),
                [
                    'folder' => $path,
                    'public_id' => $publicId,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            return back()->with('error', 'Gagal upload gambar ke Cloudinary.');
        }

        // Simpan hanya satu gambar (misalnya pada pelanggan dengan id 1 sebagai "dummy")
        Pelanggan::where('id', 1)->update([
            'reward_image' => $uploadedFile['secure_url']
        ]);

        return redirect()->back()->with('success', 'Gambar reward berhasil diperbarui.');
    }

    public function managePembelian()
    {
        $pelanggan = Pelanggan::all(); // Mengambil semua data pelanggan
        return view('admin.pembelian')->with('pelanggan', $pelanggan);
    }

    public function updatePembelian(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'total_pembelian' => 'required|string',
            'target1' => 'nullable|string',
            'deskripsi_hadiah_target1' => 'nullable|string',
            'target2' => 'nullable|string',
            'deskripsi_hadiah_target2' => 'nullable|string',
            'deskripsi_hadiah' => 'nullable|string',
            'gambar_hadiah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

        // Convert angka format Indonesia menjadi integer
        $totalPembelian = (int) str_replace('.', '', $request->total_pembelian);
        $target1 = $request->target1 ? (int) str_replace('.', '', $request->target1) : null;
        $target2 = $request->target2 ? (int) str_replace('.', '', $request->target2) : null;

        // Simpan perubahan ke model
        $pelanggan->total_pembelian = $totalPembelian;
        $pelanggan->target1 = $target1;
        $pelanggan->deskripsi_hadiah_target1 = $request->deskripsi_hadiah_target1;
        $pelanggan->target2 = $target2;
        $pelanggan->deskripsi_hadiah_target2 = $request->deskripsi_hadiah_target2;
        $pelanggan->deskripsi_hadiah = $request->deskripsi_hadiah;

        $updated = $pelanggan->isDirty(); // cek apakah data berubah

        // Tangani upload gambar baru
        if ($request->hasFile('gambar_hadiah')) {
            // if ($pelanggan->gambar_hadiah && Storage::disk('public')->exists($pelanggan->gambar_hadiah)) {
            //     Storage::disk('public')->delete($pelanggan->gambar_hadiah); // Hapus gambar lama
            $path = 'target';
            $file_extension = $request->file('gambar_hadiah')->getClientOriginalName();
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
                    $request->file('gambar_hadiah')->getRealPath(),
                    [
                        'folder' => $path,
                        'public_id' => $publicId,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error: ' . $e->getMessage());
                return back()->with('error', 'Gagal upload gambar ke Cloudinary.');
            }

            $path = $request->file('gambar_hadiah')->store('gambar_hadiah', 'public');
            $pelanggan->gambar_hadiah = $uploadedFile['secure_url'];
            $updated = true; // karena file diubah
        }

        $pelanggan->save();

        return redirect()->route('admin.pembelian')->with(
            $updated ? 'success' : 'error',
            $updated ? 'Data pencapaian berhasil diperbarui.' : 'Tidak ada perubahan data.'
        );
    }

    public function promoPage()
    {
        $promos = Promo::latest()->get();
        return view('admin.promo', compact('promos'));
    }

    public function storePromo(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'diskon' => 'required|numeric|min:0|max:100',
            'gambar' => 'nullable|image|max:2048',
        ]);

        // $path = $request->hasFile('gambar')
        //     ? $request->file('gambar')->store('promo', 'public')
        //     : null;

        $path = 'promo';
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

        Promo::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'diskon' => $request->diskon,
            'gambar' => $uploadedFile['secure_url'], // atau 'url' jika tidak pakai SSL
        ]);

        return redirect()->route('admin.promo')->with('success', 'Promo berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'diskon' => 'required|numeric|min:0|max:100',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $promo = Promo::findOrFail($id);
        $promo->nama = $request->nama;
        $promo->deskripsi = $request->deskripsi;
        $promo->tanggal_mulai = $request->tanggal_mulai;
        $promo->tanggal_selesai = $request->tanggal_selesai;
        $promo->diskon = $request->diskon;

        if ($request->hasFile('gambar')) {
            // if ($promo->gambar && Storage::disk('public')->exists($promo->gambar)) {
            //     Storage::disk('public')->delete($promo->gambar);
            $path = 'promo';
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


            // $path = $request->file('gambar')->store('promo', 'public');
            $promo->gambar = $$uploadedFile['secure_url'];
        }

        $promo->save();

        return redirect()->route('admin.promo')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);

        if ($promo->gambar && Storage::disk('public')->exists($promo->gambar)) {
            Storage::disk('public')->delete($promo->gambar);
        }

        $promo->delete();

        return redirect()->route('admin.promo')->with('success', 'Promo berhasil dihapus.');
    }
}
