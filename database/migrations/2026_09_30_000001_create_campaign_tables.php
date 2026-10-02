<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Campaign & Candidate Settings
        Schema::create('campaign_settings', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('candidate_role')->default('চেয়ারম্যান পদপ্রার্থী');
            $table->string('union_name')->default('৭নং চরশাহী ইউনিয়ন পরিষদ');
            $table->string('upazila')->default('লক্ষ্মীপুর সদর');
            $table->string('district')->default('লক্ষ্মীপুর');
            $table->string('election_year')->default('২০২৬');
            $table->dateTime('election_date')->nullable();
            $table->string('symbol_name')->default('আনারস মার্কা');
            $table->string('symbol_tagline')->nullable();
            $table->string('slogan', 500);
            $table->text('sub_slogan')->nullable();
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('office_address')->nullable();
            $table->string('meeting_time')->nullable();
            $table->string('portrait_path')->nullable();
            $table->unsignedBigInteger('support_pledge_count')->default(12485);
            $table->json('stats')->nullable();
            $table->json('bio_data')->nullable();
            $table->timestamps();
        });

        // 2. Section Visibility Settings (Home page dynamic switches)
        Schema::create('section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('title_bn');
            $table->string('description_bn')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Manifesto Categories & Promises
        Schema::create('manifestos', function (Blueprint $table) {
            $table->id();
            $table->string('category_id')->unique();
            $table->string('title');
            $table->string('badge');
            $table->string('headline')->nullable();
            $table->string('icon')->default('Target');
            $table->string('color')->default('emerald');
            $table->json('points');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Citizen Grievances & Petitions
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->string('name');
            $table->string('ward');
            $table->string('village');
            $table->string('phone');
            $table->string('category');
            $table->text('message');
            $table->string('status')->default('পর্যালোচনায় গৃহীত');
            $table->boolean('is_public')->default(true);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 5. Media Gallery (Photos)
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('location');
            $table->string('date_text');
            $table->string('image_url');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Campaign Speeches & Videos
        Schema::create('campaign_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('speaker');
            $table->string('duration');
            $table->string('views_text')->default('১০,০০০+ ভিউজ');
            $table->string('youtube_id')->default('dQw4w9WgXcQ');
            $table->text('summary')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 7. Community Endorsements
        Schema::create('endorsements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('village');
            $table->text('quote');
            $table->string('image_url')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 8. Digital Supporter Pledges
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pledges');
        Schema::dropIfExists('endorsements');
        Schema::dropIfExists('campaign_videos');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('grievances');
        Schema::dropIfExists('manifestos');
        Schema::dropIfExists('section_settings');
        Schema::dropIfExists('campaign_settings');
    }
};
