<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
    @yield('scripts')

</head>
<body class="bg-gray-100">
    @include('components.navbar')
    <div class="container mx-auto p-4">
        @yield('content')
    </div>
</body>
</html>
