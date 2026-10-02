@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-amber-100 text-amber-900 text-xs font-bold mb-1.5">
                ⚙️ সম্পূর্ণ ডাইনামিক কনফিগারেশন
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                প্রার্থী, মার্কা, কাউন্টডাউন ও বায়োগ্রাফি সেটিংস
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                এখান থেকে যা কিছু পরিবর্তন করবেন, তা ওয়েবসাইটের সকল সেকশন ও পোস্টার জেনারেটরে সরাসরি আপডেট হবে।
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition self-start sm:self-auto">
            ← ড্যাশবোর্ডে ফিরে যান
        </a>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. CANDIDATE PHOTO & MARKA (SYMBOL) IMAGES SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Box A: Candidate Portrait Photo -->
            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="text-xl">📷</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">
                        ১. প্রার্থীর প্রতিকৃতি / পোর্ট্রেট ছবি
                    </h3>
                </div>

                <div class="flex items-center gap-5">
                    <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl overflow-hidden bg-slate-100 border-4 border-amber-400 shadow-md shrink-0 relative">
                        <img 
                            id="portrait_preview_img" 
                            src="{{ $settings->portrait_path ?? '/assets/candidate_portrait.jpg' }}" 
                            alt="Candidate" 
                            class="w-full h-full object-cover object-top"
                            onerror="this.src='/assets/candidate_portrait.jpg'"
                        >
                    </div>
                    <div class="flex-1 space-y-3 min-w-0">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                ডিভাইস থেকে ছবি আপলোড
                            </label>
                            <input 
                                type="file" 
                                name="portrait_file" 
                                accept="image/jpeg,image/png,image/webp,image/jpg" 
                                onchange="previewPortraitFile(this)"
                                class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-800 file:text-white hover:file:bg-brand-900 border border-slate-300 rounded-xl bg-slate-50 p-1 cursor-pointer"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                অথবা ছবির সরাসরি URL
                            </label>
                            <input 
                                type="text" 
                                name="portrait_url" 
                                value="{{ (isset($settings->portrait_path) && str_starts_with($settings->portrait_path, 'http')) ? $settings->portrait_path : '' }}" 
                                placeholder="https://example.com/photo.jpg" 
                                oninput="previewPortraitUrl(this.value)"
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box B: Candidate Marka (Symbol) Image & Switch -->
            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🍍</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">
                            ২. নির্বাচনী প্রতীক (মার্কা) ও ছবি
                        </h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ ($settings->show_symbol ?? true) ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ ($settings->show_symbol ?? true) ? 'মার্কা সক্রিয়' : 'মার্কা বন্ধ' }}
                    </span>
                </div>

                <div class="flex items-center gap-5">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-amber-50 border-3 border-amber-400 shadow-md shrink-0 flex items-center justify-center p-2 text-3xl">
                        <img 
                            id="symbol_preview_img" 
                            src="{{ $settings->symbol_image_path ?? '' }}" 
                            alt="Symbol" 
                            class="w-full h-full object-contain {{ empty($settings->symbol_image_path) ? 'hidden' : '' }}"
                            onerror="this.style.display='none'; document.getElementById('symbol_fallback_emoji').style.display='block';"
                        >
                        <span id="symbol_fallback_emoji" class="{{ !empty($settings->symbol_image_path) ? 'hidden' : '' }}">🍍</span>
                    </div>

                    <div class="flex-1 space-y-3 min-w-0">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                মার্কার ছবি আপলোড (PNG/JPG/SVG)
                            </label>
                            <input 
                                type="file" 
                                name="symbol_file" 
                                accept="image/png,image/jpeg,image/webp,image/svg+xml" 
                                onchange="previewSymbolFile(this)"
                                class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 border border-slate-300 rounded-xl bg-slate-50 p-1 cursor-pointer"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                অথবা মার্কার ছবির সরাসরি URL
                            </label>
                            <input 
                                type="text" 
                                name="symbol_url" 
                                value="{{ (isset($settings->symbol_image_path) && str_starts_with($settings->symbol_image_path, 'http')) ? $settings->symbol_image_path : '' }}" 
                                placeholder="https://example.com/symbol.png" 
                                oninput="previewSymbolUrl(this.value)"
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">প্রতীক / মার্কার নাম</label>
                        <input type="text" name="symbol_name" value="{{ $settings->symbol_name ?? 'আনারস মার্কা' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="sm:pt-5">
                        <label class="flex items-center gap-2.5 cursor-pointer p-2 rounded-xl bg-amber-50/60 border border-amber-200">
                            <input type="checkbox" name="show_symbol" value="1" {{ ($settings->show_symbol ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="text-xs sm:text-sm font-bold text-brand-950">
                                সাইটে ও পোস্টারে মার্কা প্রদর্শন করুন (ON/OFF)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2. COUNTDOWN & SUPPORT COUNTER SECTION -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="text-xl">⏱️</span>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                    ৩. ভোট গ্রহণের কাউন্টডাউন ও জনতার সমর্থন কাউন্টার
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Election Date & Time Picker -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-brand-900 uppercase tracking-wider">
                        ভোট গ্রহণের তারিখ ও সময় (কাউন্টডাউন লক্ষ্য) ⭐
                    </label>
                    @php
                        $electionDateFormatted = '';
                        if (!empty($settings->election_date)) {
                            try {
                                $electionDateFormatted = \Carbon\Carbon::parse($settings->election_date)->format('Y-m-d\TH:i');
                            } catch (\Exception $e) {
                                $electionDateFormatted = '2026-11-25T08:00';
                            }
                        }
                    @endphp
                    <input 
                        type="datetime-local" 
                        name="election_date" 
                        value="{{ $electionDateFormatted }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    >
                    <p class="text-[11px] text-slate-500">
                        হোমপেজের লাইভ কাউন্টডাউন টাইমার স্বয়ংক্রিয়ভাবে এই তারিখ ও সময় গণনা করবে।
                    </p>
                </div>

                <!-- Digital Support Count -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-brand-900 uppercase tracking-wider">
                        দোয়া ও সমর্থন প্রকাশকারী নাগরিক সংখ্যা ⭐
                    </label>
                    <input 
                        type="number" 
                        name="support_pledge_count" 
                        value="{{ $settings->support_pledge_count ?? 12485 }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    >
                    <p class="text-[11px] text-slate-500">
                        "এখন পর্যন্ত ১২,৪৮৬ জন চরশাহী ইউনিয়নবাসী দোয়া ও সমর্থন প্রকাশ করেছেন" - এই সংখ্যাটি এখানে সেট ও আপডেট করতে পারবেন।
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. DYNAMIC 4 STATS CARDS (জনকল্যাণে নিবেদিত, উন্নয়ন ও সামাজিক উদ্যোগ, ইত্যাদি) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📊</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">
                        ৪. উন্নয়ন ও সাফল্য পরিসংখ্যান কার্ডসমূহ (৪টি কার্ড)
                    </h3>
                </div>

                <!-- Section Toggle Switch -->
                <label class="flex items-center gap-2 cursor-pointer bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                    <input type="checkbox" name="show_stats" value="1" {{ ($statsSection->is_visible ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="text-xs font-bold text-emerald-900">পরিসংখ্যান সেকশন প্রদর্শন (ON/OFF)</span>
                </label>
            </div>

            <p class="text-xs text-slate-500">
                হোমপেজ হিরো সেকশনের নিচে প্রদর্শিত উন্নয়ন অর্জন কার্ডের শিরোনাম, সংখ্যা ও একক নির্ধারণ করুন:
            </p>

            @php
                $statsItems = $settings->stats ?: [
                    ['label' => 'জনকল্যাণে নিবেদিত', 'value' => '১৫+', 'unit' => 'বছর'],
                    ['label' => 'উন্নয়ন ও সামাজিক উদ্যোগ', 'value' => '৮৫+', 'unit' => 'টি'],
                    ['label' => 'গ্রামীণ সড়ক ও কালভার্ট সংস্কার', 'value' => '৪৫+', 'unit' => 'কিমি'],
                    ['label' => 'বৃত্তি ও দরিদ্র সহায়তা প্রাপ্ত', 'value' => '৩,৫০০+', 'unit' => 'জন'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($statsItems as $idx => $st)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                    <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">কার্ড ০{{ $idx + 1 }}</div>
                    
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">লেবেল / টাইটেল</label>
                        <input type="text" name="stat_labels[]" value="{{ $st['label'] ?? '' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">মান / সংখ্যা</label>
                            <input type="text" name="stat_values[]" value="{{ $st['value'] ?? '' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-amber-600 focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">একক (Unit)</label>
                            <input type="text" name="stat_units[]" value="{{ $st['unit'] ?? '' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 4. DYNAMIC CANDIDATE BIO & ACHIEVEMENTS (২টি পার্ট) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">👤</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">
                        ৫. প্রার্থীর পরিচিতি ও অতীত উন্নয়ন কর্মকাণ্ড (২টি পার্ট)
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    পার্ট ১: প্রার্থীর জীবন পরিচিতি, পারিবারিক ঐতিহ্য ও দর্শন। পার্ট ২: অতীত উন্নয়ন ও সমাজসেবা মূলক রেকর্ড।
                </p>
            </div>

            @php
                $bioData = $settings->bio_data ?: [];
                $eduData = $bioData['education'] ?? [
                    ['year' => '২০০০', 'degree' => 'স্নাতকোত্তর (এম.এ - সমাজবিজ্ঞান)', 'institute' => 'চট্টগ্রাম বিশ্ববিদ্যালয়'],
                    ['year' => '১৯৯৮', 'degree' => 'স্নাতক (বি.এ অনার্স)', 'institute' => 'চট্টগ্রাম বিশ্ববিদ্যালয়'],
                    ['year' => '১৯৯৫', 'degree' => 'উচ্চ মাধ্যমিক (এইচএসসি)', 'institute' => 'লক্ষ্মীপুর সরকারি কলেজ'],
                ];
                $pastAchList = $bioData['pastAchievements'] ?? [];
                $pastAchText = implode("\n", $pastAchList);
            @endphp

            <!-- PART 1: BIOGRAPHY & HERITAGE -->
            <div class="p-5 rounded-2xl bg-emerald-50/40 border border-emerald-200 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-700 text-white text-xs font-bold flex items-center justify-center font-outfit">১</span>
                    <h4 class="text-sm sm:text-base font-extrabold text-emerald-950">
                        পার্ট ১: প্রার্থীর জীবন পরিচিতি ও বংশমর্যাদা
                    </h4>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        জীবনবৃত্তান্তের সারসংক্ষেপ (Bio Summary Quote)
                    </label>
                    <textarea rows="3" name="bio_summary" class="w-full p-3 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">{{ $bioData['summary'] ?? '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            পারিবারিক ঐতিহ্য (Family Heritage)
                        </label>
                        <textarea rows="3" name="family_heritage" class="w-full p-3 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">{{ $bioData['familyHeritage'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            সামাজিক দর্শন ও অঙ্গীকার (Social Philosophy)
                        </label>
                        <textarea rows="3" name="social_philosophy" class="w-full p-3 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">{{ $bioData['socialPhilosophy'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Educational Qualifications -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        শিক্ষাগত যোগ্যতা (Educational Qualifications)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($eduData as $eIdx => $edu)
                        <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-2">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">ডিগ্রি</span>
                                <input type="text" name="edu_degrees[]" value="{{ $edu['degree'] ?? '' }}" class="w-full px-2 py-1 rounded border border-slate-200 text-xs font-bold focus:outline-none">
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">প্রতিষ্ঠান</span>
                                <input type="text" name="edu_institutes[]" value="{{ $edu['institute'] ?? '' }}" class="w-full px-2 py-1 rounded border border-slate-200 text-xs focus:outline-none">
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">পাসের সন</span>
                                <input type="text" name="edu_years[]" value="{{ $edu['year'] ?? '' }}" class="w-full px-2 py-1 rounded border border-slate-200 text-xs focus:outline-none">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- PART 2: PAST ACHIEVEMENTS -->
            <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white text-xs font-bold flex items-center justify-center font-outfit">২</span>
                    <h4 class="text-sm sm:text-base font-extrabold text-amber-950">
                        পার্ট ২: অতীত সমাজসেবা ও উন্নয়ন চিত্র (বুলেট পয়েন্ট তালিকা)
                    </h4>
                </div>
                <p class="text-xs text-slate-600">
                    প্রতিটি লাইনে এক একটি সমাজসেবা বা উন্নয়ন কর্মকাণ্ড লিখুন। ফ্রন্টএন্ডে প্রতিটি পয়েন্ট আকর্ষণীয় চেকমার্ক আইকন সহ প্রদর্শিত হবে:
                </p>
                <textarea rows="6" name="past_achievements_text" class="w-full p-3.5 rounded-xl border border-amber-300 bg-white text-xs sm:text-sm font-medium leading-relaxed focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ $pastAchText }}</textarea>
            </div>
        </div>

        <!-- 5. GENERAL INFORMATION & CONTACTS -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                    ৬. সাধারণ প্রার্থীর তথ্য, স্লোগান ও যোগাযোগ
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="p-3 bg-amber-50/50 rounded-2xl border border-amber-200">
                    <label class="block text-xs font-bold text-brand-900 uppercase tracking-wider mb-1">
                        প্রার্থীর সংক্ষিপ্ত নাম (হেডারে প্রদর্শিত) ⭐
                    </label>
                    <input type="text" name="candidate_short_name" value="{{ $settings->candidate_short_name ?? 'রফিকুল ইসলাম চৌধুরী' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white text-sm font-bold text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">প্রার্থীর পুরো নাম</label>
                    <input type="text" name="candidate_name" value="{{ $settings->candidate_name ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">পদবী ও ভূমিকা</label>
                    <input type="text" name="candidate_role" value="{{ $settings->candidate_role ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ইউনিয়ন পরিষদের নাম</label>
                    <input type="text" name="union_name" value="{{ $settings->union_name ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">উপজেলা</label>
                    <input type="text" name="upazila" value="{{ $settings->upazila ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">জেলা</label>
                    <input type="text" name="district" value="{{ $settings->district ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">মূল নির্বাচনী স্লোগান</label>
                <input type="text" name="slogan" value="{{ $settings->slogan ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">উপ-স্লোগান / ভোটারদের প্রতি বার্তা</label>
                <textarea rows="2" name="sub_slogan" class="w-full p-3.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">{{ $settings->sub_slogan ?? '' }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">প্রধান হটলাইন মোবাইল</label>
                    <input type="text" name="phone_primary" value="{{ $settings->phone_primary ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">WhatsApp নম্বর</label>
                    <input type="text" name="whatsapp" value="{{ $settings->whatsapp ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ইমেইল</label>
                    <input type="email" name="email" value="{{ $settings->email ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">প্রধান নির্বাচনী ক্যাম্প / কার্যালয়ের ঠিকানা</label>
                <input type="text" name="office_address" value="{{ $settings->office_address ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
            </div>
        </div>

        <!-- Sticky Submit Button -->
        <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-4 rounded-2xl border border-slate-200 shadow-xl flex items-center justify-between">
            <span class="text-xs text-slate-500 hidden sm:inline">
                সকল পরিবর্তন ফ্রন্টএন্ডে রিয়েল-টাইমে আপডেট হবে।
            </span>
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-800 to-brand-900 hover:from-emerald-700 hover:to-brand-800 text-white font-extrabold text-sm shadow-lg transition transform hover:-translate-y-0.5">
                ✓ সকল সেটিংস সংরক্ষণ ও আপডেট করুন
            </button>
        </div>

    </form>

</div>

<script>
    function previewPortraitFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('portrait_preview_img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewPortraitUrl(url) {
        if (url && url.length > 5) {
            document.getElementById('portrait_preview_img').src = url;
        }
    }

    function previewSymbolFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('symbol_preview_img');
                img.src = e.target.result;
                img.classList.remove('hidden');
                document.getElementById('symbol_fallback_emoji').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewSymbolUrl(url) {
        if (url && url.length > 5) {
            const img = document.getElementById('symbol_preview_img');
            img.src = url;
            img.classList.remove('hidden');
            document.getElementById('symbol_fallback_emoji').classList.add('hidden');
        }
    }
</script>
@endsection
