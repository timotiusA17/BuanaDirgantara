@extends('layouts.guest') {{-- Ganti layout --}}
@section('content')
    <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4 text-center">Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="name" class="w-full p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded-lg">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-lg mb-2">Login</button>
        </form>

        <!-- Login as Guest -->
        <a href="{{ route('guest.login') }}" class="block text-center w-full bg-gray-300 text-gray-700 p-2 rounded-lg">
            Login as Guest
        </a>
    </div>
@endsection
