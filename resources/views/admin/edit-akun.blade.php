@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Akun</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.akun.update', $user->id) }}">
        @csrf

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Password (Kosongkan jika tidak ingin mengganti)</label>
            <input type="password" name="password" class="form-control">
        </div>

        @if($user->role === 'pelanggan')
            <div class="mb-3">
                <label>Nama Toko</label>
                <input type="text" name="nama_toko" class="form-control" value="{{ old('nama_toko', $pelanggan->nama_toko) }}">
            </div>

            <div class="mb-3">
                <label>Total Pembelian</label>
                <input type="number" name="total_pembelian" class="form-control" value="{{ old('total_pembelian', $pelanggan->total_pembelian) }}">
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.akun') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
