<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add show_countdown column to campaign_settings if not exists
        if (Schema::hasTable('campaign_settings') && !Schema::hasColumn('campaign_settings', 'show_countdown')) {
            Schema::table('campaign_settings', function (Blueprint $table) {
                $table->boolean('show_countdown')->default(true)->after('election_date');
            });
        }

        // 2. Add countdown section into section_settings if not exists
        if (Schema::hasTable('section_settings')) {
            $exists = DB::table('section_settings')->where('section_key', 'countdown')->exists();
            if (!$exists) {
                DB::table('section_settings')->insert([
                    'section_key' => 'countdown',
                    'title_bn' => 'ভোটের লাইভ কাউন্টডাউন টাইমার',
                    'description_bn' => 'হোমপেজে নির্বাচনের বাকি দিন, ঘণ্টা, মিনিট ও সেকেন্ড গণনার লাইভ টাইমার দেখানো বা বন্ধ রাখা।',
                    'is_visible' => true,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campaign_settings') && Schema::hasColumn('campaign_settings', 'show_countdown')) {
            Schema::table('campaign_settings', function (Blueprint $table) {
                $table->dropColumn('show_countdown');
            });
        }

        if (Schema::hasTable('section_settings')) {
            DB::table('section_settings')->where('section_key', 'countdown')->delete();
        }
    }
};
