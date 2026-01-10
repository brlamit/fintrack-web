<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - FinTrack</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-inter min-h-screen flex items-center justify-center relative overflow-x-hidden">

<!-- Background decorations -->
<div class="absolute inset-0 pointer-events-none">
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
                <p class="text-xs text-gray-400">Reset your password</p>
            </div>
        </div>
    </header>

    <!-- Card -->
    <main class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-2xl shadow-lg p-6 sm:p-7">
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-1">Reset password</h2>
            <p class="text-xs text-gray-500">Enter your email and we'll send you a 4‑digit code to verify it's really you.</p>
        </div>

        <!-- Success Alert -->
        @if(session('status'))
        <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800">
            {{ session('status') }}
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('auth.send-reset-link') }}" class="space-y-4 text-sm">
            @csrf
            <div>
                <label for="email" class="block mb-1 text-xs font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-500 ring-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-full bg-gradient-to-r from-teal-500 to-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all duration-200">
                Send verification code
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-100 pt-5">
            <a href="{{ route('auth.login') }}" class="text-xs text-teal-600 hover:text-teal-700 transition font-medium flex items-center justify-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Back to sign in
            </a>
        </div>
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
        <p class="text-sm font-medium text-gray-700">Sending link...</p>
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
