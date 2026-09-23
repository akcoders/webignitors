@extends('layouts.admin')

@section('title', 'Blog Publishing API')

@section('content')
<div class="admin-shell admin-blog-shell api-docs">
    <header class="admin-page-head"><div><span class="admin-kicker"><i></i> Integration reference</span><h1>Blog publishing API.</h1><p>Create, update, schedule and optimise articles from an external editorial workflow. No AI provider is connected.</p></div><a class="admin-secondary-button" href="{{ route('admin.blog.index') }}">Open content library <i class="bi bi-arrow-right"></i></a></header>

    <div class="api-docs-grid">
        <aside class="api-docs-nav"><strong>On this page</strong><a href="#setup">Setup</a><a href="#endpoints">Endpoints</a><a href="#payload">SEO payload</a><a href="#create-example">Create example</a><a href="#responses">Responses</a></aside>
        <div class="api-docs-content">
            <section id="setup"><span class="admin-panel-label">Authentication</span><h2>One private bearer token</h2><p>Generate a long random token, add it to the production <code>.env</code>, then clear Laravel’s cached configuration. Never expose this token in browser JavaScript.</p><pre><code>php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"</code></pre><pre><code>BLOG_API_TOKEN=your_64_character_secret

php artisan config:clear
php artisan config:cache</code></pre><p>Send it on every request:</p><pre><code>Authorization: Bearer YOUR_BLOG_API_TOKEN
Accept: application/json
Content-Type: application/json</code></pre></section>

            <section id="endpoints"><span class="admin-panel-label">Version 1</span><h2>Endpoints</h2><div class="api-endpoint-list"><div><b class="get">GET</b><code>/api/v1/blog/posts</code><span>List and filter</span></div><div><b class="post">POST</b><code>/api/v1/blog/posts</code><span>Create</span></div><div><b class="get">GET</b><code>/api/v1/blog/posts/{slug}</code><span>Retrieve</span></div><div><b class="put">PUT</b><code>/api/v1/blog/posts/{slug}</code><span>Update</span></div><div><b class="delete">DELETE</b><code>/api/v1/blog/posts/{slug}</code><span>Trash</span></div></div><p>List parameters: <code>status=draft|published</code>, <code>q=keyword</code> and <code>per_page=1..100</code>.</p></section>

            <section id="payload"><span class="admin-panel-label">Editorial schema</span><h2>Content and SEO fields</h2><div class="api-field-grid"><div><code>title</code><span>Required, max 180</span></div><div><code>content</code><span>Required Markdown</span></div><div><code>slug</code><span>Optional, generated from title</span></div><div><code>status</code><span>draft or published</span></div><div><code>published_at</code><span>ISO 8601 date/time</span></div><div><code>category</code><span>Editorial category</span></div><div><code>tags</code><span>Array or comma list</span></div><div><code>featured_image_url</code><span>Absolute or local path</span></div><div><code>featured_image_alt</code><span>Accessible description</span></div><div><code>meta_title</code><span>30–60 characters</span></div><div><code>meta_description</code><span>120–160 characters</span></div><div><code>focus_keyword</code><span>Primary search phrase</span></div><div><code>secondary_keywords</code><span>Array or comma list</span></div><div><code>canonical_url</code><span>Preferred absolute URL</span></div><div><code>og_title</code><span>Social preview title</span></div><div><code>og_description</code><span>Social preview copy</span></div><div><code>og_image_url</code><span>Social preview image</span></div><div><code>schema_type</code><span>BlogPosting, Article, TechArticle</span></div><div><code>robots_index</code><span>Boolean</span></div><div><code>robots_follow</code><span>Boolean</span></div></div></section>

            <section id="create-example"><span class="admin-panel-label">Example</span><h2>Create a fully optimised draft</h2><pre><code>curl -X POST "{{ url('/api/v1/blog/posts') }}" \
  -H "Authorization: Bearer YOUR_BLOG_API_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "A practical guide to reliable AI applications",
    "content": "## Start with the outcome\n\nYour Markdown article...",
    "excerpt": "A clear summary of what readers will learn and why it matters.",
    "category": "AI Strategy",
    "tags": ["AI applications", "software engineering"],
    "status": "draft",
    "meta_title": "Reliable AI Applications: A Practical Guide",
    "meta_description": "Learn how to design reliable AI applications with clear evaluation, observability, security and human approval points.",
    "focus_keyword": "reliable AI applications",
    "secondary_keywords": ["AI app development", "AI quality"],
    "featured_image_url": "/images/blog/reliable-ai-applications.webp",
    "featured_image_alt": "Modular AI application system connected by verified data paths",
    "robots_index": true,
    "robots_follow": true,
    "schema_type": "TechArticle"
  }'</code></pre></section>

            <section id="responses"><span class="admin-panel-label">Behaviour</span><h2>Responses and safety</h2><p>Successful create, retrieve and update requests return the complete article plus its calculated <code>seo.score</code>. Delete returns <code>204 No Content</code>. Validation errors return <code>422</code>, invalid tokens return <code>401</code>, and an unconfigured API returns <code>503</code>. Requests are limited to 60 per minute.</p><div class="api-note"><i class="bi bi-shield-lock"></i><div><strong>Production recommendation</strong><p>Rotate the token periodically, send requests only over HTTPS, keep unpublished articles as drafts and review them in the admin before publication.</p></div></div></section>
        </div>
    </div>
</div>
@endsection
