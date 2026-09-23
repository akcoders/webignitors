<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBlogPostRequest;
use App\Models\BlogPost;
use App\Services\BlogPostWriter;
use App\Services\BlogSeoAnalyzer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::query()->with('author')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q');
                $query->where(fn ($builder) => $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create(BlogSeoAnalyzer $seoAnalyzer): View
    {
        $post = new BlogPost([
            'status' => 'draft',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'BlogPosting',
        ]);

        return view('admin.blog.create', [
            'post' => $post,
            'seo' => $seoAnalyzer->analyze($post),
        ]);
    }

    public function store(SaveBlogPostRequest $request, BlogPostWriter $writer): RedirectResponse
    {
        $post = $writer->save(new BlogPost, $request->validated(), $request->user()->id);

        return to_route('admin.blog.edit', $post)->with('success', 'Article saved successfully.');
    }

    public function edit(BlogPost $blogPost, BlogSeoAnalyzer $seoAnalyzer): View
    {
        return view('admin.blog.edit', [
            'post' => $blogPost,
            'seo' => $seoAnalyzer->analyze($blogPost),
        ]);
    }

    public function update(SaveBlogPostRequest $request, BlogPost $blogPost, BlogPostWriter $writer): RedirectResponse
    {
        $writer->save($blogPost, $request->validated());

        return back()->with('success', 'Article and SEO settings updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return to_route('admin.blog.index')->with('success', 'Article moved to trash.');
    }

    public function apiDocs(): View
    {
        return view('admin.blog.api-docs');
    }
}
