<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Shop' }} - {{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-indigo-600">
                    SHOPPING
                </a>
                <nav class="hidden md:flex space-x-6 text-sm font-medium text-gray-700">
                    <a href="{{ url('/') }}" class="hover:text-indigo-600">Home</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-indigo-600">All Products</a>
                </nav>
            </div>

            <!-- Search Bar -->
            <form action="{{ route('shop.index') }}" method="GET" class="hidden sm:block flex-1 max-w-xs mx-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="w-full text-xs px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500">
            </form>

            <!-- User / Cart links -->
            <div class="flex items-center space-x-4">
                {{-- 1. Admin Link (Visible only to authenticated Admins) --}}
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded-md shadow-sm transition">
                            Admin
                        </a>
                    @endif

                    {{-- 2. Logout Button (Placed in between Admin and Cart) --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline-flex items-center">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center space-x-1 text-sm font-medium text-gray-600 hover:text-red-600 transition"
                                title="Sign Out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                @else
                    {{-- Guest Links (If not logged in) --}}
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                        Register
                    </a>
                @endauth

                <!-- Cart Counter Anchor (Ready for Cart Step) -->
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center p-2 text-gray-700 hover:text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    
                    @php
                        $count = app(\App\Services\CartService::class)->count();
                    @endphp

                    <!-- Real-time Cart Badge (Always in DOM so JavaScript can update it) -->
                    <span id="cart-badge" 
                          class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-indigo-600 rounded-full transition-transform duration-200 {{ $count > 0 ? '' : 'hidden' }}">
                        {{ $count }}
                    </span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16 py-8 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</body>
</html>