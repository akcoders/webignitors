<?php

use App\Http\Controllers\Api\BlogPostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/blog')
    ->middleware(['blog.api', 'throttle:60,1'])
    ->group(function (): void {
        Route::apiResource('posts', BlogPostController::class)
            ->parameters(['posts' => 'blogPost']);
    });
