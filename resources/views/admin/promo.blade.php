@extends('layouts.admin')
@section('content')

    <style>
        .promo-card {
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

        .promo-card .promo-image {
            transition: 0.3s;
        }

        .promo-card .promo-actions {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 10px;
            opacity: 0;
            z-index: 2;
            transition: 0.3s;
        }

        .promo-card:hover .promo-image {
            filter: blur(3px);
        }

        .promo-card:hover .promo-actions {
            opacity: 1;
        }
    </style>

    <div class="container py-4">
        <h2 class="mb-4">📣 Daftar Promo</h2>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Button Add --}}
        <button onclick="openPromoModal()" class="btn btn-primary mb-4">+ Add Promo</button>

        {{-- Promo Cards --}}
        <div class="row">
            @forelse ($promos as $promo)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm promo-card">
                        @if ($promo->gambar)
                            <img src="{{$promo->gambar}}" alt="Promo Image"
                                class="card-img-top promo-image bg-white"
                                style="height: 200px; width: 100%; object-fit: contain; border-bottom: 1px solid #ddd;">
                        @endif

                        <div class="promo-actions">
                            <button class="btn p-0 border-0 bg-transparent text-primary fs-4"
                                onclick="openEditPromoModal({{ $promo }})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <form action="{{ route('admin.promo.destroy', $promo->id) }}" method="POST"
                                style="display: inline;" title="Delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bg-transparent text-danger fs-4"
                                    onclick="return confirm('Yakin ingin menghapus promo ini?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $promo->nama }}</h5>
                            <h5 class="card-title">{{ $promo->deskripsi }}</h5>
                            <p class="card-text">
                                <strong>Diskon:</strong> {{ $promo->diskon }}% <br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p>Tidak ada promo tersedia.</p>
            @endforelse
        </div>


        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Modal --}}
        <div id="promoModal"
            class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center bg-dark bg-opacity-75"
            style="backdrop-filter: blur(5px); z-index: 9999;">
            <div class="bg-white p-4 rounded shadow-lg w-75 position-relative">
                <h4 class="mb-3">Tambah Promo Baru</h4>
                <form action="{{ route('admin.promo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Nama Promo</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Durasi Promo</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Diskon (%)</label>
                        <input type="number" name="diskon" class="form-control" min="0" max="100" required>
                    </div>

                    <div class="mb-3">
                        <label>Gambar Promo</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" onclick="closePromoModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Promo --}}
        <div id="editPromoModal"
            class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center bg-dark bg-opacity-75"
            style="backdrop-filter: blur(5px); z-index: 9999;">
            <div class="bg-white p-4 rounded shadow-lg w-75 position-relative">
                <h4 class="mb-3">Edit Promo</h4>
                <form id="editPromoForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label>Nama Promo</label>
                        <input type="text" name="nama" id="editNama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <input type="text" name="deskripsi" id="editDeskripsi" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Durasi Promo</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="tanggal_mulai" id="editMulai" class="form-control" required>
                            <input type="date" name="tanggal_selesai" id="editSelesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Diskon (%)</label>
                        <input type="number" name="diskon" id="editDiskon" class="form-control" min="0"
                            max="100" required>
                    </div>

                    <div class="mb-3">
                        <label>Gambar Promo (kosongkan jika tidak ingin ganti)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" onclick="closeEditPromoModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>


        <script>
            function openPromoModal() {
                document.getElementById('promoModal').classList.remove('d-none');
            }

            function openEditPromoModal(promo) {
                document.getElementById('editPromoForm').action = `/admin/promo/${promo.id}`;
                document.getElementById('editNama').value = promo.nama;
                document.getElementById('editDeskripsi').value = promo.deskripsi;
                document.getElementById('editMulai').value = promo.tanggal_mulai;
                document.getElementById('editSelesai').value = promo.tanggal_selesai;
                document.getElementById('editDiskon').value = promo.diskon;
                document.getElementById('editPromoModal').classList.remove('d-none');
            }

            function closeEditPromoModal() {
                document.getElementById('editPromoModal').classList.add('d-none');
            }

            function closePromoModal() {
                document.getElementById('promoModal').classList.add('d-none');
            }
        </script>

    @endsection
