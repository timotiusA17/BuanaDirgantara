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
        // buat ambil pelanggan dan masing2 pembelian nya
        $histories = Pembelian::with('pelanggan')->orderBy('tanggal_pembelian', 'desc')->get();
        $pelanggans = Pelanggan::with(['user', 'pembelians' => function ($query) {
            $query->orderBy('tanggal_pembelian', 'desc');
        }])->get();

        return view('admin.dashboard', compact('pelanggans', 'histories'));
    }

    public function getHistoryByPelanggan($pelanggan_id)
    {
        $histories = Pembelian::where('pelanggan_id', $pelanggan_id)
            ->orderBy('tanggal_pembelian', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d/m/Y'),
                    'jumlah' => number_format($item->total_pembelian, 0, ',', '.')
                ];
            });

        return response()->json($histories);
    }

    public function editPembelian(Request $request, $id)
    {
        try {
            $pembelian = Pembelian::findOrFail($id);
            $pelanggan_id = $pembelian->pelanggan_id;
            $jumlahBaru = (int) preg_replace('/[^\d]/', '', $request->jumlah);

            $pembelian->total_pembelian = $jumlahBaru;
            $pembelian->save();

            // Update total pembelian pelanggan
            $total_pembelian = Pembelian::where('pelanggan_id', $pelanggan_id)
                ->sum('total_pembelian');

            Pelanggan::where('id', $pelanggan_id)
                ->update(['total_pembelian' => $total_pembelian]);

            return response()->json([
                'success' => true,
                'jumlah' => $jumlahBaru,
                'pelanggan_id' => $pelanggan_id,
                'total_pembelian' => $total_pembelian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data pembelian.'
            ], 500);
        }
    }

    public function deletePembelian($id)
    {
        try {
            $pembelian = Pembelian::findOrFail($id);
            $pelanggan_id = $pembelian->pelanggan_id;

            $pembelian->delete();

            // Hitung ulang total pembelian 
            $total_pembelian = Pembelian::where('pelanggan_id', $pelanggan_id)
                ->sum('total_pembelian');

            // Update total pembelian di tabel pelanggan
            Pelanggan::where('id', $pelanggan_id)
                ->update(['total_pembelian' => $total_pembelian]);

            return response()->json([
                'success' => true,
                'message' => 'Data pembelian berhasil dihapus.',
                'pelanggan_id' => $pelanggan_id,
                'total_pembelian' => $total_pembelian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pembelian.'
            ], 500);
        }
    }



    public function tambahPembelian(Request $request, $id)
    {
        $request->validate([
            'jumlah_pembelian' => 'required|string',
            'tanggal_pembelian' => 'required|date_format:d/m/Y'
        ]);


        $jumlah = (int) str_replace('.', '', $request->jumlah_pembelian);

        $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_pembelian)->format('Y-m-d');

        $pelanggan = Pelanggan::findOrFail($id);

        // Simpan ke tabel pembelian
        $pembelian = new Pembelian([
            'pelanggan_id' => $id,
            'total_pembelian' => $jumlah,
            'tanggal_pembelian' => $tanggal
        ]);
        $pembelian->save();

        // Update total pembelian di tabel pelanggan
        $pelanggan->total_pembelian += $jumlah;
        $pelanggan->save();

        return redirect()->route('admin.dashboard')->with('success', 'Total pembelian berhasil ditambahkan.');
    }


    public function showCreatePelangganForm()
    {
        $users = User::with('pelanggan')->get();
        $customers = Customer::whereNotIn('PERSC', User::pluck('name'))->get();

        return view('admin.akun', compact('users', 'customers'));
    }


    public function storePelanggan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,pelanggan',
        ]);

        if ($request->role === 'pelanggan') {
            $request->validate([
                'nama_toko' => 'required|string',
                'total_pembelian' => 'required|numeric|min:0',
            ]);

            if ($request->nama_toko !== 'manual') {
                $request->validate([
                    'kodec_toko' => 'required|string|size:4',
                ]);
            }

            $namaToko = $request->nama_toko === 'manual' ? $request->manual_nama_toko : $request->nama_toko;
            $kodec = $request->nama_toko === 'manual' ? null : $request->kodec_toko;
        }

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

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

        // Pelanggan::all()->update([
        //     'reward_image' => $uploadedFile['secure_url']
        // ]);

        Pelanggan::query()->update([
            'reward_image' => $uploadedFile['secure_url']
        ]);

        return redirect()->back()->with('success', 'Gambar reward berhasil diperbarui.');
    }

    public function managePembelian()
    {
        $pelanggan = Pelanggan::all();
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

        $totalPembelian = (int) str_replace('.', '', $request->total_pembelian);
        $target1 = $request->target1 ? (int) str_replace('.', '', $request->target1) : null;
        $target2 = $request->target2 ? (int) str_replace('.', '', $request->target2) : null;

        $pelanggan->total_pembelian = $totalPembelian;
        $pelanggan->target1 = $target1;
        $pelanggan->deskripsi_hadiah_target1 = $request->deskripsi_hadiah_target1;
        $pelanggan->target2 = $target2;
        $pelanggan->deskripsi_hadiah_target2 = $request->deskripsi_hadiah_target2;
        $pelanggan->deskripsi_hadiah = $request->deskripsi_hadiah;

        $updated = $pelanggan->isDirty(); // ngecek datany berubah atau ga

        if ($request->hasFile('gambar_hadiah')) {
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
            $updated = true;
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
            'gambar' => $uploadedFile['secure_url'],
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


            $promo->gambar = $uploadedFile['secure_url'];
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
