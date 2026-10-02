@extends('layouts.admin')

@section('content')
<div class="space-y-8 sm:space-y-10">

    <!-- 0. TOP OVERVIEW / STATS CARDS -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                    নির্বাচনী প্রচার ড্যাশবোর্ড ও মাস্টার সুইচ
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $settings->union_name ?? 'ইউনিয়ন পরিষদ' }} • প্রার্থী: <strong class="text-brand-800">{{ $settings->candidate_short_name ?? $settings->candidate_name }}</strong>
                </p>
            </div>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.settings') }}" class="px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-brand-950 text-xs font-bold transition shadow-xs">
                    ⚙️ প্রার্থীর তথ্য ও মার্কা
                </a>
                <a href="{{ route('admin.manifestos') }}" class="px-3.5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
                    📜 ইশতেহার ({{ $stats['total_manifestos'] ?? 0 }})
                </a>
                <a href="{{ route('admin.grievances') }}" class="px-3.5 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-xs font-bold transition">
                    📩 অভিযোগ ({{ $stats['total_grievances'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- 4 Stat Counters Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:border-emerald-300 transition">
                <div class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">হোমপেজে দৃশ্যমান সেকশন</div>
                <div class="text-2xl sm:text-3xl font-black text-brand-800 font-outfit mt-1">
                    {{ $stats['active_sections'] ?? 0 }} <span class="text-sm font-semibold text-slate-400">/ {{ $stats['total_sections'] ?? 0 }}</span>
                </div>
                <div class="text-xs text-emerald-600 mt-1 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>সক্রিয় সেকশন</span>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:border-emerald-300 transition">
                <div class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">জনতার জমা হওয়া অভিযোগ</div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 font-outfit mt-1">
                    {{ $stats['total_grievances'] ?? 0 }}
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    অমীমাংসিত: <strong class="text-amber-700">{{ $stats['pending_grievances'] ?? 0 }}</strong> টি
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:border-emerald-300 transition">
                <div class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">অনলাইন দোয়া ও সমর্থক</div>
                <div class="text-2xl sm:text-3xl font-black text-red-600 font-outfit mt-1">
                    {{ number_format($stats['support_pledges'] ?? 12485) }}
                </div>
                <div class="text-xs text-emerald-600 mt-1 font-semibold">ডিজিটাল সমর্থন প্রাপ্ত</div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-slate-200/80 hover:border-emerald-300 transition">
                <div class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">নির্বাচনী প্রতীক (মার্কা)</div>
                <div class="text-xl sm:text-2xl font-black {{ ($settings->show_symbol ?? true) ? 'text-emerald-700' : 'text-slate-400' }} mt-1 truncate">
                    {{ ($settings->show_symbol ?? true) ? ($settings->symbol_name ?? 'আনারস') : 'লুকানো / অফ' }}
                </div>
                <div class="text-xs {{ ($settings->show_symbol ?? true) ? 'text-emerald-600 font-bold' : 'text-red-600 font-semibold' }} mt-1">
                    {{ ($settings->show_symbol ?? true) ? '✓ সাইটে প্রদর্শিত হচ্ছে' : '✕ সাইটে বন্ধ রাখা হয়েছে' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 1. HOMEPAGE SECTION VISIBILITY CONTROLLER & MARKA SWITCH -->
    <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-xs border border-slate-200/90">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-slate-100">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-amber-100 text-amber-900 text-xs font-bold mb-1.5">
                    ⚡ লাইভ সেকশন কন্ট্রোল প্যানেল
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-brand-900">
                    প্রতিটি সেকশন ও মার্কা অন / অফ অপশন (Show / Hide)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    এখানে যে কোনো সেকশন বা প্রার্থীর মার্কা সুইচ অফ করলে তা সাথে সাথে ফ্রন্টএন্ড থেকে উধাও হয়ে যাবে।
                </p>
            </div>
            <div class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 self-start sm:self-auto">
                রিয়েল-টাইম সিঙ্ক সক্রিয়
            </div>
        </div>

        <!-- SPECIAL PROMINENT MARKA (SYMBOL) MASTER SWITCH -->
        @php
            $symbolSection = $sections->firstWhere('section_key', 'symbol');
            $isSymbolOn = $symbolSection ? $symbolSection->is_visible : ($settings->show_symbol ?? true);
        @endphp
        <div class="mb-6 p-5 sm:p-6 rounded-2xl border-2 {{ $isSymbolOn ? 'bg-gradient-to-r from-amber-50 to-emerald-50/50 border-amber-400/80 shadow-sm' : 'bg-slate-50 border-slate-300 opacity-90' }} transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white border-2 {{ $isSymbolOn ? 'border-amber-400 text-amber-500' : 'border-slate-300 text-slate-400' }} shadow-md flex items-center justify-center text-2xl shrink-0 overflow-hidden p-1">
                        @if(!empty($settings->symbol_image_path))
                            <img src="{{ $settings->symbol_image_path }}" alt="Symbol" class="w-full h-full object-contain">
                        @else
                            🍍
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-extrabold text-brand-950">
                                নির্বাচনী প্রতীক / মার্কা প্রদর্শন ({{ $settings->symbol_name ?? 'আনারস মার্কা' }})
                            </h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $isSymbolOn ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                                {{ $isSymbolOn ? 'মার্কা দৃশ্যমান (ON)' : 'মার্কা লুকানো (OFF)' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                            নির্বাচন কমিশন থেকে মার্কা বরাদ্দ না হওয়া পর্যন্ত বা বিশেষ প্রয়োজনে এটি বন্ধ রাখলে হোমপেজ হেডার, হিরো ব্যানার, বায়োগ্রাফি, ফুটার এবং <strong>পোস্টার জেনারেটরের সকল ডিজাইন</strong> থেকে মার্কার ছবি ও সিল স্বয়ংক্রিয়ভাবে লুকিয়ে থাকবে।
                        </p>
                    </div>
                </div>

                @if($symbolSection)
                <form action="{{ route('admin.sections.toggle', $symbolSection->id) }}" method="POST" class="shrink-0 self-end sm:self-center">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-md transition-all transform hover:-translate-y-0.5 {{ $isSymbolOn ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-emerald-700 hover:bg-emerald-800 text-white' }}">
                        <span>{{ $isSymbolOn ? 'মার্কা বন্ধ করুন (Turn OFF)' : 'মার্কা চালু করুন (Turn ON)' }}</span>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- SPECIAL PROMINENT COUNTDOWN MASTER SWITCH -->
        @php
            $countdownSection = $sections->firstWhere('section_key', 'countdown');
            $isCountdownOn = $countdownSection ? $countdownSection->is_visible : ($settings->show_countdown ?? true);
        @endphp
        <div class="mb-6 p-5 sm:p-6 rounded-2xl border-2 {{ $isCountdownOn ? 'bg-gradient-to-r from-blue-50 to-emerald-50/50 border-blue-400/80 shadow-sm' : 'bg-slate-50 border-slate-300 opacity-90' }} transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white border-2 {{ $isCountdownOn ? 'border-blue-400 text-blue-600' : 'border-slate-300 text-slate-400' }} shadow-md flex items-center justify-center text-2xl shrink-0">
                        ⏱️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-extrabold text-brand-950">
                                ভোট গ্রহণের লাইভ কাউন্টডাউন টাইমার (Countdown Box)
                            </h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $isCountdownOn ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                                {{ $isCountdownOn ? 'কাউন্টডাউন দৃশ্যমান (ON)' : 'কাউন্টডাউন লুকানো (OFF)' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                            হোমপেজের হিরো ব্যানারে নির্বাচনের বাকি দিন, ঘণ্টা, মিনিট ও সেকেন্ড গণনার ঘড়িটি প্রদর্শন বা বন্ধ রাখার জন্য এই সুইচটি ব্যবহার করুন।
                        </p>
                    </div>
                </div>

                @if($countdownSection)
                <form action="{{ route('admin.sections.toggle', $countdownSection->id) }}" method="POST" class="shrink-0 self-end sm:self-center">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-md transition-all transform hover:-translate-y-0.5 {{ $isCountdownOn ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-emerald-700 hover:bg-emerald-800 text-white' }}">
                        <span>{{ $isCountdownOn ? 'কাউন্টডাউন বন্ধ করুন (Turn OFF)' : 'কাউন্টডাউন চালু করুন (Turn ON)' }}</span>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- SECTIONS TOGGLE GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($sections as $sec)
                @if($sec->section_key !== 'symbol' && $sec->section_key !== 'countdown')
                <div class="p-4 rounded-2xl border transition-all duration-200 {{ $sec->is_visible ? 'bg-slate-50/70 border-slate-200 hover:border-emerald-300' : 'bg-red-50/30 border-red-200/60 opacity-75' }} flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $sec->is_visible ? 'bg-emerald-500' : 'bg-red-400' }}"></span>
                            <h4 class="text-sm font-bold text-slate-900 truncate">
                                {{ $sec->title_bn }}
                            </h4>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-mono truncate">
                            {{ $sec->section_key }}
                        </p>
                    </div>

                    <!-- Toggle Form Button -->
                    <form action="{{ route('admin.sections.toggle', $sec->id) }}" method="POST" class="shrink-0">
                        @csrf
                        <button 
                            type="submit" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 {{ $sec->is_visible ? 'bg-emerald-600' : 'bg-slate-300' }}" 
                            role="switch" 
                            aria-checked="{{ $sec->is_visible ? 'true' : 'false' }}"
                            title="{{ $sec->is_visible ? 'লুকানোর জন্য ক্লিক করুন' : 'প্রদর্শনের জন্য ক্লিক করুন' }}"
                        >
                            <span class="sr-only">Toggle section</span>
                            <span 
                                aria-hidden="true" 
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $sec->is_visible ? 'translate-x-5' : 'translate-x-0' }}"
                            ></span>
                        </button>
                    </form>
                </div>
                @endif
            @endforeach
        </div>

    </div>

    <!-- Quick Navigation Hub -->
    <div class="bg-gradient-to-r from-emerald-900 to-brand-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center md:text-left">
            <h3 class="text-xl sm:text-2xl font-bold text-amber-300">
                প্রার্থীর ছবি, মার্কা ও বিস্তারিত ডাটা ম্যানেজমেন্ট
            </h3>
            <p class="text-xs sm:text-sm text-emerald-100/90 max-w-xl">
                প্রার্থীর পোর্ট্রেট ও মার্কার ইমেজ আপলোড, লাইভ কাউন্টডাউন টাইমার, ৪টি স্ট্যাট কার্ড, ২-পার্ট বায়োগ্রাফি, ইশতেহার, ফটো অ্যালবাম ও ভিডিও ভাষণ পরিচালনা করতে সংশ্লিষ্ট মেন্যুতে যান।
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('admin.settings') }}" class="px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-brand-950 font-extrabold text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5">
                প্রার্থী ও মার্কা সেটিংস ⚙️
            </a>
            <a href="{{ route('admin.manifestos') }}" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 backdrop-blur-sm transition">
                ইশতেহার ম্যানেজমেন্ট 📜
            </a>
        </div>
    </div>

</div>
@endsection
