<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Store') }}</title>

    <!-- Tailwind CSS CDN (or your compiled Vite assets) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Store Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="container mx-auto px-4 max-w-7xl h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-indigo-600 hover:text-indigo-700">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <nav class="hidden md:flex space-x-6 text-sm font-medium text-gray-700">
                    <a href="{{ route('shop.index') }}" class="hover:text-indigo-600 transition">Shop</a>
                </nav>
            </div>

            <div class="flex items-center space-x-5">
                <!-- Cart Link with Real-time Count Badge -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-indigo-600 transition" title="Shopping Cart">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @php
                        $initialCount = app(\App\Services\CartService::class)->count();
                    @endphp
                    <span id="cart-badge"
                          class="absolute -top-1 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transition-all duration-300 transform scale-100 {{ $initialCount > 0 ? '' : 'hidden' }}">
                        {{ $initialCount }}
                    </span>
                    </a>

                <!-- User Auth Links -->
                @auth
                    <div class="flex items-center space-x-3 text-sm">
                        @if (auth()->user()->isAdmin())
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700">Admin</span>
                        @endif
                        <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-red-600 hover:underline">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center space-x-3 text-sm">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Log in</a>
                        <a href="{{ route('register') }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Injection -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel Store') }}. All rights reserved.
    </footer>

</body>
</html>