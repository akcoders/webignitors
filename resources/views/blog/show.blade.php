@extends('layouts.app')

@section('title', $post->seo_title)
@section('meta_description', $post->seo_description)
@section('canonical', $post->canonical_url ?: route('blog.show', $post))
@section('robots', ($post->robots_index ? 'index' : 'noindex').','.($post->robots_follow ? 'follow' : 'nofollow'))
@section('og_type', 'article')
@section('og_title', $post->og_title ?: $post->seo_title)
@section('og_description', $post->og_description ?: $post->seo_description)
@section('og_image', url($post->og_image_url ?: $post->featured_image_url ?: '/images/blog/api-first-software-business-applications.jpg'))

@push('head')
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
<meta property="article:section" content="{{ $post->category }}">
@foreach($post->tags ?? [] as $tag)<meta property="article:tag" content="{{ $tag }}">@endforeach
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => $post->schema_type ?: 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->seo_description,
    'image' => [url($post->og_image_url ?: $post->featured_image_url ?: '/images/blog/api-first-software-business-applications.jpg')],
    'datePublished' => $post->published_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => ['@type' => 'Organization', 'name' => 'WebIgnitors', 'url' => route('home')],
    'publisher' => ['@type' => 'Organization', 'name' => 'WebIgnitors', 'url' => route('home')],
    'mainEntityOfPage' => $post->canonical_url ?: route('blog.show', $post),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article class="article-page">
    <header class="article-hero">
        <div class="container">
            <nav class="article-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><a href="{{ route('blog.index') }}">Insights</a><i class="bi bi-chevron-right"></i><span>{{ $post->category }}</span></nav>
            <div class="article-heading reveal">
                <span class="article-category">{{ $post->category }}</span>
                <h1>{{ $post->title }}</h1>
                <p>{{ $post->excerpt }}</p>
                <div class="article-byline"><span class="brand-mark"><span>WI</span></span><div><strong>WebIgnitors editorial team</strong><small>Published {{ $post->published_at->format('F j, Y') }} · {{ $post->reading_time }} min read</small></div></div>
            </div>
        </div>
    </header>

    <div class="container">
        <figure class="article-cover reveal"><img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt }}" width="1600" height="900"></figure>
        <div class="article-layout">
            <aside class="article-share">
                <span>Share</span>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
                <button type="button" data-copy-url="{{ route('blog.show', $post) }}" aria-label="Copy article link"><i class="bi bi-link-45deg"></i></button>
            </aside>
            <div class="article-content">{!! $post->content_html !!}</div>
            <aside class="article-side-card">
                <span>Make it practical</span><h2>Want this applied to your business?</h2><p>We design and build software, automation and growth systems around measurable outcomes.</p><a href="{{ route('contact') }}">Talk to the team <i class="bi bi-arrow-up-right"></i></a>
            </aside>
        </div>
        <div class="article-tags">@foreach($post->tags ?? [] as $tag)<a href="{{ route('blog.index', ['q' => $tag]) }}">#{{ str($tag)->slug() }}</a>@endforeach</div>
    </div>
</article>

@if($relatedPosts->isNotEmpty())
<section class="related-posts"><div class="container"><div class="section-head"><div><span class="eyebrow"><i></i> Keep exploring</span><h2>Related field notes</h2></div><a href="{{ route('blog.index') }}">All insights <i class="bi bi-arrow-right"></i></a></div><div class="blog-grid">@foreach($relatedPosts as $related)<article class="blog-card reveal"><a class="blog-card-media" href="{{ route('blog.show', $related) }}"><img src="{{ $related->thumbnail_url ?: $related->featured_image_url }}" alt="{{ $related->featured_image_alt }}" width="720" height="405" loading="lazy"><span>{{ $related->category }}</span></a><div class="blog-card-body"><div class="blog-meta"><time>{{ $related->published_at->format('M j, Y') }}</time><span>{{ $related->reading_time }} min</span></div><h2><a href="{{ route('blog.show', $related) }}">{{ $related->title }}</a></h2><p>{{ str($related->excerpt)->limit(135) }}</p><a class="blog-card-link" href="{{ route('blog.show', $related) }}">Explore <i class="bi bi-arrow-right"></i></a></div></article>@endforeach</div></div></section>
@endif
@endsection
