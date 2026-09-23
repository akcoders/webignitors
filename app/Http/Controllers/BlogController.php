<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->published()->with('author');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category = trim((string) $request->query('category'))) {
            $query->where('category', $category);
        }

        return view('blog.index', [
            'posts' => $query->latest('published_at')->paginate(9)->withQueryString(),
            'featured' => BlogPost::query()->published()->latest('published_at')->first(),
            'categories' => BlogPost::query()->published()
                ->whereNotNull('category')
                ->selectRaw('category, count(*) as posts_count')
                ->groupBy('category')
                ->orderByDesc('posts_count')
                ->get(),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->isPubliclyVisible(), 404);

        return view('blog.show', [
            'post' => $blogPost->load('author'),
            'relatedPosts' => BlogPost::query()->published()
                ->whereKeyNot($blogPost->id)
                ->when($blogPost->category, fn ($query) => $query->where('category', $blogPost->category))
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
