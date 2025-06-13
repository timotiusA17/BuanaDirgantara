<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin' || Auth::user()->role === 'owner') {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role === 'pelanggan') {
                return redirect()->route('home');
            }
        }

        return back()->withErrors(['name' => 'Username atau password salah.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function loginAsGuest()
    {
        // Auth::logout();
        $produks = Produk::all();
        $barangs = Barang::orderBy('gambar')->get();
        return view('home-guest' ,compact('produks', 'barangs'));
    }

}
