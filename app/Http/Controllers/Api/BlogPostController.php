<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Services\BlogPostWriter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BlogPostController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = BlogPost::query()->with('author')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q');
                $query->where(fn ($builder) => $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return BlogPostResource::collection($posts);
    }

    public function store(SaveBlogPostRequest $request, BlogPostWriter $writer): BlogPostResource
    {
        $post = $writer->save(new BlogPost, $request->validated());

        return new BlogPostResource($post);
    }

    public function show(BlogPost $blogPost): BlogPostResource
    {
        return new BlogPostResource($blogPost->load('author'));
    }

    public function update(SaveBlogPostRequest $request, BlogPost $blogPost, BlogPostWriter $writer): BlogPostResource
    {
        return new BlogPostResource($writer->save($blogPost, $request->validated()));
    }

    public function destroy(BlogPost $blogPost): Response
    {
        $blogPost->delete();

        return response()->noContent();
    }
}
