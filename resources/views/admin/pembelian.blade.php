@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <div class="container py-5">
        <h2 class="mb-4">Kelola Pencapaian Pelanggan</h2>
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered" id="customerTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Toko</th>
                            <th>Total Pembelian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $p)
                            <tr>
                                <td>{{ $p->nama_toko }}</td>
                                <td data-order="{{ $p->total_pembelian }}">Rp
                                    {{ number_format($p->total_pembelian, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                        onclick="openEditModal(
                            {{ $p->id }},
                            '{{ $p->nama_toko }}',
                            {{ $p->total_pembelian }},
                            {{ $p->target1 ?? 'null' }},
                            `{{ $p->deskripsi_hadiah_target1 ?? '' }}`,  // Perhatikan backticks
                            {{ $p->target2 ?? 'null' }},
                            `{{ $p->deskripsi_hadiah_target2 ?? '' }}`,  // Perhatikan backticks
                            `{{ $p->deskripsi_hadiah ?? '' }}`,
                            `{{ $p->gambar_hadiah ? $p->gambar_hadiah : '' }}`)">Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div id="editModal"
            class="modal d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
            <div class="bg-white p-4 rounded w-75 position-relative">
                <h5 class="mb-4">Edit Pencapaian Pelanggan</h5>
                <form action="{{ route('admin.update-pembelian') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pelanggan_id" id="pelangganId">

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Toko</label>
                                <input type="text" class="form-control" id="namaToko" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target 1</label>
                                <input type="text" name="target1" class="form-control" id="target1">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Hadiah Target 1</label>
                                <input type="text" name="deskripsi_hadiah_target1" class="form-control"
                                    id="deskripsiHadiahTarget1">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Hadiah (Umum)</label>
                                <input type="text" name="deskripsi_hadiah" class="form-control" id="deskripsiHadiah">
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Pembelian</label>
                                <input type="text" name="total_pembelian" class="form-control" id="totalPembelian">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target 2</label>
                                <input type="text" name="target2" class="form-control" id="target2">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Hadiah Target 2</label>
                                <input type="text" name="deskripsi_hadiah_target2" class="form-control"
                                    id="deskripsiHadiahTarget2">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gambar Hadiah</label>
                                <input type="file" name="gambar_hadiah" class="form-control">
                                <div id="previewGambar" class="mt-3">
                                    <p class="small text-muted mb-1">Gambar Saat Ini:</p>
                                    <img id="currentImage" src="" alt="Gambar Hadiah"
                                        style="max-width: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- JQuery & DataTables --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                        // Hindari re-inisialisasi jika sudah ada
                        if ($.fn.DataTable.isDataTable('#customerTable')) {
                            $('#customerTable').DataTable().clear().destroy();
                        }

                        // Inisialisasi DataTable dengan semua fitur lengkap
                        $('#customerTable').DataTable({
                                paging: true,
                                lengthChange: true,
                                pageLength: 10,
                                ordering: true,
                                searching: true,
                                info: true,
                                lengthMenu: [
                                    [10, 25, 50, 100, -1],
                                    [10, 25, 50, 100, "Semua"]
                                ],
                                language: {
                                    lengthMenu: "Tampilkan _MENU_ entri",
                                    search: "Cari:",
                                    width: "100%",
                                    allowClear: true,
                                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                                    paginate: {
                                        previous: "Sebelumnya",
                                        next: "Selanjutnya"
                                    },
                                    zeroRecords: "Tidak ada data ditemukan"
                                },
                                columnDefs: [{
                                        targets: 1, 
                                        type: 'num' 
                                    }]
                                });

                            // Format input angka
                            formatNumberInput(document.getElementById('target1')); formatNumberInput(document
                                .getElementById('target2')); formatNumberInput(document.getElementById('totalPembelian'));
                        });

                    function formatNumberInput(input) {
                        input.addEventListener('input', function() {
                            let value = this.value.replace(/\D/g, '');
                            this.value = new Intl.NumberFormat('id-ID').format(value);
                        });
                    }

                    function openEditModal(id, nama, pembelian, target1, deskripsi_hadiah_target1, target2,
                        deskripsi_hadiah_target2,
                        deskripsi_hadiah, gambarUrl) {
                        console.log('Gambar URL:', gambarUrl); // Debugging
                        document.getElementById('editModal').classList.remove('d-none');
                        document.getElementById('pelangganId').value = id;
                        document.getElementById('namaToko').value = nama;
                        document.getElementById('totalPembelian').value = new Intl.NumberFormat('id-ID').format(pembelian);
                        document.getElementById('target1').value = target1 ? new Intl.NumberFormat('id-ID').format(target1) :
                        '';
                        document.getElementById('deskripsiHadiahTarget1').value = deskripsi_hadiah_target1 ?? '';
                        document.getElementById('target2').value = target2 ? new Intl.NumberFormat('id-ID').format(target2) :
                        '';
                        document.getElementById('deskripsiHadiahTarget2').value = deskripsi_hadiah_target2 ?? '';
                        document.getElementById('deskripsiHadiah').value = deskripsi_hadiah ?? '';
                        // document.getElementById('previewGambar').innerHTML = gambarUrl ?
                        //     `<p class="mt-2 text-muted">Gambar Saat Ini:</p><img src="${gambarUrl}" alt="Gambar Hadiah" style="width: 100px;">` :
                        //     '';

                        const previewDiv = document.getElementById('previewGambar');
                        if (gambarUrl && gambarUrl !== '') {
                            previewDiv.innerHTML = `
            <p class="mt-2 text-muted">Gambar Saat Ini:</p>
            <img src="${gambarUrl}" alt="Gambar Hadiah" style="width: 100px;">
        `;
                        } else {
                            previewDiv.innerHTML = '';
                        }
                    }

                    function closeModal() {
                        document.getElementById('editModal').classList.add('d-none');
                    }
        </script>
    @endsection
