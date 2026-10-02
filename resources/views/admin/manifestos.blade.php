@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-1.5">
                📜 নির্বাচনী প্রতিশ্রুতি ও কর্মপরিকল্পনা
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                জনতার নির্বাচনী ইশতেহার টেবিল
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                সকল ইশতেহার ক্যাটাগরি ও দফা সুন্দর টেবিল আকারে পর্যবেক্ষণ, দ্রুত এডিট ও নতুন দফা সংযুক্ত করুন।
            </p>
        </div>

        <button 
            type="button" 
            onclick="openAddModal()" 
            class="px-5 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-xs sm:text-sm font-bold transition shadow-md self-start sm:self-auto flex items-center gap-2 transform hover:-translate-y-0.5"
        >
            <span>+ নতুন ইশতেহার ক্যাটাগরি</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-semibold">মোট ক্যাটাগরি</span>
            <div class="text-2xl font-black text-brand-900 font-outfit mt-1">{{ $manifestos->count() }} টি</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-semibold">সক্রিয় (Active)</span>
            <div class="text-2xl font-black text-emerald-600 font-outfit mt-1">{{ $manifestos->where('is_active', true)->count() }} টি</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-xs text-slate-500 font-semibold">মোট নির্বাচনী দফা</span>
            @php
                $totalPoints = 0;
                foreach($manifestos as $m) {
                    $pts = is_array($m->points) ? $m->points : [];
                    $totalPoints += count($pts);
                }
            @endphp
            <div class="text-2xl font-black text-amber-600 font-outfit mt-1">{{ $totalPoints }} টি দফা</div>
        </div>
    </div>

    <!-- MAIN MANIFESTOS TABLE -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                <span>📋</span>
                <span>ইশতেহার ক্যাটাগরি ও দফা তালিকা</span>
            </h3>
            <span class="text-xs text-slate-400">সর্বমোট {{ $manifestos->count() }} টি রেকর্ড</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-16">ক্রম</th>
                        <th class="py-3.5 px-4">ক্যাটাগরি ও ব্যাজ</th>
                        <th class="py-3.5 px-4">ইশতেহারের শিরোনাম ও বিবরণ</th>
                        <th class="py-3.5 px-4 text-center">দফা সংখ্যা</th>
                        <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 text-right pr-6">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($manifestos as $index => $item)
                    @php
                        $pointsList = is_array($item->points) ? $item->points : [];
                        $pointsCount = count($pointsList);
                    @endphp
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <!-- Sort Order / Index -->
                        <td class="py-4 px-4 text-center font-bold text-slate-500 font-outfit">
                            {{ $item->sort_order ?: ($index + 1) }}
                        </td>

                        <!-- Category Badge & Key -->
                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200">
                                    <span>🎯</span>
                                    <span>{{ $item->badge }}</span>
                                </span>
                                <div class="text-[11px] font-mono text-slate-400">
                                    {{ $item->category_id }}
                                </div>
                            </div>
                        </td>

                        <!-- Title & Headline -->
                        <td class="py-4 px-4 max-w-xs md:max-w-md">
                            <div class="font-bold text-slate-900 text-sm">
                                {{ $item->title }}
                            </div>
                            @if(!empty($item->headline))
                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                {{ $item->headline }}
                            </div>
                            @endif
                        </td>

                        <!-- Points Count & Quick Preview Pill -->
                        <td class="py-4 px-4 text-center">
                            <button 
                                type="button" 
                                onclick="openPreviewModal({{ $item->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs transition border border-amber-300"
                                title="দফাসমূহ দেখতে ক্লিক করুন"
                            >
                                <span>{{ $pointsCount }} টি দফা</span>
                                <span class="text-[10px]">👁</span>
                            </button>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                <span>{{ $item->is_active ? 'সক্রিয়' : 'বন্ধ' }}</span>
                            </span>
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-4 px-4 text-right pr-6">
                            <div class="inline-flex items-center gap-2">
                                <button 
                                    type="button" 
                                    onclick="openEditModal({{ $item->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition border border-slate-300 flex items-center gap-1"
                                >
                                    <span>✏️</span>
                                    <span>এডিট</span>
                                </button>

                                <form action="{{ route('admin.manifestos.delete', $item->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ইশতেহার ক্যাটাগরি মুছে ফেলতে চান?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="px-2.5 py-1.5 rounded-xl text-red-600 hover:bg-red-50 font-bold text-xs transition border border-red-200"
                                        title="মুছে ফেলুন"
                                    >
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            কোনো ইশতেহার ক্যাটাগরি পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- 1. PREVIEW POINTS MODAL TEMPLATES (Hidden in DOM, shown by JS) -->
@foreach($manifestos as $item)
@php
    $pointsList = is_array($item->points) ? $item->points : [];
