<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন | {{ $settings->candidate_short_name ?? $settings->candidate_name ?? 'নির্বাচনী ক্যাম্পেইন' }}</title>
    
    <!-- Google Fonts: Hind Siliguri + Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#064e3b',
                            900: '#043327',
                            950: '#022119'
                        }
                    },
                    fontFamily: {
                        bengali: ['"Hind Siliguri"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-gradient-to-br from-brand-950 via-[#03231a] to-slate-950 text-slate-800 antialiased selection:bg-amber-400 selection:text-brand-950">

    <div class="w-full max-w-md my-8">
        
        <!-- Top Electoral Branding Card -->
        <div class="text-center mb-6 space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-tr from-amber-400 to-amber-300 text-brand-950 shadow-xl ring-4 ring-amber-400/20 text-3xl mb-1 transform hover:scale-105 transition-transform">
                🗳️
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                নির্বাচনী অ্যাডমিন প্যানেল
            </h1>
            <p class="text-xs sm:text-sm text-emerald-300 font-semibold">
                {{ $settings->union_name ?? '৭নং চরশাহী ইউনিয়ন পরিষদ' }} • সাধারণ নির্বাচন {{ $settings->election_year ?? '২০২৬' }}
            </p>
        </div>

        <!-- Main Login Box -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-emerald-900/30 space-y-6">

            <!-- Flash Error Message -->
            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs font-bold space-y-1">
                    @foreach($errors->all() as $err)
                        <div class="flex items-center gap-1.5">
                            <span>⚠️</span>
                            <span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-1.5">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- PROMINENT DEMO CREDENTIALS CARD (ব্যবহারকারীর বিশেষ অনুরোধ) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-emerald-50/60 border-2 border-amber-400/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400 text-brand-950 text-[11px] font-black uppercase tracking-wider">
                        ⭐ ডেমো লগইন ক্রেডেনশিয়াল
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium">টেস্টিং ও ডেমোর জন্য</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-white border border-amber-200 shadow-xs">
                        <span class="text-[10px] text-slate-400 font-bold block">ইমেইল (Email):</span>
                        <code class="text-xs font-bold text-slate-900 font-mono select-all">admin@election.com</code>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white border border-amber-200 shadow-xs">
                        <span class="text-[10px] text-slate-400 font-bold block">পাসওয়ার্ড (Password):</span>
                        <code class="text-xs font-bold text-amber-700 font-mono select-all">admin123</code>
                    </div>
                </div>

                <!-- 1-Click Auto Fill Button -->
                <button 
                    type="button" 
                    onclick="fillDemoCredentials()"
                    class="w-full flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-brand-950 font-bold text-xs transition shadow-xs transform active:scale-95"
                >
                    <span>⚡ ডেমো ক্রেডেনশিয়াল দিয়ে অটো-ফিল করুন</span>
                </button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        অ্যাডমিন ইমেইল ঠিকানা
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', 'admin@election.com') }}" 
                        placeholder="admin@election.com" 
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none bg-slate-50 focus:bg-white transition"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        লগইন পাসওয়ার্ড
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        value="admin123" 
                        placeholder="••••••••" 
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none bg-slate-50 focus:bg-white transition"
                        required
                    >
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span>আমাকে মনে রাখুন</span>
                    </label>
                    <span class="text-slate-400">নিরাপদ ড্যাশবোর্ড সেশন</span>
                </div>

                <button 
                    type="submit" 
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-800 to-brand-900 hover:from-emerald-700 hover:to-brand-800 text-white font-extrabold text-sm shadow-lg transition transform hover:-translate-y-0.5 active:translate-y-0"
                >
                    <span>অ্যাডমিন ড্যাশবোর্ডে প্রবেশ করুন →</span>
                </button>
            </form>

            <!-- Back to Live Frontend Website -->
            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="http://127.0.0.1:5173" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                    <span>লাইভ প্রচার ওয়েবসাইট দেখুন</span>
                    <span>↗</span>
                </a>
            </div>

        </div>

        <p class="text-center text-xs text-emerald-400/60 mt-4">
            © {{ $settings->election_year ?? '২০২৬' }} নির্বাচনী ক্যাম্পেইন ম্যানেজমেন্ট প্ল্যাটফর্ম
        </p>

    </div>

    <!-- Script to Auto-fill Demo Credentials -->
    <script>
        function fillDemoCredentials() {
            document.getElementById('email').value = 'admin@election.com';
            document.getElementById('password').value = 'admin123';
            
            // Subtle visual feedback
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            emailInput.classList.add('ring-2', 'ring-amber-400');
            passInput.classList.add('ring-2', 'ring-amber-400');
            setTimeout(() => {
                emailInput.classList.remove('ring-2', 'ring-amber-400');
                passInput.classList.remove('ring-2', 'ring-amber-400');
            }, 1000);
        }
    </script>

</body>
</html>
