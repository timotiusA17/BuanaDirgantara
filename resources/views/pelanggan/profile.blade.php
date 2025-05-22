@extends('layouts.app')

@section('content')
<div x-data="{ showUsernameModal: false, showPasswordModal: false }" class="min-h-screen bg-gray-100 py-10">

    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-lg space-y-6">
        <h1 class="text-3xl font-bold text-center text-gray-800">Profil Saya</h1>

        <div class="text-gray-700 space-y-1">
            <p><span class="font-semibold">Username:</span> {{ Auth::user()->name }}</p>
            <p><span class="font-semibold">Nama Toko:</span> {{ Auth::user()->pelanggan->nama_toko }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button @click="showUsernameModal = true" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                Change Username
            </button>
            <button @click="showPasswordModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Change Password
            </button>
        </div>

        {{-- Alert & Error Message --}}
        @if (session('success'))
            <div class="text-green-600 bg-green-50 border border-green-200 rounded p-2">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="text-red-600 bg-red-50 border border-red-200 rounded p-2">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Modal Ganti Username --}}
    <div x-show="showUsernameModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl">
            <h2 class="text-lg font-semibold mb-4">Ubah Username</h2>
            <form method="POST" action="{{ route('pelanggan.updateUsername') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username Lama</label>
                    <input type="text" name="old_username" value="{{ Auth::user()->name }}" readonly class="w-full bg-gray-100 border border-gray-300 px-3 py-2 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username Baru</label>
                    <input type="text" name="new_username" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Username Baru</label>
                    <input type="text" name="new_username_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="showUsernameModal = false" class="text-gray-500 hover:underline">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Ganti Password --}}
    <div x-show="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl">
            <h2 class="text-lg font-semibold mb-4">Ubah Password</h2>
            <form method="POST" action="{{ route('pelanggan.updatePassword') }}" class="space-y-4">
                @csrf
    
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="old_password" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
    
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="new_password" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
    
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="w-full border border-gray-300 px-3 py-2 rounded-lg" required>
                </div>
    
                <div class="flex justify-between pt-4">
                    <button type="button" @click="showPasswordModal = false" class="text-gray-500 hover:underline">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
</div>

{{-- Tambahkan Alpine.js --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
