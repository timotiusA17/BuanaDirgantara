@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-center mb-8 text-indigo-700">🎁 Promo Menarik Untukmu</h2>

    @if ($promos->isEmpty())
        <p class="text-center text-gray-500">Belum ada promo yang tersedia saat ini.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($promos as $promo)
                <div class="border border-black-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 bg-white">
                    @if ($promo->gambar)
                        <img src="{{ $promo->gambar }}" alt="Promo Image"
                             class="w-full h-40 object-cover border-b">
                    @endif
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-indigo-800 mb-1">{{ $promo->nama }}</h3>
                        <p class="text-sm text-gray-700 mb-2">{{ $promo->deskripsi }}</p>

                        <div class="text-sm">
                            <span class="text-green-600 font-bold">Diskon {{ $promo->diskon }}%</span>
                        </div>

                        <div class="text-xs text-gray-500 mt-2">
                            Berlaku: <br>
                            {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                            - 
                            {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
