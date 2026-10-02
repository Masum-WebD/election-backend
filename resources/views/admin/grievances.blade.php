@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-amber-100 text-amber-900 text-xs font-bold mb-1.5">
                📩 জনমত ও প্রতিকার
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                জনতার অভিযোগ ও উন্নয়ন পরামর্শ বক্স ({{ $grievances->count() }} টি)
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                ওয়েবসাইটে ইউনিয়নবাসীর দাখিল করা নাগরিক সমস্যা, রাস্তাঘাট, ড্রেনেজ ও কল্যাণ ভাতা সংস্কার আবেদনসমূহ।
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200">
                অমীমাংসিত: {{ $stats['pending_grievances'] ?? 0 }}
            </span>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                সম্পন্ন: {{ $stats['resolved_grievances'] ?? 0 }}
            </span>
        </div>
    </div>

    <!-- Grievances List -->
    <div class="space-y-4">
        @forelse($grievances as $item)
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200/90 text-left flex flex-col md:flex-row md:items-center justify-between gap-5 hover:border-emerald-300 transition">
            <div class="flex-1 space-y-2 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="font-extrabold text-slate-900 text-base">{{ $item->name }}</span>
                    <span class="font-mono text-xs px-2 py-0.5 bg-slate-200 rounded-md text-slate-700 font-bold">{{ $item->tracking_id }}</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $item->status == 'সম্পন্ন' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        {{ $item->status }}
                    </span>
                    <span class="text-xs text-slate-400">📅 {{ $item->created_at->format('d M, Y') }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    <span class="font-bold text-slate-700">{{ $item->ward }}</span>
                    <span>•</span>
                    <span>গ্রাম: {{ $item->village }}</span>
                    <span>•</span>
                    <span>মোবাইল: <a href="tel:{{ $item->phone }}" class="text-emerald-700 font-bold hover:underline">{{ $item->phone }}</a></span>
                    <span>•</span>
                    <span class="text-brand-800 font-bold bg-emerald-100/60 px-2 py-0.5 rounded">বিষয়: {{ $item->category }}</span>
                </div>

                <div class="text-sm text-slate-800 mt-2 bg-slate-50 p-4 rounded-2xl border border-slate-200/70 leading-relaxed">
                    "{{ $item->message }}"
                </div>

                @if(!empty($item->admin_notes))
                <div class="text-xs text-emerald-800 bg-emerald-50 p-2.5 rounded-xl border border-emerald-200">
                    <span class="font-bold">অ্যাডমিন নোট:</span> {{ $item->admin_notes }}
                </div>
                @endif
            </div>

            <!-- Action Controls Form -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0 self-start md:self-center">
                <form action="{{ route('admin.grievances.status', $item->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <select name="status" class="text-xs py-2 px-3 rounded-xl border border-slate-300 font-bold bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        <option value="পর্যালোচনায় গৃহীত" {{ $item->status == 'পর্যালোচনায় গৃহীত' ? 'selected' : '' }}>পর্যালোচনায় গৃহীত</option>
                        <option value="অগ্রাধিকার তালিকায় অন্তর্ভুক্ত" {{ $item->status == 'অগ্রাধিকার তালিকায় অন্তর্ভুক্ত' ? 'selected' : '' }}>অগ্রাধিকার তালিকায় অন্তর্ভুক্ত</option>
                        <option value="সম্পন্ন" {{ $item->status == 'সম্পন্ন' ? 'selected' : '' }}>সম্পন্ন</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">
                        আপডেট
                    </button>
                </form>

                <form action="{{ route('admin.grievances.delete', $item->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই আবেদনটি মুছে ফেলতে চান?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl text-xs font-bold border border-red-200 transition">
                        মুছে ফেলুন
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-slate-300 text-slate-400">
            এখনও কোনো অভিযোগ জমা পড়েনি।
        </div>
        @endforelse
    </div>

</div>
@endsection