@endphp
<div id="preview-modal-{{ $item->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-fade-in text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                    {{ $item->badge }}
                </span>
                <h3 class="text-lg font-bold text-slate-900 mt-1">
                    {{ $item->title }}
                </h3>
            </div>
            <button onclick="closePreviewModal({{ $item->id }})" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-500 font-bold">
                ✕
            </button>
        </div>

        <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
            @foreach($pointsList as $pIdx => $pt)
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-2.5 text-xs sm:text-sm text-slate-800">
                <span class="w-5 h-5 rounded-full bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                    {{ $pIdx + 1 }}
                </span>
                <span class="leading-relaxed font-medium">{{ $pt }}</span>
            </div>
            @endforeach
        </div>

        <div class="pt-2 text-right">
            <button onclick="closePreviewModal({{ $item->id }})" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs">
                বন্ধ করুন
            </button>
        </div>
    </div>
</div>

<!-- 2. EDIT MANIFESTO MODAL -->
<div id="edit-modal-{{ $item->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 my-8 text-left">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="text-xl">✏️</span>
                <h3 class="text-lg font-bold text-slate-900">
                    ইশতেহার ক্যাটাগরি এডিট করুন
                </h3>
            </div>
            <button onclick="closeEditModal({{ $item->id }})" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-500 font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('admin.manifestos.update', $item->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ক্যাটাগরি শিরোনাম</label>
                    <input type="text" name="title" value="{{ $item->title }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ট্যাব ব্যাজ নাম</label>
                    <input type="text" name="badge" value="{{ $item->badge }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">উপ-শিরোনাম / হেডলাইন</label>
                    <input type="text" name="headline" value="{{ $item->headline }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ক্রম নম্বর</label>
                        <input type="number" name="sort_order" value="{{ $item->sort_order }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    </div>

                    <div class="pt-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-800">
                            <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span>সক্রিয় (Active)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    ইশতেহারের দফাসমূহ (প্রতিটি লাইনে ১টি করে দফা লিখুন)
                </label>
                @php
                    $pointsText = is_array($item->points) ? implode("\n", $item->points) : $item->points;
                @endphp
                <textarea rows="5" name="points_text" class="w-full p-3.5 rounded-xl border border-slate-300 text-xs sm:text-sm leading-relaxed focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>{{ $pointsText }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal({{ $item->id }})" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    বাতিল
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition">
                    ✓ পরিবর্তন সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- 3. ADD NEW MANIFESTO MODAL -->
<div id="add-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 my-8 text-left">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="text-xl">➕</span>
                <h3 class="text-lg font-bold text-slate-900">
                    নতুন নির্বাচনী ইশতেহার ক্যাটাগরি তৈরি
                </h3>
            </div>
            <button onclick="closeAddModal()" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-500 font-bold">
                ✕
            </button>
        </div>

        <form action="{{ route('admin.manifestos.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ক্যাটাগরি শিরোনাম</label>
                    <input type="text" name="title" placeholder="উদা: আধুনিক যোগাযোগ ও ড্রেনেজ" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ট্যাব ব্যাজ নাম</label>
                    <input type="text" name="badge" placeholder="উদা: যোগাযোগ ও ড্রেনেজ" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-emerald-600 focus:outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">উপ-শিরোনাম / হেডলাইন</label>
                    <input type="text" name="headline" placeholder="উদা: চরশাহীর দীর্ঘদিনের জলাবদ্ধতা ও ড্রেনেজ নিরসন" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">আইডি স্লাগ</label>
                        <input type="text" name="category_id" placeholder="roads_drainage" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ক্রম নম্বর</label>
                        <input type="number" name="sort_order" value="{{ $manifestos->count() + 1 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    দফাসমূহ (প্রতিটি লাইনে ১টি করে দফা লিখুন)
                </label>
                <textarea rows="5" name="points_text" placeholder="১. ইউনিয়নের প্রতিটি ওয়ার্ডের কাঁচা রাস্তা পাকা করা হবে&#10;২. প্রধান খাল ও নালার খনন নিশ্চিত করা হবে&#10;৩. বাজারে সোলার স্ট্রিট লাইট স্থাপন করা হবে" class="w-full p-3.5 rounded-xl border border-slate-300 text-xs sm:text-sm leading-relaxed focus:ring-2 focus:ring-emerald-600 focus:outline-none" required></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    বাতিল
                </button>
                <button type="submit" class="px-7 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs sm:text-sm shadow-md transition">
                    + ইশতেহার সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Control Scripts -->
<script>
    function openPreviewModal(id) {
        const el = document.getElementById('preview-modal-' + id);
        if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
    }
    function closePreviewModal(id) {
        const el = document.getElementById('preview-modal-' + id);
        if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
    }
    function openEditModal(id) {
        const el = document.getElementById('edit-modal-' + id);
        if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
    }
    function closeEditModal(id) {
        const el = document.getElementById('edit-modal-' + id);
        if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
    }
    function openAddModal() {
        const el = document.getElementById('add-modal');
        if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
    }
    function closeAddModal() {
        const el = document.getElementById('add-modal');
        if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
    }
</script>
@endsection
