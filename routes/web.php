<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;

// Public Auth Routes
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard & Section Toggles
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');
    Route::post('/admin/sections/toggle/{id}', [DashboardController::class, 'toggleSection'])->name('admin.sections.toggle');

    // Candidate Settings (Profile, Photo, Marka Image & Toggle, Countdown, Support Count, 4 Stats, Bio 2 parts)
    Route::get('/admin/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');

    // Manifestos (Categories & Points CRUD)
    Route::get('/admin/manifestos', [DashboardController::class, 'manifestos'])->name('admin.manifestos');
    Route::post('/admin/manifestos', [DashboardController::class, 'storeManifesto'])->name('admin.manifestos.store');
    Route::post('/admin/manifestos/{id}', [DashboardController::class, 'updateManifesto'])->name('admin.manifestos.update');
    Route::delete('/admin/manifestos/{id}', [DashboardController::class, 'deleteManifesto'])->name('admin.manifestos.delete');

    // Gallery (Photo Album CRUD)
    Route::get('/admin/gallery', [DashboardController::class, 'gallery'])->name('admin.gallery');
    Route::post('/admin/gallery', [DashboardController::class, 'storeGalleryItem'])->name('admin.gallery.store');
    Route::delete('/admin/gallery/{id}', [DashboardController::class, 'deleteGalleryItem'])->name('admin.gallery.delete');

    // Videos (YouTube speeches CRUD)
    Route::get('/admin/videos', [DashboardController::class, 'videos'])->name('admin.videos');
    Route::post('/admin/videos', [DashboardController::class, 'storeVideo'])->name('admin.videos.store');
    Route::delete('/admin/videos/{id}', [DashboardController::class, 'deleteVideo'])->name('admin.videos.delete');

    // Citizen Grievances
    Route::get('/admin/grievances', [DashboardController::class, 'grievances'])->name('admin.grievances');
    Route::post('/admin/grievances/{id}/status', [DashboardController::class, 'updateGrievanceStatus'])->name('admin.grievances.status');
    Route::delete('/admin/grievances/{id}', [DashboardController::class, 'deleteGrievance'])->name('admin.grievances.delete');

    // Endorsements
    Route::get('/admin/endorsements', [DashboardController::class, 'endorsements'])->name('admin.endorsements');
    Route::post('/admin/endorsements/{id}/toggle', [DashboardController::class, 'toggleEndorsement'])->name('admin.endorsements.toggle');
    Route::delete('/admin/endorsements/{id}', [DashboardController::class, 'deleteEndorsement'])->name('admin.endorsements.delete');
});
