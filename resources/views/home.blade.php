@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 px-4">
    {{-- Sambutan --}}
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <h2 class="text-2xl font-bold mb-2">
            Welcome, <span class="text-black">{{ Auth::user()->pelanggan->nama_toko }}</span>
        </h2>
        <p class="text-gray-600">
            Selamat datang di platform kami! Jelajahi katalog produk kami untuk kebutuhan bahan bangunan Anda.
        </p>
    </div>

    {{-- Katalog --}}
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Katalog Produk</h3>
            <input type="text" id="searchInput" placeholder="Cari produk..." class="border px-3 py-2 rounded w-64"
                onkeyup="filterProducts()">
        </div>

        <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach ($barangs as $produk)
                <div class="product-card bg-white border border-black rounded-lg shadow p-3 flex flex-col items-center text-center"
                    data-name="{{ strtolower($produk->NAMAB) }}">
                    <img src="{{$produk->gambar}}" class="w-full h-32 object-contain mb-3"
                        alt="{{ $produk->NAMAB }}">
                    <h4 class="text-md font-semibold text-gray-800">{{ $produk->NAMAB }}</h4>
                    <p class="text-gray-600 text-sm">Rp{{ number_format($produk->HJUALB, 0, ',', '.') }} /
                        {{ $produk->SATUANB }}</p>
                </div>
            @endforeach
        </div>

        @if ($barangs->isEmpty())
            <p class="text-gray-500 mt-4">Belum ada produk yang tersedia.</p>
        @endif

        {{-- Pagination --}}
        <div id="pagination" class="flex justify-center mt-6 space-x-2"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const itemsPerPage = 10;
    let currentPage = 1;
    const maxPagesToShow = 100;

    function paginateProducts() {
        const cards = document.querySelectorAll('.product-card');
        const keyword = document.getElementById('searchInput')?.value?.toLowerCase() ?? '';
        const pagination = document.getElementById('pagination');

        const visibleCards = Array.from(cards).filter(card => {
            const name = card.getAttribute('data-name');
            return name.includes(keyword);
        });

        const totalPages = Math.min(Math.ceil(visibleCards.length / itemsPerPage), maxPagesToShow);

        // Reset display
        cards.forEach(card => card.style.display = 'none');

        visibleCards.forEach((card, index) => {
            if (index >= (currentPage - 1) * itemsPerPage && index < currentPage * itemsPerPage) {
                card.style.display = '';
            }
        });

        pagination.innerHTML = '';

        const createButton = (label, page, disabled = false) => {
            const btn = document.createElement('button');
            btn.innerText = label;
            btn.disabled = disabled;
            btn.className =
                `px-3 py-1 border rounded ${page === currentPage ? 'bg-blue-500 text-white' : 'bg-white text-black'} hover:bg-blue-100`;
            btn.onclick = () => {
                currentPage = page;
                paginateProducts();
            };
            return btn;
        };

        if (totalPages > 1) {
            pagination.appendChild(createButton('«', 1, currentPage === 1));
            pagination.appendChild(createButton('<', currentPage - 1, currentPage === 1));

            // Range pagination tampil max 7 tombol halaman secara dinamis
            let start = Math.max(currentPage - 3, 1);
            let end = Math.min(start + 6, totalPages);
            if (end - start < 6) start = Math.max(end - 6, 1);

            for (let i = start; i <= end; i++) {
                pagination.appendChild(createButton(i, i));
            }

            pagination.appendChild(createButton('>', currentPage + 1, currentPage === totalPages));
            pagination.appendChild(createButton('»', totalPages, currentPage === totalPages));
        }
    }

    function filterProducts() {
        currentPage = 1;
        paginateProducts();
    }

    document.addEventListener('DOMContentLoaded', paginateProducts);
</script>
@endsection
