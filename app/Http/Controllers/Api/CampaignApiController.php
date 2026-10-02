<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignSetting;
use App\Models\SectionSetting;
use App\Models\Manifesto;
use App\Models\Grievance;
use App\Models\GalleryItem;
use App\Models\CampaignVideo;
use App\Models\Endorsement;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignApiController extends Controller
{
    /**
     * Get complete campaign data including section visibility switches
     */
    public function getCampaignData()
    {
        $settings = CampaignSetting::first();
        $sections = SectionSetting::orderBy('sort_order')->get();

        // Convert section settings to a quick key-value map for frontend
        $sectionMap = [];
        foreach ($sections as $s) {
            $sectionMap[$s->section_key] = (bool) $s->is_visible;
        }

        $manifestos = Manifesto::where('is_active', true)->orderBy('sort_order')->get();
        $grievances = Grievance::where('is_public', true)->orderBy('created_at', 'desc')->take(10)->get();
        $gallery = GalleryItem::where('is_active', true)->orderBy('sort_order')->get();
        $videos = CampaignVideo::where('is_active', true)->orderBy('sort_order')->get();
        $endorsements = Endorsement::where('is_approved', true)->orderBy('created_at', 'desc')->get();

        $wards = [
            ['no' => 1, 'name' => '১নং ওয়ার্ড (উত্তর চরশাহী ও বাঘমারা)'],
            ['no' => 2, 'name' => '২নং ওয়ার্ড (পশ্চিম চরশাহী ও মিয়াপাড়া)'],
            ['no' => 3, 'name' => '৩নং ওয়ার্ড (চরশাহী মধ্যপাড়া ও বাজার এলাকা)'],
            ['no' => 4, 'name' => '৪নং ওয়ার্ড (দক্ষিণ চরশাহী ও হাওলাদার বাড়ি)'],
            ['no' => 5, 'name' => '৫নং ওয়ার্ড (পূর্ব চরশাহী ও ফকির বাড়ি)'],
            ['no' => 6, 'name' => '৬নং ওয়ার্ড (রামপুর ও কাশিমপুর)'],
            ['no' => 7, 'name' => '৭নং ওয়ার্ড (গোবিন্দপুর ও চন্ডিপুর)'],
            ['no' => 8, 'name' => '৮নং ওয়ার্ড (মোল্লাপাড়া ও নয়াহাট)'],
            ['no' => 9, 'name' => '৯নং ওয়ার্ড (আনন্দপুর ও চর কাদিরা)'],
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings,
            'sections' => $sectionMap,
            'sectionList' => $sections,
            'manifestos' => $manifestos,
            'grievances' => $grievances,
            'gallery' => $gallery,
            'videos' => $videos,
            'endorsements' => $endorsements,
            'wards' => $wards,
        ]);
    }

    /**
     * Get section settings list
     */
    public function getSectionSettings()
    {
        $sections = SectionSetting::orderBy('sort_order')->get();
        return response()->json([
            'success' => true,
            'sections' => $sections,
        ]);
    }

    /**
     * Toggle a section visibility on/off
     */
    public function toggleSection(Request $request)
    {
        $key = $request->input('section_key');
        $section = SectionSetting::where('section_key', $key)->first();

        if (!$section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $section->is_visible = !$section->is_visible;
        $section->save();

        if ($section->section_key === 'symbol') {
            $settings = CampaignSetting::first();
            if ($settings) {
                $settings->show_symbol = $section->is_visible;
                $settings->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Section visibility updated successfully',
            'section' => $section,
        ]);
    }

    /**
     * Store new Citizen Grievance / Petition
     */
    public function storeGrievance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'ward' => 'required|string',
            'village' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'category' => 'required|string|max:100',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trackingId = 'UP-2026-' . rand(1000, 9999);

        $grievance = Grievance::create([
            'tracking_id' => $trackingId,
            'name' => $request->name,
            'ward' => $request->ward,
            'village' => $request->village,
            'phone' => $request->phone,
            'category' => $request->category,
            'message' => $request->message,
            'status' => 'পর্যালোচনায় গৃহীত',
            'is_public' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'আপনার অভিযোগ/পরামর্শ সফলভাবে জমা হয়েছে।',
            'receipt' => [
                'id' => $trackingId,
                'name' => $grievance->name,
                'category' => $grievance->category,
                'phone' => $grievance->phone,
                'ward' => $grievance->ward,
                'status' => $grievance->status,
                'date' => 'আজকে'
            ],
            'grievance' => $grievance,
        ], 201);
    }

    /**
     * Get Grievance list
     */
    public function getGrievances()
    {
        $grievances = Grievance::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'grievances' => $grievances,
        ]);
    }

    /**
     * Store Digital Supporter / Prayer Pledge
     */
    public function storePledge(Request $request)
    {
        $settings = CampaignSetting::first();
        if ($settings) {
            $settings->increment('support_pledge_count');
            $newCount = $settings->support_pledge_count;
        } else {
            $newCount = 12486;
        }

        Pledge::create([
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'দোয়া ও সমর্থন নিশ্চিত হয়েছে!',
            'supportCount' => $newCount,
        ]);
    }

    /**
     * Store citizen endorsement
     */
    public function storeEndorsement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'quote' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $endorsement = Endorsement::create([
            'name' => $request->name,
            'title' => $request->profession ?? 'সচেতন ইউনিয়নবাসী',
            'village' => $request->village ?? 'চরশাহী ইউনিয়ন',
            'quote' => $request->quote,
            'image_url' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80',
            'is_approved' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'সমর্থন বার্তা সফলভাবে প্রকাশ হয়েছে।',
            'endorsement' => $endorsement,
        ], 201);
    }
}
