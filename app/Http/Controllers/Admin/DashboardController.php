<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignSetting;
use App\Models\SectionSetting;
use App\Models\Manifesto;
use App\Models\Grievance;
use App\Models\GalleryItem;
use App\Models\CampaignVideo;
use App\Models\Endorsement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Dashboard Overview & Master Section Switches
     */
    public function index()
    {
        $settings = CampaignSetting::first();
        $sections = SectionSetting::orderBy('sort_order')->get();
        $grievances = Grievance::all();
        $manifestos = Manifesto::all();
        $gallery = GalleryItem::all();
        $videos = CampaignVideo::all();
        $endorsements = Endorsement::all();

        $stats = [
            'total_grievances' => $grievances->count(),
            'pending_grievances' => $grievances->where('status', 'পর্যালোচনায় গৃহীত')->count(),
            'resolved_grievances' => $grievances->where('status', 'সম্পন্ন')->count(),
            'active_sections' => $sections->where('is_visible', true)->count(),
            'total_sections' => $sections->count(),
            'support_pledges' => $settings ? $settings->support_pledge_count : 12485,
            'total_manifestos' => $manifestos->count(),
            'total_photos' => $gallery->count(),
            'total_videos' => $videos->count(),
            'total_endorsements' => $endorsements->count(),
        ];

        return view('admin.dashboard', compact('settings', 'sections', 'stats'));
    }

    /**
     * Instant Toggle Section Visibility (হোম পেজে সেকশন দেখানো বা লুকানোর সুইচ)
     */
    public function toggleSection($id)
    {
        $section = SectionSetting::findOrFail($id);
        $section->is_visible = !$section->is_visible;
        $section->save();

        if ($section->section_key === 'symbol') {
            $settings = CampaignSetting::first();
            if ($settings) {
                $settings->show_symbol = $section->is_visible;
                $settings->save();
            }
        } elseif ($section->section_key === 'countdown') {
            $settings = CampaignSetting::first();
            if ($settings && Schema::hasColumn('campaign_settings', 'show_countdown')) {
                $settings->show_countdown = $section->is_visible;
                $settings->save();
            }
        }

        $statusText = $section->is_visible ? 'হোম পেজে প্রদর্শিত হবে।' : 'হোম পেজ থেকে লুকানো হয়েছে।';

        return redirect()->back()->with('success', "‘{$section->title_bn}’ সফলভাবে {$statusText}");
    }

    /**
     * Candidate, Marka, Countdown, Stats & Bio Settings Page
     */
    public function settings()
    {
        $settings = CampaignSetting::first();
        $sections = SectionSetting::orderBy('sort_order')->get();
        $statsSection = $sections->firstWhere('section_key', 'stats');

        return view('admin.settings', compact('settings', 'sections', 'statsSection'));
    }

    /**
     * Update Candidate Profile, Marka, Countdown, 4 Stats & 2-Part Bio Settings
     */
    public function updateSettings(Request $request)
    {
        $settings = CampaignSetting::first();
        if (!$settings) {
            $settings = new CampaignSetting();
        }

        $uploadDir = public_path('uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 1. Basic Candidate Info
        $settings->candidate_name = $request->input('candidate_name', $settings->candidate_name);
        $settings->candidate_short_name = $request->input('candidate_short_name', $settings->candidate_short_name ?: 'রফিকুল ইসলাম চৌধুরী');
        $settings->candidate_role = $request->input('candidate_role', $settings->candidate_role);
        $settings->union_name = $request->input('union_name', $settings->union_name);
        $settings->upazila = $request->input('upazila', $settings->upazila);
        $settings->district = $request->input('district', $settings->district);
        $settings->election_year = $request->input('election_year', $settings->election_year ?: '২০২৬');
        $settings->slogan = $request->input('slogan', $settings->slogan);
        $settings->sub_slogan = $request->input('sub_slogan', $settings->sub_slogan);
        $settings->phone_primary = $request->input('phone_primary', $settings->phone_primary);
        $settings->phone_secondary = $request->input('phone_secondary', $settings->phone_secondary);
        $settings->whatsapp = $request->input('whatsapp', $settings->whatsapp);
        $settings->email = $request->input('email', $settings->email);
        $settings->office_address = $request->input('office_address', $settings->office_address);

        // 2. Election Countdown Date & Time & Toggle
        if ($request->filled('election_date')) {
            $settings->election_date = $request->input('election_date');
        }
        $showCountdown = $request->has('show_countdown') ? (bool) $request->input('show_countdown') : false;
        if (Schema::hasColumn('campaign_settings', 'show_countdown')) {
            $settings->show_countdown = $showCountdown;
        }

        // Sync countdown section visibility
        $countdownSection = SectionSetting::where('section_key', 'countdown')->first();
        if ($countdownSection) {
            $countdownSection->is_visible = $showCountdown;
            $countdownSection->save();
        }

        // 3. Digital Support Count (দোয়া ও সমর্থন সংখ্যা)
        if ($request->filled('support_pledge_count')) {
            $settings->support_pledge_count = (int) $request->input('support_pledge_count');
        }

        // 4. Marka (Symbol) Settings & Image
        $settings->symbol_name = $request->input('symbol_name', $settings->symbol_name);
        $settings->symbol_tagline = $request->input('symbol_tagline', $settings->symbol_tagline);
        $showSymbol = $request->has('show_symbol') ? (bool) $request->input('show_symbol') : false;
        $settings->show_symbol = $showSymbol;

        // Handle Symbol Image Upload
        if ($request->hasFile('symbol_file')) {
            $request->validate([
                'symbol_file' => 'image|mimes:jpeg,png,jpg,webp,svg|max:8192',
            ]);
            $file = $request->file('symbol_file');
            $filename = 'symbol_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $settings->symbol_image_path = '/uploads/' . $filename;
        } elseif ($request->filled('symbol_url')) {
            $settings->symbol_image_path = $request->input('symbol_url');
        }

        // Sync symbol section visibility
        $symbolSection = SectionSetting::where('section_key', 'symbol')->first();
        if ($symbolSection) {
            $symbolSection->is_visible = $showSymbol;
            $symbolSection->save();
        }

        // 5. Candidate Portrait Photo Upload
        if ($request->hasFile('portrait_file')) {
            $request->validate([
                'portrait_file' => 'image|mimes:jpeg,png,jpg,webp|max:8192',
            ]);
            $file = $request->file('portrait_file');
            $filename = 'candidate_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $settings->portrait_path = '/uploads/' . $filename;
        } elseif ($request->filled('portrait_url')) {
            $settings->portrait_path = $request->input('portrait_url');
        }

        // 6. Dynamic 4 Stats Cards (জনকল্যাণে নিবেদিত, উন্নয়ন ও সামাজিক উদ্যোগ, ইত্যাদি)
        if ($request->has('stat_labels')) {
            $statLabels = $request->input('stat_labels', []);
            $statValues = $request->input('stat_values', []);
            $statUnits = $request->input('stat_units', []);
            $newStats = [];

            for ($i = 0; $i < count($statLabels); $i++) {
                if (!empty(trim($statLabels[$i] ?? ''))) {
                    $newStats[] = [
                        'label' => trim($statLabels[$i]),
                        'value' => trim($statValues[$i] ?? ''),
                        'unit' => trim($statUnits[$i] ?? ''),
                    ];
                }
            }
            if (!empty($newStats)) {
                $settings->stats = $newStats;
            }
        }

        // Section toggle for stats cards
        $statsSection = SectionSetting::where('section_key', 'stats')->first();
        if ($statsSection) {
            $statsSection->is_visible = $request->has('show_stats');
            $statsSection->save();
        }

        // 7. Dynamic Candidate Bio & Past Achievements (২টি পার্ট)
        $currentBio = $settings->bio_data ?: [];
        
        // Part 1: Personal Bio & Heritage
        $bioSummary = $request->input('bio_summary', $currentBio['summary'] ?? '');
        $familyHeritage = $request->input('family_heritage', $currentBio['familyHeritage'] ?? '');
        $socialPhilosophy = $request->input('social_philosophy', $currentBio['socialPhilosophy'] ?? '');

        // Education array
        $eduDegrees = $request->input('edu_degrees', []);
        $eduInstitutes = $request->input('edu_institutes', []);
        $eduYears = $request->input('edu_years', []);
        $educationList = [];

        if (is_array($eduDegrees) && count($eduDegrees) > 0) {
            for ($i = 0; $i < count($eduDegrees); $i++) {
                if (!empty(trim($eduDegrees[$i] ?? ''))) {
                    $educationList[] = [
                        'degree' => trim($eduDegrees[$i]),
                        'institute' => trim($eduInstitutes[$i] ?? ''),
                        'year' => trim($eduYears[$i] ?? ''),
                    ];
                }
            }
        } else {
            $educationList = $currentBio['education'] ?? [];
        }

        // Part 2: Past Achievements List
        $achievementsInput = $request->input('past_achievements_text', '');
        $achievementsList = [];
        if (!empty($achievementsInput)) {
            $lines = explode("\n", $achievementsInput);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $achievementsList[] = ltrim($trimmed, '-•*0123456789. ');
                }
            }
        } elseif (isset($currentBio['pastAchievements']) && is_array($currentBio['pastAchievements'])) {
            $achievementsList = $currentBio['pastAchievements'];
        }

        $settings->bio_data = [
            'summary' => $bioSummary,
            'familyHeritage' => $familyHeritage,
            'socialPhilosophy' => $socialPhilosophy,
            'education' => $educationList,
            'pastAchievements' => $achievementsList,
        ];

        $settings->save();

        return redirect()->back()->with('success', 'প্রার্থীর তথ্য, মার্কা, কাউন্টডাউন, পরিসংখ্যান ও পরিচিতি সফলভাবে সংরক্ষিত হয়েছে।');
    }

    /**
     * Manifesto Categories & Points View
     */
    public function manifestos()
    {
        $settings = CampaignSetting::first();
        $manifestos = Manifesto::orderBy('sort_order')->get();

        return view('admin.manifestos', compact('settings', 'manifestos'));
    }

    /**
     * Store new Manifesto category with points
     */
    public function storeManifesto(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'required|string|max:100',
        ]);

        $categoryId = $request->filled('category_id') 
            ? strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $request->input('category_id')))
            : 'manifesto_' . time();

        $pointsInput = $request->input('points_text', '');
        $points = [];
        foreach (explode("\n", $pointsInput) as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $points[] = ltrim($trimmed, '-•*0123456789. ');
            }
        }

        if (empty($points)) {
            $points = ['টেকসই উন্নয়ন ও নাগরিক অধিকার নিশ্চিতকরণ'];
        }

        Manifesto::create([
            'category_id' => $categoryId,
            'title' => $request->input('title'),
            'badge' => $request->input('badge'),
            'headline' => $request->input('headline', ''),
            'icon' => $request->input('icon', 'Target'),
            'color' => $request->input('color', 'emerald'),
            'points' => $points,
            'is_active' => true,
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        return redirect()->back()->with('success', 'নতুন নির্বাচনী ইশতেহার ক্যাটাগরি সফলভাবে যুক্ত হয়েছে।');
    }

    /**
     * Update Manifesto Category and Points
     */
    public function updateManifesto(Request $request, $id)
    {
        $manifesto = Manifesto::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'required|string|max:100',
        ]);

        $pointsInput = $request->input('points_text', '');
        $points = [];
        foreach (explode("\n", $pointsInput) as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed)) {
                $points[] = ltrim($trimmed, '-•*0123456789. ');
            }
        }

        $manifesto->title = $request->input('title');
        $manifesto->badge = $request->input('badge');
        $manifesto->headline = $request->input('headline');
        $manifesto->points = !empty($points) ? $points : $manifesto->points;
        $manifesto->is_active = $request->has('is_active');
        $manifesto->sort_order = (int) $request->input('sort_order', $manifesto->sort_order);
        $manifesto->save();

        return redirect()->back()->with('success', "ইশতেহার ‘{$manifesto->badge}’ সফলভাবে আপডেট হয়েছে।");
    }

    /**
     * Delete Manifesto
     */
    public function deleteManifesto($id)
    {
        $manifesto = Manifesto::findOrFail($id);
        $manifesto->delete();

        return redirect()->back()->with('success', 'ইশতেহার ক্যাটাগরি মুছে ফেলা হয়েছে।');
    }

    /**
     * Gallery Items View
     */
    public function gallery()
    {
        $settings = CampaignSetting::first();
        $gallery = GalleryItem::orderBy('sort_order')->orderBy('created_at', 'desc')->get();

        return view('admin.gallery', compact('settings', 'gallery'));
    }

    /**
     * Store new Photo Gallery Item
     */
    public function storeGalleryItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
        ]);

        $imageUrl = '/assets/candidate_portrait.jpg';

        if ($request->hasFile('image_file')) {
            $request->validate([
                'image_file' => 'image|mimes:jpeg,png,jpg,webp|max:8192',
            ]);
            $file = $request->file('image_file');
            $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $imageUrl = '/uploads/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imageUrl = $request->input('image_url');
        }

        GalleryItem::create([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'location' => $request->input('location', 'চরশাহী ইউনিয়ন'),
            'date_text' => $request->input('date_text', 'নির্বাচনী সমাবেশ ২০২৬'),
            'image_url' => $imageUrl,
            'description' => $request->input('description', ''),
            'is_active' => true,
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        return redirect()->back()->with('success', 'প্রচার অ্যালবামে নতুন ছবি সফলভাবে যুক্ত হয়েছে।');
    }

    /**
     * Delete Gallery Item
     */
    public function deleteGalleryItem($id)
    {
        $item = GalleryItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'ছবি সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Campaign Videos & Speeches View
     */
    public function videos()
    {
        $settings = CampaignSetting::first();
        $videos = CampaignVideo::orderBy('sort_order')->orderBy('created_at', 'desc')->get();

        return view('admin.videos', compact('settings', 'videos'));
    }

    /**
     * Store new Campaign Video
     */
    public function storeVideo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_input' => 'required|string',
        ]);

        $youtubeId = $this->extractYoutubeId($request->input('youtube_input'));

        CampaignVideo::create([
            'title' => $request->input('title'),
            'speaker' => $request->input('speaker', 'আলহাজ্ব মো: রফিকুল ইসলাম চৌধুরী'),
            'duration' => $request->input('duration', '১২:৪৫ মিনিট'),
            'views_text' => $request->input('views_text', '১০,০০০+ ভিউজ'),
            'youtube_id' => $youtubeId,
            'summary' => $request->input('summary', ''),
            'is_active' => true,
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        return redirect()->back()->with('success', 'ইউটিউব ভিডিও ও বক্তব্য সফলভাবে যুক্ত হয়েছে।');
    }

    /**
     * Delete Campaign Video
     */
    public function deleteVideo($id)
    {
        $video = CampaignVideo::findOrFail($id);
        $video->delete();

        return redirect()->back()->with('success', 'ভিডিও সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Citizen Grievances View
     */
    public function grievances()
    {
        $settings = CampaignSetting::first();
        $grievances = Grievance::orderBy('created_at', 'desc')->get();

        $stats = [
            'total_grievances' => $grievances->count(),
            'pending_grievances' => $grievances->where('status', 'পর্যালোচনায় গৃহীত')->count(),
            'resolved_grievances' => $grievances->where('status', 'সম্পন্ন')->count(),
        ];

        return view('admin.grievances', compact('settings', 'grievances', 'stats'));
    }

    /**
     * Update Citizen Grievance Status
     */
    public function updateGrievanceStatus(Request $request, $id)
    {
        $grievance = Grievance::findOrFail($id);
        $grievance->status = $request->input('status', $grievance->status);
        $grievance->admin_notes = $request->input('admin_notes', $grievance->admin_notes);
        $grievance->save();

        return redirect()->back()->with('success', "আবেদন #{$grievance->tracking_id}-এর স্ট্যাটাস আপডেট হয়েছে।");
    }

    /**
     * Delete Citizen Grievance
     */
    public function deleteGrievance($id)
    {
        $grievance = Grievance::findOrFail($id);
        $grievance->delete();

        return redirect()->back()->with('success', 'আবেদনটি সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Endorsements View
     */
    public function endorsements()
    {
        $settings = CampaignSetting::first();
        $endorsements = Endorsement::orderBy('created_at', 'desc')->get();

        return view('admin.endorsements', compact('settings', 'endorsements'));
    }

    /**
     * Toggle Endorsement Approval
     */
    public function toggleEndorsement($id)
    {
        $item = Endorsement::findOrFail($id);
        $item->is_approved = !$item->is_approved;
        $item->save();

        return redirect()->back()->with('success', 'সমর্থন বার্তার স্ট্যাটাস আপডেট হয়েছে।');
    }

    /**
     * Delete Endorsement
     */
    public function deleteEndorsement($id)
    {
        $item = Endorsement::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'সমর্থন বার্তা মুছে ফেলা হয়েছে।');
    }

    /**
     * Helper: Extract 11-char YouTube ID from any URL or input
     */
    private function extractYoutubeId($input)
    {
        if (empty($input)) return 'dQw4w9WgXcQ';
        $input = trim($input);
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/))([a-zA-Z0-9_-]{11})/', $input, $matches)) {
            return $matches[1];
        }
        if (strlen($input) === 11) {
            return $input;
        }
        return 'dQw4w9WgXcQ';
    }
}
