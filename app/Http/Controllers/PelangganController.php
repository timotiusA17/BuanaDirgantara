<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Promo;


use App\Models\Produk;



class PelangganController extends Controller
{
    public function profile()
    {
        return view('pelanggan.profile');
    }

    public function leaderboard()
    {
        $pelanggans = Pelanggan::with('user')->get();
        $userId = auth()->user()->id;
        $rewardImage = Pelanggan::whereNotNull('reward_image')->value('reward_image');

        // Sort all pelanggans by total_pembelian descending
        $sortedPelanggans = $pelanggans->sortByDesc('total_pembelian')->values(); // <- tambah ->values() untuk reset index

        // Temukan ranking user login
        $userRank = null;
        foreach ($sortedPelanggans as $index => $p) {
            if ($p->user_id == $userId) {
                $userRank = $index + 1; // +1 karena index dimulai dari 0
                break;
            }
        }

        // Prepare data for general leaderboard (rank 4+)
        $generalChartData = [];
        $labelCounter = 'A';

        foreach ($sortedPelanggans->slice(3) as $p) {
            $label = $p->user_id == $userId ? $p->nama_toko : 'Pelanggan ' . $labelCounter++;
            $generalChartData[] = [
                'label' => $label,
                'total' => $p->total_pembelian,
                'user_id' => $p->user_id, // <- tambahkan ini untuk JavaScript chart
            ];
        }

        // Prepare data for top 3 chart
        $top3ChartData = [];
        foreach ($sortedPelanggans->take(3) as $p) {
            $label = $p->user_id == $userId ? $p->nama_toko : substr($p->nama_toko, 0, 3) . '***';
            $top3ChartData[] = [
                'label' => $label,
                'total' => $p->total_pembelian,
            ];
        }

        // Get top 3 pelanggans
        $top3 = $sortedPelanggans->take(3)->values();

        return view('pelanggan.leaderboard', [
            'generalChartData' => $generalChartData,
            'top3ChartData' => $top3ChartData,
            'top3' => $top3,
            'userId' => $userId,
            'userRank' => $userRank, // <- tambahkan ini
            'rewardImage' => $rewardImage
        ]);
    }



    public function home()
    {
        $produks = Produk::all();
        $barangs = Barang::all();
        return view('home', compact('produks', 'barangs'));
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama tidak cocok.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function updateUsername(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'new_username' => 'required|string|min:3|max:255|unique:users,name,' . $user->id,
            'new_username_confirmation' => 'required|same:new_username',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update username
        $user->name = $request->new_username;
        $user->save();

        return redirect()->back()->with('success', 'Username berhasil diperbarui.');
    }

    public function personalAchievement()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();

        if (!$pelanggan) {
            return redirect()->back()->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $totalPembelian = $pelanggan->total_pembelian;
        $target1 = $pelanggan->target1 ?? 0;
        $target2 = $pelanggan->target2 ?? 0;
        $deskripsi_hadiah_target1 = $pelanggan->deskripsi_hadiah_target1;
        $deskripsi_hadiah_target2 = $pelanggan->deskripsi_hadiah_target2;
        $deskripsi_hadiah = $pelanggan->deskripsi_hadiah;

        // JANGAN tambahkan lagi "gambar_hadiah/" karena sudah disimpan lengkap
        $gambar_hadiah = $pelanggan->gambar_hadiah
            ? Storage::url($pelanggan->gambar_hadiah)
            : null;

        $target_aktif = $totalPembelian < $target1 ? $target1 : $target2;
        $progress = $target_aktif > 0 ? ($totalPembelian / $target_aktif) * 100 : 0;
        $progress = min(100, round($progress, 1));

        // Ambil data pembelian per bulan
        $pembelianPerBulan = Pembelian::where('pelanggan_id', $pelanggan->id)
            ->selectRaw('EXTRACT(MONTH FROM tanggal_pembelian) as bulan, SUM(total_pembelian) as total')
            ->whereRaw('EXTRACT(YEAR FROM tanggal_pembelian) = ?', [date('Y')])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Format data untuk chart
        $chartData = [];
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        // Inisialisasi semua bulan dengan nilai 0
        foreach (range(1, 12) as $bulan) {
            $chartData[$bulan] = 0;
        }

        // Isi data yang ada
        foreach ($pembelianPerBulan as $item) {
            $chartData[$item->bulan] = $item->total;
        }

        return view('pelanggan.personal-achievement', compact(
            'totalPembelian',
            'target1',
            'target2',
            'deskripsi_hadiah_target1',
            'deskripsi_hadiah_target2',
            'progress',
            'deskripsi_hadiah',
            'gambar_hadiah',
            'chartData',
            'bulanLabels'
        ));
    }


    public function promoPage()
    {
        $promos = Promo::latest()->get();
        return view('pelanggan.promo', compact('promos'));
    }
}
