<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard' }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Top Navigation Header -->
    <header class="md:hidden bg-slate-900 text-white flex items-center justify-between px-4 py-3 shadow-md z-30">
        <div class="flex items-center space-x-2">
            <span class="text-indigo-400 font-black text-xl tracking-wider">SHOP</span>
            <span class="text-xs bg-indigo-900 text-indigo-200 uppercase font-bold px-1.5 py-0.5 rounded">Admin</span>
        </div>
        <button type="button" 
                onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" 
                class="text-gray-300 hover:text-white p-1 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </header>

    <!-- Left Sidebar -->
    <aside id="admin-sidebar" 
           class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out md:static md:inset-auto flex flex-col justify-between shrink-0 shadow-lg">
        <div>
            <!-- Branding -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950/50">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-black text-indigo-400 tracking-wider">SHOP</span>
                    <span class="text-xs font-semibold px-2 py-0.5 bg-indigo-950 text-indigo-300 rounded border border-indigo-800 uppercase">
                        Admin
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5 text-sm font-medium">
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- Categories --}}
                @if(Route::has('admin.categories.index'))
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span>Categories</span>
                    </a>
                @endif

                {{-- Products --}}
                @if(Route::has('admin.products.index'))
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Products</span>
                    </a>
                @endif

                {{-- Orders --}}
                @if(Route::has('admin.orders.index'))
                    @php
                        $pendingCount = \App\Models\Order::whereNotIn('status', ['delivered', 'cancelled'])->count();
                    @endphp
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 rounded-lg transition duration-150 {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Orders</span>
                        </div>
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ request()->routeIs('admin.orders.*') ? 'bg-white text-indigo-700' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40 space-y-3">
            <a href="{{ route('shop.index') }}" 
               target="_blank" 
               class="flex items-center justify-center w-full px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                View Storefront
            </a>

            <div class="flex items-center justify-between pt-2">
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            title="Sign Out" 
                            class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded transition duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Utility Bar (Right Header) -->
        <header class="hidden md:flex h-16 bg-white border-b border-gray-200 items-center justify-between px-8 z-10">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-semibold text-gray-700">Admin Control Center</span>
            </div>

            <!-- Top Right Nav Links -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="text-xs font-semibold px-3 py-1.5 rounded-md transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                    Dashboard
                </a>

                @if(Route::has('admin.orders.index'))
                    <a href="{{ route('admin.orders.index') }}" 
                       class="text-xs font-semibold px-3 py-1.5 rounded-md transition {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                        Orders
                    </a>
                @endif

                @if(Route::has('admin.products.index'))
                    <a href="{{ route('admin.products.index') }}" 
                       class="text-xs font-semibold px-3 py-1.5 rounded-md transition {{ request()->routeIs('admin.products.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                        Products
                    </a>
                @endif

                @if(Route::has('admin.categories.index'))
                    <a href="{{ route('admin.categories.index') }}" 
                       class="text-xs font-semibold px-3 py-1.5 rounded-md transition {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                        Categories
                    </a>
                @endif

                <span class="h-4 w-px bg-gray-200"></span>

                <!-- Quick Return to Storefront -->
                <a href="{{ route('shop.index') }}" 
                   target="_blank"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition flex items-center">
                    <span>Storefront</span> &rarr;
                </a>
            </div>
        </header>

        <!-- Flash Messages & Toast Banners -->
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="p-4 mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-lg font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-lg font-bold">&times;</button>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 text-lg font-bold">&times;</button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            @if(isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $header }}</h1>
                </div>
            @endif

            {{-- Render the slot passed from <x-admin-layout> --}}
            @if(isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    <!-- Scripts Stack -->
    @stack('scripts')
</body>
</html>