<?php

use App\Livewire\Customer\Agents;
use App\Livewire\Frontend\LandingPage;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
})->name('home');
*/

Route::get('/', LandingPage::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('agents', Agents::class)->name('agents');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

/*
|--------------------------------------------------------------------------
| Cron jobs
|--------------------------------------------------------------------------
|
*/
Route::get('/cron/schedule/{token}', [CronController::class, 'runSchedule']);
Route::get('/cron/queue/{token}', [CronController::class, 'runQueue']);
Route::get('/cron/notifications/{token}', [CronController::class, 'runNotifications']);


require __DIR__.'/auth.php';
