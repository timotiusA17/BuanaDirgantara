@extends('layouts.app')

@section('content')
<div x-data="{ showUsernameModal: false, showPasswordModal: false }" class="min-h-screen bg-gradient-to-tr from-indigo-100 via-white to-indigo-200 py-12 px-4">

    <div class="max-w-xl mx-auto bg-white p-8 rounded-3xl shadow-2xl space-y-6 border border-gray-100">
        <h1 class="text-4xl font-bold text-center text-indigo-700 flex items-center justify-center gap-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M5.121 17.804A13.937 13.937 0 0112 15c2.2 0 4.263.532 6.121 1.477M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Profil Saya
        </h1>

        <div class="text-gray-700 space-y-2 text-lg">
            <p><span class="font-semibold">👤 Username:</span> {{ Auth::user()->name }}</p>
            <p><span class="font-semibold">🏪 Nama Toko:</span> {{ Auth::user()->pelanggan->nama_toko }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-2">
            <button @click="showUsernameModal = true" class="flex items-center justify-center gap-2 bg-yellow-500 text-white px-5 py-2.5 rounded-xl hover:bg-yellow-600 transition shadow">
                ✏️ Ubah Username
            </button>
            <button @click="showPasswordModal = true" class="flex items-center justify-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 transition shadow">
                🔐 Ubah Password
            </button>
        </div>

        {{-- Alert & Error Message --}}
        @if (session('success'))
            <div class="flex items-center gap-2 text-green-800 bg-green-50 border border-green-200 rounded-lg p-3 mt-4">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="text-red-700 bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Modal Username --}}
    <div x-show="showUsernameModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4">
            <h2 class="text-xl font-semibold text-indigo-700">Ubah Username</h2>
            <form method="POST" action="{{ route('pelanggan.updateUsername') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Username Lama</label>
                    <input type="text" name="old_username" value="{{ Auth::user()->name }}" readonly class="w-full bg-gray-100 border border-gray-300 px-3 py-2 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Username Baru</label>
                    <input type="text" name="new_username" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Konfirmasi Username Baru</label>
                    <input type="text" name="new_username_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showUsernameModal = false" class="text-gray-500 hover:underline">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Password --}}
    <div x-show="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl space-y-4">
            <h2 class="text-xl font-semibold text-indigo-700">Ubah Password</h2>
            <form method="POST" action="{{ route('pelanggan.updatePassword') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Password Lama</label>
                    <input type="password" name="old_password" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password Baru</label>
                    <input type="password" name="new_password" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showPasswordModal = false" class="text-gray-500 hover:underline">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
