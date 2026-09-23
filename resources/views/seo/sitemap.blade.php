<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach([route('home'), route('about'), route('services'), route('work'), route('process'), route('blog.index'), route('audit.create'), route('contact')] as $url)
    <url><loc>{{ $url }}</loc><changefreq>weekly</changefreq><priority>{{ $url === route('home') ? '1.0' : '0.8' }}</priority></url>
@endforeach
@foreach($posts as $post)
    <url><loc>{{ route('blog.show', $post) }}</loc><lastmod>{{ $post->updated_at->toAtomString() }}</lastmod><changefreq>monthly</changefreq><priority>0.7</priority></url>
@endforeach
</urlset>
