<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add show_symbol and candidate_short_name to campaign_settings if not exist
        Schema::table('campaign_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_settings', 'show_symbol')) {
                $table->boolean('show_symbol')->default(true)->after('symbol_tagline');
            }
            if (!Schema::hasColumn('campaign_settings', 'candidate_short_name')) {
                $table->string('candidate_short_name')->nullable()->after('candidate_name');
            }
        });

        // Set default candidate_short_name
        DB::table('campaign_settings')->whereNull('candidate_short_name')->update([
            'candidate_short_name' => 'রফিকুল ইসলাম চৌধুরী',
            'show_symbol' => true,
        ]);

        // Insert or update symbol section setting
        $exists = DB::table('section_settings')->where('section_key', 'symbol')->exists();
        if (!$exists) {
            DB::table('section_settings')->insert([
                'section_key' => 'symbol',
                'title_bn' => 'নির্বাচনী প্রতীক (মার্কা) প্রদর্শন',
                'description_bn' => 'হেডার, হিরো ব্যানার ও সাইটজুড়ে প্রার্থীর নির্বাচনী মার্কা (আনারস প্রতীক ও ব্যাজ) দেখানো বা বন্ধ রাখার অপশন।',
                'is_visible' => true,
                'sort_order' => 0, // Put it at the very top or priority
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('campaign_settings', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_settings', 'show_symbol')) {
                $table->dropColumn('show_symbol');
            }
            if (Schema::hasColumn('campaign_settings', 'candidate_short_name')) {
                $table->dropColumn('candidate_short_name');
            }
        });

        DB::table('section_settings')->where('section_key', 'symbol')->delete();
    }
};
