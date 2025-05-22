@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Leaderboard Page</h2>
    <p>Ini adalah halaman leaderboard admin.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.reward.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="reward_image">Upload Gambar Reward untuk Top 1 Leaderboard:</label>
            <input type="file" name="reward_image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Gambar Reward</button>
    </form>
</div>
@endsection
