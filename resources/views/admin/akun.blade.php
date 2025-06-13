@extends('layouts.admin')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- jQuery (wajib sebelum Select2 JS dan DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Akun Pengguna</h2>
            <button onclick="toggleForm()" class="btn btn-success">+</button>
        </div>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Pesan error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Modal Form Tambah Pengguna --}}
        <div id="formOverlay" class="form-overlay" style="display: none;">
            <div class="form-popup bg-light p-4 rounded shadow">
                <form action="{{ route('admin.akun.store') }}" method="POST">
                    @csrf
                    <h4 class="mb-3">Buat Akun Baru</h4>

                    <div class="mb-3">
                        <label for="name" class="form-label">Username</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" name="password" id="password" class="form-control" value="12345678" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required
                            onchange="handleRoleChange(this.value)">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="pelanggan">Pelanggan</option>
                        </select>
                    </div>

                    <div id="pelangganFields" style="display: none;">
                        <div class="mb-3">
                            <label for="nama_toko" class="form-label">Nama Toko</label>
                            <select name="nama_toko" id="nama_toko" class="form-select select2" onchange="updateKodec(this)"
                                required>
                                <option value="">-- Pilih Toko --</option>
                                <option value="manual">-- Input Manual --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->PERSC }}" data-kodec="{{ $customer->KODEC }}">
                                        {{ $customer->PERSC }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="kodec_toko" id="kodec_toko">
                        </div>

                        <div class="mb-3" id="manualNamaTokoContainer" style="display: none;">
                            <label for="manual_nama_toko" class="form-label">Masukkan Nama Toko</label>
                            <input type="text" name="manual_nama_toko" id="manual_nama_toko" class="form-control">
                        </div>


                        <div class="mb-3">
                            <label for="total_pembelian" class="form-label">Total Pembelian Awal (Rp)</label>
                            <input type="number" name="total_pembelian" id="total_pembelian" class="form-control"
                                value="0" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Tutup</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Data Akun dengan DataTables --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="akunTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Nama Toko (Pelanggan)</th>
                                <th>Total Pembelian (Rp)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>
                                        @if ($user->role === 'pelanggan')
                                            {{ $user->pelanggan->nama_toko ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->role === 'pelanggan')
                                            {{ number_format($user->pelanggan->total_pembelian ?? 0, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->role === 'owner' || $user->id === auth()->user()->id || $user->role === 'pelanggan')
                                            <a href="{{ route('admin.akun.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                        @endif
                                        @if (
                                            $user->role !== 'owner' &&
                                                $user->id !== auth()->user()->id &&
                                                ($user->role === 'pelanggan' || ($user->role === 'admin' && auth()->user()->role === 'owner')))
                                            <form action="{{ route('admin.akun.delete', $user->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk toggle modal & dynamic role --}}
    <script>
        function toggleForm() {
            const overlay = document.getElementById('formOverlay');
            overlay.style.display = overlay.style.display === 'none' ? 'flex' : 'none';
        }

        function handleRoleChange(role) {
            const fields = document.getElementById('pelangganFields');
            if (role === 'pelanggan') {
                fields.style.display = 'block';
                document.getElementById('nama_toko').setAttribute('required', 'required');
                document.getElementById('total_pembelian').setAttribute('required', 'required');
            } else {
                fields.style.display = 'none';
                document.getElementById('nama_toko').removeAttribute('required');
                document.getElementById('total_pembelian').removeAttribute('required');
            }
        }

        function syncUsername() {
            const selected = document.getElementById('nama_toko').value;
            document.getElementById('name').value = selected;
        }

        function updateKodec(select) {
            const selectedOption = select.options[select.selectedIndex];
            const manualInput = document.getElementById('manualNamaTokoContainer');
            const manualField = document.getElementById('manual_nama_toko');

            if (select.value === 'manual') {
                manualInput.style.display = 'block';
                manualField.required = true;

                document.getElementById('kodec_toko').value = '';
                document.getElementById('name').value = '';
            } else {
                manualInput.style.display = 'none';
                manualField.required = false;
                manualField.value = '';

                document.getElementById('kodec_toko').value = selectedOption.getAttribute('data-kodec');
                document.getElementById('name').value = selectedOption.value;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            $('.select2').select2({
                placeholder: "Cari nama toko...",
                allowClear: true,
                width: '100%'
            });

            $('#akunTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [{
                    orderable: false,
                    targets: [5]
                }]
            });
        });
    </script>



    <style>
        .form-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-popup {
            width: 100%;
            max-width: 500px;
            background-color: white;
        }

        /* Style untuk DataTables */
        .dataTables_filter input {
            margin-bottom: 10px;
        }
    </style>
@endsection
