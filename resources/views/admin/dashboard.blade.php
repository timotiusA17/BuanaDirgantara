@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Daftar Pelanggan</h2>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table" id="pelanggan-table">
                    <thead>
                        <tr>
                            <th>Nama Toko</th>
                            <th>Total Pembelian</th>
                            <th>Tambah Pembelian</th>
                            <th>History</th>
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
                                <td>
                                    <button class="btn btn-secondary btn-sm history-btn"
                                        data-pelanggan-id="{{ $pelanggan->id }}"
                                        data-nama-toko="{{ $pelanggan->nama_toko }}">
                                        <i class="fas fa-history"></i> History
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal History -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Riwayat Pembelian - <span id="modalTokoName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            <!-- History data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" id="edit_pembelian_id" name="id">
                        <div class="mb-3">
                            <label for="edit_jumlah" class="form-label">Jumlah</label>
                            <input type="text" class="form-control input-currency" id="edit_jumlah" name="jumlah"
                                required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.querySelectorAll('.input-currency').forEach(input => {
            new Cleave(input, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 0
            });
        });

        document.querySelectorAll('.date-input').forEach(input => {
            new Cleave(input, {
                date: true,
                datePattern: ['d', 'm', 'Y'],
                delimiter: '/'
            });
        });

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

        let pelangganTable;
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#pelanggan-table')) {
                pelangganTable = $('#pelanggan-table').DataTable({
                    dom: '<"d-flex justify-content-between align-items-center"f>tip',
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
            }

            $(document).on('click', '.history-btn', function() {
                const pelangganId = $(this).data('pelanggan-id');
                const namaToko = $(this).data('nama-toko');

                $('#modalTokoName').text(namaToko);

                $.get(`/admin/pembelian/history/${pelangganId}`, function(data) {
                    const tbody = $('#history-table-body');
                    tbody.empty();

                    data.forEach(history => {
                        const row = `
                            <tr data-id="${history.id}">
                                <td>${history.tanggal}</td>
                                <td class="jumlah-display">Rp ${history.jumlah}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-history">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-history" data-id="${history.id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    $('#historyModal').modal('show');
                });
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

                const row = button.closest('tr');
                const namaToko = row.querySelector('td:first-child').innerText.trim();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    html: `Apakah yakin ingin menambahkan sebesar <strong>Rp ${jumlahValue}</strong> ke toko <strong>${namaToko}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        $(document).on('click', '.edit-history', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const jumlah = row.find('.jumlah-display').text().replace(/[^\d]/g, '');

            $('#edit_pembelian_id').val(id);
            $('#edit_jumlah').val(jumlah);

            new Cleave('#edit_jumlah', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 0
            });

            $('#editModal').modal('show');
        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#edit_pembelian_id').val();
            const jumlah = $('#edit_jumlah').val().replace(/[^\d]/g, '');

            $.ajax({
                url: `/admin/pembelian/edit/${id}`,
                type: 'POST',
                data: {
                    jumlah: jumlah,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $(`tr[data-id="${id}"] .jumlah-display`).text(
                            `Rp ${parseInt(response.jumlah).toLocaleString('id-ID')}`
                        );

                        $(`tr[data-pelanggan-id="${response.pelanggan_id}"] td:nth-child(2)`)
                            .text('Rp ' + formatNumber(response.total_pembelian));

                        $('#editModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data pembelian berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbarui data'
                    });
                }
            });
        });

        $(document).on('click', '.delete-history', function() {
            const id = $(this).data('id');
            const button = $(this);
            const historyModal = $('#historyModal'); 

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pembelian akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/pembelian/delete/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Hapus baris dari tabel history
                                button.closest('tr').remove();

                                // Update total pembelian di tabel utama
                                $(`tr:has(td:contains("${response.pelanggan_id}"))`)
                                    .find('td:nth-child(2)')
                                    .text('Rp ' + formatNumber(response.total_pembelian));

                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );

                                // kalo history udh ga ada, close modal
                                if ($('#history-table-body tr').length === 0) {
                                    historyModal.modal('hide');
                                }
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan pada server.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
@endsection
