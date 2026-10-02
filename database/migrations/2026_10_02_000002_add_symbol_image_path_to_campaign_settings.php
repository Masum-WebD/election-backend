<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_settings', 'symbol_image_path')) {
                $table->string('symbol_image_path')->nullable()->after('symbol_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaign_settings', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_settings', 'symbol_image_path')) {
                $table->dropColumn('symbol_image_path');
            }
        });
    }
};
