@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold mb-1.5">
                🎬 ভিডিও ভাষণ ও প্রেস ব্রিফিং
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                ইউটিউব ভিডিও বক্তব্য ব্যবস্থাপনা
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                জনসভার সরাসরি ভিডিও বক্তব্য, মিডিয়া সাক্ষাৎকার ও প্রেস ব্রিফিংয়ের ইউটিউব লিঙ্ক এবং বিবরণ যুক্ত করুন।
            </p>
        </div>

        <a href="#add-video-card" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-xs self-start sm:self-auto flex items-center gap-1.5">
            <span>+ নতুন ভিডিও যুক্ত করুন</span>
        </a>
    </div>

    <!-- Video List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($videos as $vid)
        <div class="bg-white rounded-3xl overflow-hidden shadow-xs border border-slate-200/90 text-left flex flex-col">
            <!-- YouTube Video Thumbnail / Iframe Preview -->
            <div class="relative aspect-video bg-black overflow-hidden group">
                <iframe 
                    class="w-full h-full"
                    src="https://www.youtube.com/embed/{{ $vid->youtube_id }}" 
                    title="{{ $vid->title }}" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                ></iframe>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                        <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">{{ $vid->speaker }}</span>
                        <span>{{ $vid->duration }} • {{ $vid->views_text }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 text-base leading-snug">
                        {{ $vid->title }}
                    </h3>

                    @if(!empty($vid->summary))
                    <p class="text-xs text-slate-600 mt-2 line-clamp-3 leading-relaxed">
                        {{ $vid->summary }}
                    </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="font-mono text-slate-400">ID: {{ $vid->youtube_id }}</span>
                    <form action="{{ route('admin.videos.delete', $vid->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই ভিডিওটি মুছে ফেলতে চান?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-bold">
                            মুছে ফেলুন ✕
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-10 bg-white rounded-3xl border border-dashed border-slate-300 text-slate-400">
            এখনও কোনো ভিডিও ভাষণ যুক্ত করা হয়নি। নিচে নতুন ভিডিও যোগ করুন।
        </div>
        @endforelse
    </div>

    <!-- Add New Video Card -->
    <div id="add-video-card" class="bg-gradient-to-r from-red-50/70 to-slate-50 rounded-3xl p-6 sm:p-8 shadow-xs border-2 border-red-200 text-left space-y-5">
        <div class="flex items-center gap-2 border-b border-red-200 pb-3">
            <span class="text-xl">➕</span>
            <h3 class="text-lg font-bold text-slate-900">
                নতুন ইউটিউব ভিডিও লিঙ্ক ও বিবরণ যোগ করুন
            </h3>
        </div>

        <form action="{{ route('admin.videos.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ভিডিও শিরোনাম</label>
                    <input type="text" name="title" placeholder="উদা: ৭নং ওয়ার্ডে পথসভায় প্রার্থীর নির্বাচনী বক্তব্য" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm font-bold focus:ring-2 focus:ring-red-500 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        ইউটিউব ভিডিও লিঙ্ক অথবা ভিডিও আইডি ⭐
                    </label>
                    <input type="text" name="youtube_input" placeholder="উদা: https://www.youtube.com/watch?v=dQw4w9WgXcQ অথবা dQw4w9WgXcQ" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm font-bold focus:ring-2 focus:ring-red-500 focus:outline-none" required>
                    <span class="text-[11px] text-slate-400 block mt-0.5">যেকোনো সাধারণ YouTube URL বা Shorts লিঙ্ক পেস্ট করতে পারেন।</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">বক্তা</label>
                    <input type="text" name="speaker" value="{{ $settings->candidate_short_name ?? $settings->candidate_name ?? 'আলহাজ্ব মো: রফিকুল ইসলাম চৌধুরী' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ভিডিওর দৈর্ঘ্য / সময়কাল</label>
                    <input type="text" name="duration" placeholder="উদা: ১২:৪৫ মিনিট" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ভিউজের বিবরণ</label>
                    <input type="text" name="views_text" placeholder="উদা: ১০,০০০+ ভিউজ" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">বক্তব্যের মূল সারসংক্ষেপ ও বার্তা</label>
                <textarea rows="3" name="summary" placeholder="উদা: চরশাহীর প্রতিটি নাগরিকের সাংবিধানিক অধিকার রক্ষা ও টেকসই উন্নয়নে দলমত নির্বিশেষে ঐক্যবদ্ধ হওয়ার আহ্বান..." class="w-full p-3 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
            </div>

            <button type="submit" class="px-7 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5">
                + ভিডিও প্রকাশ করুন
            </button>
        </form>
    </div>

</div>
@endsection
