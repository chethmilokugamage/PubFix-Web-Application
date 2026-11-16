<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PubFix - Online Handyman Hiring System' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <span class="bg-green-700 text-white font-extrabold text-xl px-2.5 py-1 rounded-lg tracking-wider">PubFix</span>
                    <span class="text-xs text-gray-500 font-medium hidden sm:inline">Handyman System</span>
                </a>
            </div>
            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('reviews.index') }}" class="text-green-700 font-semibold hover:text-green-800 transition">Reviews & Ratings</a>
                @if(Route::has('bookings.index'))
                    <a href="{{ route('bookings.index') }}" class="text-gray-600 hover:text-gray-900 transition">Bookings</a>
                @endif
                <a href="{{ route('reviews.create') }}" class="bg-green-700 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 rounded-lg hover:bg-green-800 transition shadow-sm">
                    + Write Review
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow py-6">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} PubFix - Online Handyman Hiring System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
