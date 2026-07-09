<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied — Courier DB</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center px-4">

    <div class="max-w-md w-full text-center">
        <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-slate-900 mb-2">Access Denied</h1>
        <p class="text-slate-500 mb-8">
            You do not have permission to view this page.<br>
            Please log in with an account that has the required role.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ \App\Support\RoleRedirect::path(auth()->user()->role) }}"
                   class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Go to My Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Log In
                </a>
            @endauth

            <a href="{{ url('/') }}"
               class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                Go to Home
            </a>
        </div>
    </div>

</body>
</html>
