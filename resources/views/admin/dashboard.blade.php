@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Daftar Pelanggan</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table" id="pelanggan-table">
                    <thead>
                        <tr>
                            <th>Nama Toko</th>
                            <th>Total Pembelian</th>
                            <th>Tambah Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggans as $pelanggan)
                            <tr>
                                <td>{{ $pelanggan->nama_toko }}</td>
                                <td>Rp {{ number_format($pelanggan->total_pembelian, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('admin.tambahPembelian', $pelanggan->id) }}" method="POST">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <input type="text" name="jumlah_pembelian"
                                                    class="form-control input-currency" placeholder="Jumlah" required>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <input type="text" name="tanggal_pembelian"
                                                        class="form-control date-input" id="datepicker_{{ $pelanggan->id }}"
                                                        placeholder="DD/MM/YYYY" required>
                                                    <span class="input-group-text calendar-btn"
                                                        data-target="#datepicker_{{ $pelanggan->id }}">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-primary w-100 tambah-btn">+</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Styles --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <style>
        .input-group-text {
            cursor: pointer;
            background-color: #fff;
        }

        .datepicker {
            z-index: 1151 !important;
        }
    </style>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Format input jumlah pembelian
        document.querySelectorAll('.input-currency').forEach(input => {
            new Cleave(input, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 0
            });
        });

        // Format input tanggal
        document.querySelectorAll('.date-input').forEach(input => {
            new Cleave(input, {
                date: true,
                datePattern: ['d', 'm', 'Y'],
                delimiter: '/'
            });
        });

        // Datepicker + tombol kalender
        @foreach ($pelanggans as $pelanggan)
            $('#datepicker_{{ $pelanggan->id }}').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });

            $('[data-target="#datepicker_{{ $pelanggan->id }}"]').on('click', function() {
                $('#datepicker_{{ $pelanggan->id }}').datepicker('show');
            });
        @endforeach

        // Inisialisasi DataTables
        $(document).ready(function() {
            $('#pelanggan-table').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "→",
                        previous: "←"
                    },
                    zeroRecords: "Tidak ditemukan",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total entri)"
                }
            });
        });

        document.querySelectorAll('.tambah-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const form = button.closest('form');
                const jumlahInput = form.querySelector('input[name="jumlah_pembelian"]');
                const tanggalInput = form.querySelector('input[name="tanggal_pembelian"]');

                const jumlahValue = jumlahInput.value.trim();
                const tanggalValue = tanggalInput.value.trim();

                if (!jumlahValue || !tanggalValue) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: !jumlahValue ? 'Input pembelian harus diisi!' :
                            'Tanggal pembelian harus diisi!',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pembelian akan ditambahkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();

                        // Opsional: Tampilkan notifikasi setelah submit berhasil
                        // Catatan: Ini hanya terlihat jika form tidak redirect langsung
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pembelian berhasil ditambahkan!'
                        });
                    }
                });
            });
        });
    </script>
@endsection
