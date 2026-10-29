<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Customer Login</h2>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Standard Fortify Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                       class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm">
                    <span class="ml-2 text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-200">
                Log In
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-gray-300"></span></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-500">Or continue with</span></div>
        </div>

        <!-- Socialite 4-Provider Grid -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Google
            </a>
            <a href="{{ route('social.redirect', 'github') }}" class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                GitHub
            </a>
            <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-blue-800 hover:bg-blue-50 transition">
                Facebook
            </a>
            <a href="{{ route('social.redirect', 'linkedin') }}" class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-sky-800 hover:bg-sky-50 transition">
                LinkedIn
            </a>
        </div>

        <!-- Manual Registration Link -->
        <p class="mt-6 text-center text-sm text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-semibold">Create one here</a>
        </p>
    </div>
</body>
</html>