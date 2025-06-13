<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery (wajib) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Katalog Produk</h2>

        <!-- Tombol Tambah Produk -->
        <button class="btn btn-primary" onclick="openModal()">+ Tambah Produk</button>

        <!-- Modal Tambah Produk -->
        <div id="productModal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h4>Tambah Produk</h4>
                <form action="{{ route('admin.katalog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label>Nama Produk:</label>
                    <input type="text" name="nama" class="form-control" required>

                    <label>Harga Produk:</label>
                    <input type="number" name="harga" class="form-control" required>

                    <label>Satuan:</label>
                    <select name="satuan" class="form-control" required>
                        <option value="pcs">pcs</option>
                        <option value="dus">dus</option>
                    </select>

                    <label>Gambar Produk:</label>
                    <input type="file" name="gambar" class="form-control" required>

                    <div class="button-group">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Produk -->
        <div id="editModal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h4>Edit Produk</h4>
                <form id="editForm" method="POST"  enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <label>Nama Produk:</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>

                    <label>Harga Produk:</label>
                    <input type="number" name="harga" id="editHarga" class="form-control" required>

                    <label>Satuan:</label>
                    <select name="satuan" id="editSatuan" class="form-control" required>
                        <option value="PCS">PCS</option>
                        <option value="DUS">DUS</option>
                    </select>

                    <label>Gambar Produk (opsional):</label>
                    <input type="file" name="gambar" class="form-control">

                    <div class="button-group">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Produk dalam Tabel -->
        <div class="mt-4">
            <table class="table table-bordered" id="produkTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $index => $produk)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $produk->NAMAB }}</td>
                            <td>Rp{{ number_format($produk->HJUALB, 0, ',', '.') }}</td>
                            <td>{{ $produk->SATUANB }}</td>
                            <td>
                                <div class="gambar-container">
                                    <span class="gambar-nama">{{ basename($produk->gambar) }}</span>
                                    <a href="{{ $produk->gambar }}" target="_blank"
                                        class="btn btn-sm btn-info">Lihat</a>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning"
                                    onclick="openEditModal('{{ $produk->KODEB }}', '{{ $produk->NAMAB }}', '{{ $produk->HJUALB }}', '{{ $produk->SATUANB }}')">Edit</a>
                                <form action="{{ route('admin.katalog.delete', $produk->KODEB) }}" method="POST"
                                    class="d-inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CSS -->
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }

        .button-group {
            margin-top: 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .gambar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            max-width: 1000px;
        }

        .gambar-nama {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
    </style>

    <!-- Script -->
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#produkTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data tersedia",
                }
            });
        });

        function openModal() {
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function openEditModal(id, nama, harga, satuan) {
            document.getElementById('editNama').value = nama;
            document.getElementById('editHarga').value = harga;
            document.getElementById('editSatuan').value = satuan;
            document.getElementById('editForm').action = `/admin/katalog/${id}`;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
            });
        @endif

        document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus produk ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
