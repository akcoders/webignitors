<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WebsiteAuditController;
use App\Http\Controllers\WebsiteReportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/services', 'pages.services')->name('services');
Route::view('/services/web-development', 'pages.services.web-development')->name('services.web');
Route::view('/services/mobile-apps', 'pages.services.mobile-apps')->name('services.mobile');
Route::view('/services/digital-marketing', 'pages.services.digital-marketing')->name('services.marketing');
Route::view('/services/ai-integration', 'pages.services.ai-integration')->name('services.ai');
Route::view('/work', 'pages.work')->name('work');
Route::view('/process', 'pages.process')->name('process');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/website-audit', [WebsiteAuditController::class, 'create'])->name('audit.create');
Route::post('/website-audit', [WebsiteAuditController::class, 'store'])
    ->middleware('throttle:2,1440')
    ->name('audit.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:3,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/dashboard', [WebsiteReportController::class, 'index'])->name('dashboard');
    Route::get('/reports/{report}', [WebsiteReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/status', [WebsiteReportController::class, 'status'])
        ->middleware('throttle:60,1')
        ->name('reports.status');
    Route::get('/reports/{report}/download', [WebsiteReportController::class, 'download'])
        ->name('reports.download');
    Route::get('/reports/{report}/screenshots/{device}', [WebsiteReportController::class, 'screenshot'])
        ->whereIn('device', ['mobile', 'desktop'])
        ->name('reports.screenshot');
});
