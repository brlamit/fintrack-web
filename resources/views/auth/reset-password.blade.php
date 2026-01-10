<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - FinTrack</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter', sans-serif; }
  .glass { @apply bg-white/80 backdrop-blur-md border border-gray-200 rounded-2xl p-6 shadow-lg; }
</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10 relative">

<!-- Background shapes -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">
  <div class="absolute top-[-120px] right-[-80px] w-[360px] h-[360px] bg-gradient-to-br from-teal-500/5 to-sky-500/5 blur-3xl rounded-full"></div>
  <div class="absolute bottom-[-120px] left-[-60px] w-[320px] h-[320px] bg-gradient-to-br from-emerald-500/5 to-sky-500/5 blur-3xl rounded-full"></div>
</div>

<div class="relative z-10 w-full max-w-md px-4 sm:px-6">
  <!-- Header -->
  <div class="flex items-center justify-between mb-8">
    <a href="/" class="flex items-center gap-2">
      <img src="{{ asset('images/logo.png') }}" alt="FT" class="h-9 w-9 rounded-2xl shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=FT&background=14b8a6&color=fff'">
      <div class="leading-tight">
        <div class="text-sm font-semibold tracking-tight bg-gradient-to-r from-emerald-500 via-cyan-500 to-sky-600 bg-clip-text text-transparent">FinTrack</div>
        <div class="text-[11px] text-gray-500">Smart money, calm mind</div>
      </div>
    </a>
  </div>

  <!-- Card -->
  <div class="glass rounded-2xl px-5 py-6 sm:px-7 sm:py-7">
    <div class="mb-6 text-center">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Reset your password</h1>
      <p class="mt-1.5 text-xs text-gray-500 max-w-sm mx-auto">Choose a strong, unique password to keep your FinTrack account secure.</p>
    </div>

    <!-- Alert -->
    @if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-100 p-3">
        @foreach($errors->all() as $error)
            <p class="text-[11px] text-red-600">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('auth.reset-password.post') }}" class="space-y-4">
      @csrf
      <div class="space-y-1.5">
        <label for="email" class="block text-xs font-medium text-gray-700">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" 
            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-gray-500 outline-none cursor-not-allowed" readonly required>
      </div>

      <div class="space-y-1.5">
        <label for="password" class="block text-xs font-medium text-gray-700">New password</label>
        <input type="password" id="password" name="password" required
            class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
      </div>

      <div class="space-y-1.5">
        <label for="password_confirmation" class="block text-xs font-medium text-gray-700">Confirm new password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
            class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
      </div>

      <button type="submit" class="w-full mt-2 rounded-full bg-gradient-to-r from-teal-500 to-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all duration-200">
        Reset password
      </button>
    </form>

    <div class="mt-6 text-center border-t border-gray-100 pt-5">
        <a href="{{ route('auth.login') }}" class="text-xs text-teal-600 hover:text-teal-700 transition font-medium flex items-center justify-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to sign in
        </a>
    </div>
  </div>

  <!-- Footer -->
  <footer class="mt-8 text-center text-[10px] text-gray-500">
    &copy; {{ date('Y') }} FinTrack. Secure, private and simple.
  </footer>
</div>

<!-- Auth Loader Overlay -->
<div id="auth-loader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden">
    <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4">
        <div class="w-10 h-10 border-4 border-teal-500/20 border-t-teal-500 rounded-full animate-spin"></div>
        <p class="text-sm font-medium text-gray-700">Updating password...</p>
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
