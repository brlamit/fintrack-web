<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set Password - FinTrack</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-inter min-h-screen flex items-center justify-center relative overflow-x-hidden">

<!-- Background shapes -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-32 -right-20 w-80 h-80 bg-gradient-to-br from-teal-500/5 to-sky-500/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-gradient-to-br from-emerald-500/5 to-sky-500/5 rounded-full blur-3xl"></div>
</div>

<div class="relative z-10 w-full max-w-md mx-auto px-4">
    <!-- Header -->
    <header class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="FT" class="w-10 h-10 rounded-2xl shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=FT&background=14b8a6&color=fff'">
            </a>
            <div>
                <h1 class="text-lg font-semibold bg-gradient-to-r from-teal-400 via-cyan-400 to-sky-400 bg-clip-text text-transparent">FinTrack</h1>
                <p class="text-xs text-gray-400">Set a new password</p>
            </div>
        </div>
    </header>

    <!-- Glass card -->
    <main class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-2xl shadow-lg p-6 sm:p-7">
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-1">Create a secure password</h2>
            <p class="text-xs text-gray-500">For your security, please choose a fresh password before continuing.</p>
        </div>

        <form method="POST" action="{{ route('auth.force-password-change.post') }}" class="space-y-4 text-sm">
            @csrf
            <!-- New password -->
            <div>
                <label for="password" class="block mb-1 text-xs font-medium text-gray-700">New Password</label>
                <input type="password" id="password" name="password" required autofocus placeholder="Create a strong password"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-500 ring-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-[11px] text-gray-400">At least 8 characters with uppercase, lowercase and a number.</p>
            </div>

            <!-- Confirm password -->
            <div>
                <label for="password_confirmation" class="block mb-1 text-xs font-medium text-gray-700">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter your new password"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>

            <button type="submit" class="w-full rounded-full bg-gradient-to-r from-teal-500 to-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all duration-200">
                Set password and continue
            </button>
        </form>
    </main>

    <!-- Footer -->
    <footer class="mt-8 text-center text-[10px] text-gray-500">
        &copy; {{ date('Y') }} FinTrack. Secure, private and simple.
    </footer>
</div>

<!-- Auth Loader Overlay -->
<div id="auth-loader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden">
    <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4">
        <div class="w-10 h-10 border-4 border-teal-500/20 border-t-teal-500 rounded-full animate-spin"></div>
        <p class="text-sm font-medium text-gray-700">Updating security...</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const loader = document.getElementById('auth-loader');
        if (form && loader) {
            form.addEventListener('submit', function() {
                loader.classList.remove('hidden');
            });
        }
    });
</script>

</body>
</html>
