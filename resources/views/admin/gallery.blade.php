@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-1.5">
                📷 ফটো অ্যালবাম
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                প্রচার অ্যালবাম ও ফটো গ্যালারি ব্যবস্থাপনা
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                ক্যাটাগরি অনুযায়ী জনসভা, উঠান বৈঠক, গণসংযোগ ও সমাজসেবার ছবি এবং বিবরণ যোগ করুন।
            </p>
        </div>

        <a href="#add-photo-card" class="px-4 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-xs font-bold transition shadow-xs self-start sm:self-auto flex items-center gap-1.5">
            <span>+ নতুন ছবি যুক্ত করুন</span>
        </a>
    </div>

    <!-- Photos Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($gallery as $item)
        <div class="bg-white rounded-3xl overflow-hidden shadow-xs border border-slate-200/90 text-left flex flex-col">
            <div class="relative aspect-[16/10] bg-slate-900 overflow-hidden">
                <img 
                    src="{{ $item->image_url }}" 
                    alt="{{ $item->title }}" 
                    class="w-full h-full object-cover"
                    onerror="this.src='/assets/candidate_portrait.jpg'"
                >
                <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold">
                    {{ $item->category }}
                </span>
            </div>

            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500 mb-1">
                        <span>📍 {{ $item->location }}</span>
                        <span>•</span>
                        <span>📅 {{ $item->date_text }}</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">
                        {{ $item->title }}
                    </h3>
                    @if(!empty($item->description))
                    <p class="text-xs text-slate-600 mt-1 line-clamp-2">
                        {{ $item->description }}
                    </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">ID: #{{ $item->id }}</span>
                    <form action="{{ route('admin.gallery.delete', $item->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই ছবিটি মুছে ফেলতে চান?');">
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
            গ্যালারিতে এখনও কোনো ছবি নেই। নিচে নতুন ছবি যুক্ত করুন।
        </div>
        @endforelse
    </div>

    <!-- Add New Photo Card -->
    <div id="add-photo-card" class="bg-gradient-to-r from-emerald-50/70 to-slate-50 rounded-3xl p-6 sm:p-8 shadow-xs border-2 border-emerald-300 text-left space-y-5">
        <div class="flex items-center gap-2 border-b border-emerald-200 pb-3">
            <span class="text-xl">➕</span>
            <h3 class="text-lg font-bold text-emerald-950">
                প্রচার অ্যালবামে নতুন ছবি যুক্ত করুন
            </h3>
        </div>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ছবির শিরোনাম</label>
                    <input type="text" name="title" placeholder="উদা: চরশাহী বাজার গণসংযোগ ও পথসভা" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ক্যাটাগরি</label>
                    <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                        <option value="গণসংযোগ">গণসংযোগ</option>
                        <option value="উঠান বৈঠক">উঠান বৈঠক</option>
                        <option value="ত্রাণ ও সমাজসেবা">ত্রাণ ও সমাজসেবা</option>
                        <option value="যুব সমাবেশ">যুব সমাবেশ</option>
                        <option value="মতবিনিময় সভা">মতবিনিময় সভা</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">স্থান / অবস্থান</label>
                    <input type="text" name="location" placeholder="উদা: চরশাহী মধ্যপাড়া ও বাজার এলাকা" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">তারিখ বা সময় টেক্সট</label>
                    <input type="text" name="date_text" placeholder="উদা: অক্টোবর ২০২৬" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ডিভাইস থেকে ছবি আপলোড</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-800 file:text-white border border-slate-300 rounded-xl bg-white p-1 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">অথবা ছবির অনলাইন URL</label>
                    <input type="text" name="image_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">সংক্ষিপ্ত বিবরণ</label>
                <textarea rows="2" name="description" placeholder="উদা: ৭নং ওয়ার্ডে সর্বস্তরের জনতার ভালোবাসায় সিক্ত হলেন প্রার্থী..." class="w-full p-3 rounded-xl border border-slate-300 bg-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none"></textarea>
            </div>

            <button type="submit" class="px-7 py-3 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5">
                + ছবিতে যুক্ত করুন
            </button>
        </form>
    </div>

</div>
@endsection
