<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="font-bold text-xl text-gray-800">E-Commerce Store</div>
        <div class="flex items-center space-x-4">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-9 h-9 rounded-full border">
            @endif
            <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>

            <!-- Fortify Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600 mb-6">You are successfully logged in via {{ ucfirst(Auth::user()->provider ?? 'Email') }}.</p>

        <div class="border-t pt-4">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Account Information</h2>
            <ul class="text-sm text-gray-600 space-y-2">
                <li><span class="font-semibold text-gray-800">Email:</span> {{ Auth::user()->email }}</li>
                <li><span class="font-semibold text-gray-800">Provider:</span> {{ Auth::user()->provider ?? 'Standard Registration' }}</li>
                <li><span class="font-semibold text-gray-800">Member Since:</span> {{ Auth::user()->created_at->format('M d, Y') }}</li>
            </ul>
        </div>
    </main>
</body>
</html>