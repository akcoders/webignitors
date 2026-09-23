@extends('layouts.admin')

@section('title', 'Blog Management')

@section('content')
<div class="admin-shell admin-blog-shell">
    <header class="admin-page-head">
        <div>
            <span class="admin-kicker"><i></i> Editorial operations</span>
            <h1>Ideas, built to rank.</h1>
            <p>Publish useful articles and control every important search and social field.</p>
        </div>
        <a class="admin-primary-button" href="{{ route('admin.blog.create') }}"><i class="bi bi-plus-lg"></i> New article</a>
    </header>

    <section class="admin-blog-summary">
        <div><span>All articles</span><strong>{{ $posts->total() }}</strong></div>
        <div><span>Published here</span><strong>{{ $posts->where('status', 'published')->count() }}</strong></div>
        <div><span>Average SEO</span><strong>{{ round($posts->avg('seo_score') ?: 0) }}<small>/100</small></strong></div>
        <a href="{{ route('admin.blog.api-docs') }}"><i class="bi bi-braces"></i><span>Publishing API<small>View endpoints & examples</small></span><i class="bi bi-arrow-up-right"></i></a>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head admin-blog-filter-head">
            <div><span class="admin-panel-label">Content library</span><h2>Articles</h2></div>
            <form method="GET" action="{{ route('admin.blog.index') }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title or slug">
                <select name="status" aria-label="Filter by status">
                    <option value="">Every status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="admin-table admin-blog-table">
                <thead><tr><th>Article</th><th>Status</th><th>SEO</th><th>Published</th><th>Updated</th><th></th></tr></thead>
                <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td><div class="admin-blog-title">@if($post->featured_image_url)<img src="{{ $post->featured_image_url }}" alt="">@endif<div><strong>{{ $post->title }}</strong><small>/blog/{{ $post->slug }} · {{ $post->category ?: 'Uncategorised' }}</small></div></div></td>
                        <td><span class="admin-status status-{{ $post->status }}"><i></i>{{ ucfirst($post->status) }}</span></td>
                        <td><div class="seo-mini-score score-{{ $post->seo_score >= 80 ? 'good' : ($post->seo_score >= 55 ? 'fair' : 'poor') }}"><strong>{{ $post->seo_score }}</strong><span><i style="width: {{ $post->seo_score }}%"></i></span></div></td>
                        <td>{{ $post->published_at?->format('d M Y') ?: '—' }}<small>{{ $post->published_at?->format('h:i A') }}</small></td>
                        <td>{{ $post->updated_at->diffForHumans() }}</td>
                        <td><div class="admin-table-actions">@if($post->isPubliclyVisible())<a href="{{ route('blog.show', $post) }}" target="_blank" aria-label="View article"><i class="bi bi-box-arrow-up-right"></i></a>@endif<a href="{{ route('admin.blog.edit', $post) }}" aria-label="Edit article"><i class="bi bi-pencil"></i></a></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty">No articles match this view. Create the first one when you are ready.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-table-pagination">{{ $posts->links() }}</div>
    </section>
</div>
@endsection
