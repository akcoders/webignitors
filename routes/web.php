<?php

use App\Http\Controllers\ContactController;
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
