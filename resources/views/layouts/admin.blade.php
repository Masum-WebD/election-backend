<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন কন্ট্রোল প্যানেল ও ড্যাশবোর্ড | {{ $settings->candidate_short_name ?? $settings->candidate_name ?? 'চেয়ারম্যান পদপ্রার্থী' }}</title>
    
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
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full font-bengali text-slate-800 antialiased bg-slate-100 flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

    <!-- Sidebar (Desktop Fixed + Mobile Off-canvas) -->
    <aside id="admin-sidebar" class="fixed lg:static top-0 bottom-0 left-0 z-50 w-72 bg-gradient-to-b from-brand-950 via-brand-900 to-[#021812] text-white flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 shadow-2xl lg:shadow-none border-r border-emerald-900/50">
        
        <!-- Sidebar Brand Header -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-emerald-800/40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-300 text-brand-950 font-black flex items-center justify-center text-xl shadow-lg ring-2 ring-amber-400/30">
                    🗳️
                </div>
                <div class="overflow-hidden">
                    <h2 class="font-extrabold text-base text-white tracking-tight leading-none truncate">
                        নির্বাচনী প্যানেল
                    </h2>
                    <p class="text-[11px] text-emerald-300/80 mt-1 font-medium truncate">
                        {{ $settings->union_name ?? 'ইউনিয়ন পরিষদ' }}
                    </p>
                </div>
            </div>

            <!-- Mobile close button -->
            <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-emerald-300 hover:text-white hover:bg-white/10" aria-label="Close sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Candidate Badge Mini Box -->
        <div class="px-5 py-3 mx-4 my-4 rounded-xl bg-emerald-900/40 border border-emerald-700/30 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-amber-400/20 border-2 border-amber-400/60 overflow-hidden flex items-center justify-center text-amber-300 text-xs font-bold shrink-0 shadow-sm">
                @if(!empty($settings->portrait_path))
                    <img src="{{ $settings->portrait_path }}" alt="Candidate" class="w-full h-full object-cover object-top" onerror="this.parentElement.innerHTML='প্রার্থী'">
                @else
                    প্রার্থী
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold text-white truncate">
                    {{ $settings->candidate_short_name ?? $settings->candidate_name ?? 'রফিকুল ইসলাম চৌধুরী' }}
                </div>
                <div class="text-[10px] text-amber-300/90 font-medium truncate">
                    {{ $settings->candidate_role ?? 'চেয়ারম্যান পদপ্রার্থী' }}
                </div>
            </div>
        </div>

        <!-- Navigation Menu Links -->
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto py-2">
            <a href="{{ route('admin.dashboard') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">⚡</span>
                <span class="flex-1">ড্যাশবোর্ড ও সেকশন সুইচ</span>
            </a>

            <a href="{{ route('admin.settings') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">⚙️</span>
                <span class="flex-1">প্রার্থী, মার্কা ও সেটিংস</span>
            </a>

            <a href="{{ route('admin.manifestos') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.manifestos*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">📜</span>
                <span class="flex-1">নির্বাচনী ইশতেহার</span>
            </a>

            <a href="{{ route('admin.gallery') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.gallery*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">📷</span>
                <span class="flex-1">প্রচার অ্যালবাম ও ফটো</span>
            </a>

            <a href="{{ route('admin.videos') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.videos*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">🎬</span>
                <span class="flex-1">ভিডিও ভাষণ ও বক্তব্য</span>
            </a>

            <a href="{{ route('admin.grievances') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.grievances*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">📩</span>
                <span class="flex-1">জনতার অভিযোগ ও দাবি</span>
            </a>

            <a href="{{ route('admin.endorsements') }}" onclick="closeSidebarOnMobile()" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.endorsements*') ? 'bg-amber-400 text-brand-950 font-bold shadow-md' : 'text-emerald-100 hover:text-white hover:bg-white/10' }} transition-colors group">
                <span class="text-lg">💬</span>
                <span class="flex-1">সমর্থন ও অভিমত বাণী</span>
            </a>
        </nav>

        <!-- Sidebar Footer Action (Live Website Link & Logout) -->
        <div class="p-4 border-t border-emerald-800/40 bg-brand-950/60 space-y-2">
            <a href="http://127.0.0.1:5173" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-brand-950 font-bold text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5">
                <span>লাইভ ফ্রন্টেন্ড দেখুন</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>

            @auth
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl text-xs font-bold text-red-300 hover:text-white hover:bg-red-950/60 border border-red-800/40 transition">
                    <span>⎋ লগআউট ({{ auth()->user()->name ?? 'অ্যাডমিন' }})</span>
                </button>
            </form>
            @endauth

            <div class="text-center text-[10px] text-emerald-400/60 pt-0.5">
                Laravel {{ app()->version() }} • MySQL • React 19
            </div>
        </div>

    </aside>

    <!-- Main App Container -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-x-hidden">
        
        <!-- Top App Bar -->
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <!-- Hamburger Menu for Mobile / Tablet -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-brand-900 hover:bg-slate-100 focus:outline-none" aria-label="Open sidebar menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight">
                        নির্বাচনী প্রচারণা অ্যাডমিন ড্যাশবোর্ড
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ auth()->user()->name ?? 'অ্যাডমিন' }}</span>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs border border-red-200 transition" title="লগআউট">
                        লগআউট ⎋
                    </button>
                </form>
                @endauth

                <!-- Live Frontend Quick Button -->
                <a href="http://127.0.0.1:5173" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                    <span class="hidden sm:inline">লাইভ সাইট</span>
                    <span class="sm:hidden">সাইট</span>
                    <span>↗</span>
                </a>
            </div>
        </header>

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 bg-emerald-600 text-white text-xs sm:text-sm font-bold py-3 px-5 rounded-2xl shadow-md flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2">
                    <span class="text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white text-xs">✕</button>
            </div>
        @endif

        <!-- Main Body Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
            <div>
                © {{ $settings->election_year ?? '২০২৬' }} নির্বাচনী ক্যাম্পেইন সিস্টেম • স্বতন্ত্র ও ডিজিটাল প্ল্যাটফর্ম
            </div>
            <div class="text-emerald-700 font-semibold">
                {{ $settings->union_name ?? '৭নং চরশাহী ইউনিয়ন পরিষদ' }}
            </div>
        </footer>

    </div>

    <!-- Toggle Sidebar JS Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 1024) {
                toggleSidebar();
            }
        }
    </script>

</body>
</html>
