<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - FinTrack</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    darkMode: 'class'
  }
</script>
</head>
<body class="relative min-h-screen bg-gray-50">

<div class="relative z-10 w-full max-w-md mx-auto px-4 py-10">
    <!-- Header -->
    <header class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="FT" class="w-10 h-10 rounded-2xl shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=FT&background=14b8a6&color=fff'">
            </a>
            <div>
                <h1 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-400 via-sky-400 to-emerald-400">FinTrack</h1>
                <p class="text-xs text-gray-400">Create your free account</p>
            </div>
        </div>
    </header>

    <!-- Register Card -->
    <main class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-2xl p-8 shadow-lg">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-1">Join FinTrack</h2>
            <p class="text-sm text-gray-500">Set up your space to track spending, budgets, and savings goals.</p>
        </div>
        

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-teal-500/40 bg-teal-500/10 px-3 py-2 text-xs text-teal-800">
                    {{ session('status') }}
                </div>
            @endif
        <form method="POST" action="{{ route('auth.register.post') }}" class="space-y-4 text-sm">
                @csrf

                <div>
                     <label for="name" class="block mb-1 text-xs font-medium text-gray-700">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('name') border-red-500 ring-red-500 @enderror"
                        placeholder="Your full name"
                    >
                    @error('name')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                   <label for="email" class="block mb-1 text-xs font-medium text-gray-700">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-500 ring-red-500 @enderror"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block mb-1 text-xs font-medium text-gray-700">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-500 ring-red-500 @enderror"
                        placeholder="Create a strong password"
                    >
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                   <p class="mt-1 text-[11px] text-gray-400">At least 8 characters with uppercase, lowercase, and a number.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-1 text-xs font-medium text-gray-700">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Re-enter your password"
                    >
                </div>

                <div class="pt-1">
                    <button type="submit" class="w-full rounded-full bg-gradient-to-r from-teal-500 to-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all duration-200">
                        Create your account
                    </button>
                </div>
            </form>

        <div class="mt-5 flex items-center justify-between gap-3 text-xs text-gray-400">
            <span class="h-px flex-1 bg-gray-200"></span>
            <span>or</span>
            <span class="h-px flex-1 bg-gray-200"></span>
        </div>

        <div class="mt-4 text-xs text-gray-600 text-center">
            Already have an account?
            <a href="{{ route('auth.login') }}" class="text-teal-600 hover:text-teal-700 font-medium">Sign in</a>
        </div>
    </main>

    <footer class="mt-8 text-[10px] text-gray-500 text-center">
        Tip: use a personal email you check often so you never miss budget reminders.
    </footer>
</div>

<!-- Auth Loader Overlay -->
<div id="auth-loader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden">
    <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4">
        <div class="w-10 h-10 border-4 border-teal-500/20 border-t-teal-500 rounded-full animate-spin"></div>
        <p class="text-sm font-medium text-gray-700">Registering...</p>
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
