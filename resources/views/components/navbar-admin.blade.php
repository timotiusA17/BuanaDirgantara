<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="#">Dashboard Admin</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarAdmin">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.katalog') }}">Katalog</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.pembelian') }}">Pembelian</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.leaderboard') }}">Leaderboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.promo') }}">Promo</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.akun') }}">Akun</a></li>
            </ul>
        </div>

        <div class="d-flex ms-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </div>
</nav>
