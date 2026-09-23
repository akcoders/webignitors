<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('seo.sitemap', [
            'posts' => BlogPost::query()->published()->latest('updated_at')->get(),
        ])->header('Content-Type', 'application/xml');
    }
}
