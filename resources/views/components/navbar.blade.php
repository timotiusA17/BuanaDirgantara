<nav class="bg-black p-4 text-white relative z-50">
    <div class="container mx-auto flex items-center justify-between">
        <!-- Left: Logo/Brand -->
        <div class="flex items-center flex-shrink-0">
            <a href="{{ route('home') }}" class="text-lg font-bold">Buana Dirgantara</a>
        </div>

        <!-- Center: Menu (hidden on mobile) -->
        <div class="hidden md:flex md:items-center md:justify-center flex-1 mx-4">
            <ul class="flex space-x-6">
                <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                <li><a href="{{ route('personal.achievement') }}" class="hover:underline">Personal Achievement</a></li>
                <li><a href="{{ route('leaderboard') }}" class="hover:underline">Leaderboard</a></li>
                <li><a href="{{ route('promo') }}" class="hover:underline">Promo</a></li>
                <li><a href="{{ route('pelanggan.profile') }}" class="hover:underline">Profile</a></li>
            </ul>
        </div>

        <!-- Right: Logout (hidden on mobile) -->
        <div class="hidden md:block flex-shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="hover:underline">Logout</button>
            </form>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-button" class="text-white focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu (hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-black pb-4 px-4">
        <div class="flex flex-col space-y-3">
            <a href="{{ route('home') }}" class="block hover:underline py-2">Home</a>
            <a href="{{ route('personal.achievement') }}" class="block hover:underline py-2">Personal Achievement</a>
            <a href="{{ route('leaderboard') }}" class="block hover:underline py-2">Leaderboard</a>
            <a href="{{ route('promo') }}" class="block hover:underline py-2">Promo</a>
            <a href="{{ route('pelanggan.profile') }}" class="block hover:underline py-2">Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="pt-2 border-t border-gray-700">
                @csrf
                <button type="submit" class="hover:underline w-full text-left">Logout</button>
            </form>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        
        // Optional: Toggle aria-expanded attribute for accessibility
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
    });
</script>