<?php

use App\Http\Controllers\InstallerController;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\HowToBuyPage;
use App\Livewire\MigrationDetailPage;
use App\Livewire\MigrationPage;
use App\Livewire\RankDetailPage;
use App\Livewire\ShopPage;
use App\Livewire\TermsPage;
use App\Livewire\TrackingDetailPage;
use App\Livewire\TrackingPage;
use Illuminate\Support\Facades\Route;

Route::view('invalid-license', 'errors.invalid-license')->name('license.invalid');

Route::prefix('install')->name('installer.')->group(function () {
    Route::get('/', [InstallerController::class, 'requirements'])->name('requirements');
    Route::post('/requirements', [InstallerController::class, 'storeRequirements'])->name('requirements.store');

    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database', [InstallerController::class, 'storeDatabase'])->name('database.store');

    Route::get('/app-settings', [InstallerController::class, 'appSettings'])->name('app-settings');
    Route::post('/app-settings', [InstallerController::class, 'storeAppSettings'])->name('app-settings.store');

    Route::get('/admin-user', [InstallerController::class, 'adminUser'])->name('admin-user');
    Route::post('/admin-user', [InstallerController::class, 'storeAdminUser'])->name('admin-user.store');

    Route::get('/finish', [InstallerController::class, 'finish'])->name('finish');
    Route::post('/finish', [InstallerController::class, 'complete'])->name('complete');
});

Route::middleware(['installed', 'license.active'])->group(function () {
    Route::get('/', HomePage::class)->name('home');
    Route::get('terms', TermsPage::class)->name('terms');
    Route::get('cara-pembelian', HowToBuyPage::class)->name('how-to-buy');
    Route::get('shop', ShopPage::class)->name('shop');
    Route::get('cek-pembelian', TrackingPage::class)->name('tracking');
    Route::get('rank/{rank}', RankDetailPage::class)->name('rank.detail');
    Route::get('checkout/{rank}', CheckoutPage::class)->name('checkout');
    Route::get('pembelian/{uuid}', TrackingDetailPage::class)->name('tracking.detail');
    Route::get('/migrasi', MigrationPage::class)->name('migration');
    Route::get('/detail-migrasi/{uuid}', MigrationDetailPage::class)->name('migration.detail');
});
