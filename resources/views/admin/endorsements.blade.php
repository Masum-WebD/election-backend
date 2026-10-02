@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-1.5">
                💬 জনসমর্থন ও প্রশংসাপত্র
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
                জনগণের সমর্থন ও অভিমত বাণী ({{ $endorsements->count() }} টি)
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                ইউনিয়নের বিশিষ্ট ব্যক্তিবর্গ ও সাধারণ ভোটারদের লিখিত সমর্থন বাণী ও অভিমত পরিচালনা করুন।
            </p>
        </div>
    </div>

    <!-- Endorsements Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($endorsements as $item)
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/90 text-left flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">
                            {{ $item->name }}
                        </h3>
                        <p class="text-xs text-slate-500">
                            {{ $item->title }} • {{ $item->village }}
                        </p>
                    </div>

                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->is_approved ? 'অনুমোদিত (Live)' : 'লুকানো (Hidden)' }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 text-sm text-slate-700 italic leading-relaxed">
                    "{{ $item->quote }}"
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <form action="{{ route('admin.endorsements.toggle', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition {{ $item->is_approved ? 'bg-amber-100 hover:bg-amber-200 text-amber-900' : 'bg-emerald-700 hover:bg-emerald-800 text-white' }}">
                        {{ $item->is_approved ? 'সাইট থেকে লুকান' : 'সাইটে অনুমোদন দিন' }}
                    </button>
                </form>

                <form action="{{ route('admin.endorsements.delete', $item->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই অভিমতটি মুছে ফেলতে চান?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-bold p-1">
                        মুছে ফেলুন ✕
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-dashed border-slate-300 text-slate-400">
            এখনও কোনো সমর্থন বার্তা জমা পড়েনি।
        </div>
        @endforelse
    </div>

</div>
@endsection
