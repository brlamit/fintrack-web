<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - FinTrack</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen relative overflow-hidden">

<!-- Decorative Background -->
<div class="absolute inset-0 pointer-events-none">
    <div class="absolute -top-24 -left-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl"></div>
</div>

<div class="relative z-10 w-full max-w-md mx-auto px-4">
    <!-- Header -->
    <header class="mb-8 flex flex-col items-center text-center">
        <div class="w-14 h-14 bg-gray-900 rounded-2xl shadow-xl flex items-center justify-center mb-4 transform -rotate-3 hover:rotate-0 transition-transform duration-300">
            <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">FinTrack <span class="text-teal-600">Admin</span></h1>
        <p class="text-sm text-gray-500 mt-2">Secure gateway for administrators</p>
    </header>

    <!-- Card -->
    <main class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl p-8 sm:p-10">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-widest">Administrator Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@fintrack.com"
                        class="w-full rounded-2xl border border-gray-100 bg-gray-50 pl-11 pr-4 py-3.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all shadow-inner">
                </div>
            </div>

            <div>
                <label for="password" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-widest">Secure Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full rounded-2xl border border-gray-100 bg-gray-50 pl-11 pr-4 py-3.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all shadow-inner">
                </div>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-gray-900 hover:bg-black px-4 py-4 text-sm font-bold text-white shadow-xl transition-all transform hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-2">
                <span>Authorize & Enter</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            </button>
        </form>
    </main>

    <!-- Footer -->
    <footer class="mt-10 text-center">
        <a href="/" class="group text-sm text-gray-400 hover:text-teal-600 transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            Exit to Public Website
        </a>
    </footer>
</div>

<!-- Page Loader -->
<div id="page-loader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-md hidden">
    <div class="bg-white p-8 rounded-[2rem] shadow-2xl flex flex-col items-center gap-6">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-teal-500/20 rounded-full"></div>
            <div class="w-16 h-16 border-4 border-teal-500 border-t-transparent rounded-full animate-spin absolute inset-0"></div>
        </div>
        <div class="text-center">
            <p class="text-lg font-bold text-gray-900 uppercase tracking-widest">Verifying</p>
            <p class="text-xs text-gray-500 mt-1">Establishing secure session...</p>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('page-loader').classList.remove('hidden');
    });
</script>

</body>
</html>