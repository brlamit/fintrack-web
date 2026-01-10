<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Code - FinTrack</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative min-h-screen bg-gray-50 font-inter flex items-center justify-center">

<!-- Background decorations -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-32 -right-20 w-80 h-80 bg-gradient-to-br from-teal-500/5 to-sky-500/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-gradient-to-br from-emerald-500/5 to-sky-500/5 rounded-full blur-3xl"></div>
</div>

<div class="relative z-10 w-full max-w-md mx-auto px-4 py-10">
    <!-- Header -->
    <header class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="FT" class="w-10 h-10 rounded-2xl shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=FT&background=14b8a6&color=fff'">
            </a>
            <div>
                <h1 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-400 via-sky-400 to-emerald-400">FinTrack</h1>
                <p class="text-xs text-gray-500">Verify your email</p>
            </div>
        </div>
    </header>

    <!-- Verify Code Card -->
    <main class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-2xl p-8 shadow-lg text-center">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold mb-1">Enter Verification Code</h2>
            <p class="text-sm text-gray-600">We sent a 4‑digit code to <span class="font-medium text-gray-900">{{ $email }}</span>. Enter it below to continue.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-xl border border-teal-500/40 bg-teal-500/10 px-3 py-2 text-xs text-teal-800 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.otp.verify') }}" id="otp-form" class="flex flex-col items-center gap-4">
            @csrf
            <div class="flex justify-center gap-3">
                <input type="text" inputmode="numeric" maxlength="1" name="otp_1" autofocus class="otp-input w-12 h-12 sm:w-14 sm:h-14 rounded-xl border border-gray-300 bg-white text-center text-lg sm:text-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <input type="text" inputmode="numeric" maxlength="1" name="otp_2" class="otp-input w-12 h-12 sm:w-14 sm:h-14 rounded-xl border border-gray-300 bg-white text-center text-lg sm:text-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <input type="text" inputmode="numeric" maxlength="1" name="otp_3" class="otp-input w-12 h-12 sm:w-14 sm:h-14 rounded-xl border border-gray-300 bg-white text-center text-lg sm:text-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <input type="text" inputmode="numeric" maxlength="1" name="otp_4" class="otp-input w-12 h-12 sm:w-14 sm:h-14 rounded-xl border border-gray-300 bg-white text-center text-lg sm:text-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>

            @error('otp')
                <p class="text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full rounded-full bg-gradient-to-r from-teal-500 to-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all duration-200">
                Verify Code
            </button>
        </form>

        <form method="POST" action="{{ route('auth.otp.resend') }}" class="mt-4 text-[11px] text-gray-600">
            @csrf
            <button type="submit" class="text-teal-600 hover:text-teal-700 font-medium font-inter">Resend Code</button>
        </form>
    </main>

    <footer class="mt-8 text-[10px] text-gray-500 text-center">
        &copy; {{ date('Y') }} FinTrack. Secure, private and simple.
    </footer>
</div>

<!-- Auth Loader Overlay -->
<div id="auth-loader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden">
    <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4">
        <div class="w-10 h-10 border-4 border-teal-500/20 border-t-teal-500 rounded-full animate-spin"></div>
        <p id="loader-text" class="text-sm font-medium text-gray-700">Verifying...</p>
    </div>
</div>

<script>
// OTP input auto-focus
const inputs = document.querySelectorAll('.otp-input');
inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        const value = input.value.replace(/\D/g, '');
        input.value = value;
        if (value && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });

    // Handle paste
    input.addEventListener('paste', (event) => {
        const paste = (event.clipboardData || window.clipboardData).getData('text');
        if (paste.length === inputs.length) {
            const digits = paste.split('');
            inputs.forEach((input, i) => {
                input.value = digits[i] || '';
            });
            inputs[inputs.length - 1].focus();
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const loader = document.getElementById('auth-loader');
    const loaderText = document.getElementById('loader-text');
    
    // Main verify form
    const otpForm = document.getElementById('otp-form');
    if (otpForm) {
        otpForm.addEventListener('submit', function() {
            loaderText.textContent = 'Verifying...';
            loader.classList.remove('hidden');
        });
    }

    // Resend form
    const resendForm = document.querySelector('form[action*="resend"]');
    if (resendForm) {
        resendForm.addEventListener('submit', function() {
            loaderText.textContent = 'Resending...';
            loader.classList.remove('hidden');
        });
    }
});
</script>
</body>
</html>
