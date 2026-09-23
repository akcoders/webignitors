@extends('layouts.app')

@section('title', 'Software, AI & Digital Growth Insights')
@section('meta_description', 'Practical WebIgnitors insights on AI applications, software engineering, ecommerce, ERP, CRM, automation and digital growth.')

@section('content')
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-grid">
            <div class="reveal">
                <span class="eyebrow"><i></i> The ignition journal</span>
                <h1>Ideas for building <em>smarter</em> digital businesses.</h1>
                <p>Plain-language field notes on software, AI, ecommerce, automation and the decisions that make technology produce a real return.</p>
            </div>
            <div class="blog-orbit" aria-hidden="true">
                <span class="orbit-ring orbit-one"></span><span class="orbit-ring orbit-two"></span>
                <strong>{{ $posts->total() }}<span>field notes</span></strong>
                <i class="orbit-dot dot-one"></i><i class="orbit-dot dot-two"></i><i class="orbit-dot dot-three"></i>
            </div>
        </div>
    </div>
</section>

<section class="blog-index-section">
    <div class="container">
        <div class="blog-toolbar reveal">
            <form method="GET" action="{{ route('blog.index') }}" role="search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search ideas, tools, strategies…" aria-label="Search insights">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <button type="submit">Search</button>
            </form>
            <div class="blog-categories" aria-label="Filter by category">
                <a class="{{ ! request('category') ? 'active' : '' }}" href="{{ route('blog.index', request()->only('q')) }}">All</a>
                @foreach($categories as $category)
                    <a class="{{ request('category') === $category->category ? 'active' : '' }}" href="{{ route('blog.index', ['category' => $category->category, 'q' => request('q')]) }}">{{ $category->category }} <span>{{ $category->posts_count }}</span></a>
                @endforeach
            </div>
        </div>

        @if($featured && ! request()->filled('q') && ! request()->filled('category'))
            <article class="blog-feature reveal">
                <a class="blog-feature-media" href="{{ route('blog.show', $featured) }}">
                    <img src="{{ $featured->thumbnail_url ?: $featured->featured_image_url }}" alt="{{ $featured->featured_image_alt }}" width="720" height="405" fetchpriority="high">
                    <span>Newest perspective</span>
                </a>
                <div class="blog-feature-copy">
                    <div class="blog-meta"><span>{{ $featured->category }}</span><time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->format('M j, Y') }}</time><span>{{ $featured->reading_time }} min read</span></div>
                    <h2><a href="{{ route('blog.show', $featured) }}">{{ $featured->title }}</a></h2>
                    <p>{{ $featured->excerpt }}</p>
                    <a class="text-link" href="{{ route('blog.show', $featured) }}">Read the field note <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </article>
        @endif

        <div class="blog-grid">
            @forelse($posts as $post)
                <article class="blog-card reveal">
                    <a class="blog-card-media" href="{{ route('blog.show', $post) }}">
                        <img src="{{ $post->thumbnail_url ?: $post->featured_image_url }}" alt="{{ $post->featured_image_alt }}" width="720" height="405" loading="lazy">
                        <span>{{ $post->category }}</span>
                    </a>
                    <div class="blog-card-body">
                        <div class="blog-meta"><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M j, Y') }}</time><span>{{ $post->reading_time }} min</span></div>
                        <h2><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h2>
                        <p>{{ str($post->excerpt)->limit(150) }}</p>
                        <a class="blog-card-link" href="{{ route('blog.show', $post) }}" aria-label="Read {{ $post->title }}">Explore <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            @empty
                <div class="blog-empty"><span>00</span><h2>No field notes found.</h2><p>Try a broader search or clear the current filter.</p><a href="{{ route('blog.index') }}" class="btn btn-ink">See every insight</a></div>
            @endforelse
        </div>

        <div class="blog-pagination">{{ $posts->links() }}</div>
    </div>
</section>

<section class="blog-cta">
    <div class="container"><div class="blog-cta-inner reveal"><span>Need execution, not another tab?</span><h2>Turn one useful idea into a working system.</h2><a href="{{ route('contact') }}" class="btn btn-lime">Start the conversation <i class="bi bi-arrow-up-right"></i></a></div></div>
</section>
@endsection
