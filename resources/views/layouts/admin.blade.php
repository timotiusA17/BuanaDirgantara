<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <!-- Bootstrap (gunakan yang kamu pakai) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    @include('components.navbar-admin') {{-- Navbar khusus admin --}}

    <div class="container mt-4">
        @yield('content')
        @yield('scripts')
    </div>

    <!-- Optional: JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
