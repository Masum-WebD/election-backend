<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CampaignApiController;

Route::get('/campaign-data', [CampaignApiController::class, 'getCampaignData']);
Route::get('/section-settings', [CampaignApiController::class, 'getSectionSettings']);
Route::post('/section-settings/toggle', [CampaignApiController::class, 'toggleSection']);

Route::get('/grievances', [CampaignApiController::class, 'getGrievances']);
Route::post('/grievances', [CampaignApiController::class, 'storeGrievance']);

Route::post('/pledge', [CampaignApiController::class, 'storePledge']);
Route::post('/endorsements', [CampaignApiController::class, 'storeEndorsement']);
