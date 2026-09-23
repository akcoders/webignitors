@if($errors->any())
    <div class="blog-editor-errors"><strong>Please correct the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="blog-editor-grid">
    <div class="blog-editor-main">
        <section class="blog-editor-panel">
            <div class="blog-editor-panel-head"><span>01</span><div><h2>The story</h2><p>Useful, specific and easy to scan.</p></div></div>
            <div class="editor-field">
                <label for="title">Article title <em>*</em><small><span data-count-for="title">0</span>/180</small></label>
                <input id="title" name="title" type="text" maxlength="180" value="{{ old('title', $post->title) }}" required data-count-input data-slug-source>
                @error('title')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="editor-field">
                <label for="slug">URL slug <small>Auto-generated if blank</small></label>
                <div class="slug-input"><span>/blog/</span><input id="slug" name="slug" type="text" maxlength="190" value="{{ old('slug', $post->slug) }}" data-slug-target></div>
                @error('slug')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="editor-field">
                <label for="excerpt">Short summary <small><span data-count-for="excerpt">0</span>/320 recommended</small></label>
                <textarea id="excerpt" name="excerpt" rows="4" maxlength="1000" data-count-input>{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div class="editor-field">
                <label for="content">Article content in Markdown <em>*</em><small><span data-word-count>0</span> words · 800+ recommended</small></label>
                <textarea id="content" name="content" rows="28" required data-markdown-content>{{ old('content', $post->content) }}</textarea>
                <p class="field-help">Use <code>##</code> for section headings, <code>###</code> for subsections, <code>-</code> for lists and <code>**text**</code> for emphasis.</p>
            </div>
        </section>

        <section class="blog-editor-panel">
            <div class="blog-editor-panel-head"><span>02</span><div><h2>Search appearance</h2><p>Control the message shown in search results.</p></div></div>
            <div class="seo-preview"><span>{{ parse_url(config('app.url'), PHP_URL_HOST) }} › blog › <b data-seo-slug>{{ $post->slug ?: 'article-url' }}</b></span><h3 data-seo-title>{{ old('meta_title', $post->meta_title) ?: old('title', $post->title) ?: 'Your article title' }}</h3><p data-seo-description>{{ old('meta_description', $post->meta_description) ?: old('excerpt', $post->excerpt) ?: 'Your meta description will appear here. Make the value of the article clear before someone clicks.' }}</p></div>
            <div class="editor-two-columns">
                <div class="editor-field"><label for="meta_title">Meta title <small><span data-count-for="meta_title">0</span>/60</small></label><input id="meta_title" name="meta_title" type="text" maxlength="70" value="{{ old('meta_title', $post->meta_title) }}" data-count-input></div>
                <div class="editor-field"><label for="focus_keyword">Focus keyword</label><input id="focus_keyword" name="focus_keyword" type="text" maxlength="120" value="{{ old('focus_keyword', $post->focus_keyword) }}"></div>
            </div>
            <div class="editor-field"><label for="meta_description">Meta description <small><span data-count-for="meta_description">0</span>/160</small></label><textarea id="meta_description" name="meta_description" rows="3" maxlength="180" data-count-input>{{ old('meta_description', $post->meta_description) }}</textarea></div>
            <div class="editor-two-columns">
                <div class="editor-field"><label for="secondary_keywords">Related keywords <small>Comma-separated</small></label><input id="secondary_keywords" name="secondary_keywords" type="text" value="{{ old('secondary_keywords', implode(', ', $post->secondary_keywords ?? [])) }}"></div>
                <div class="editor-field"><label for="canonical_url">Canonical URL</label><input id="canonical_url" name="canonical_url" type="url" value="{{ old('canonical_url', $post->canonical_url) }}" placeholder="{{ url('/blog/article-url') }}"></div>
            </div>
        </section>

        <section class="blog-editor-panel">
            <div class="blog-editor-panel-head"><span>03</span><div><h2>Social & structured data</h2><p>Fine-tune link previews and machine-readable context.</p></div></div>
            <div class="editor-field"><label for="og_title">Social title</label><input id="og_title" name="og_title" type="text" maxlength="100" value="{{ old('og_title', $post->og_title) }}"></div>
            <div class="editor-field"><label for="og_description">Social description</label><textarea id="og_description" name="og_description" rows="3" maxlength="220">{{ old('og_description', $post->og_description) }}</textarea></div>
            <div class="editor-two-columns">
                <div class="editor-field"><label for="og_image_url">Social image URL</label><input id="og_image_url" name="og_image_url" type="text" value="{{ old('og_image_url', $post->og_image_url) }}"></div>
                <div class="editor-field"><label for="schema_type">Schema type</label><select id="schema_type" name="schema_type">@foreach(['BlogPosting', 'Article', 'TechArticle'] as $schema)<option value="{{ $schema }}" @selected(old('schema_type', $post->schema_type ?: 'BlogPosting') === $schema)>{{ $schema }}</option>@endforeach</select></div>
            </div>
            <div class="editor-checks"><label><input type="checkbox" name="robots_index" value="1" @checked(old('robots_index', $post->robots_index ?? true))><span><strong>Allow indexing</strong><small>Let search engines include this page</small></span></label><label><input type="checkbox" name="robots_follow" value="1" @checked(old('robots_follow', $post->robots_follow ?? true))><span><strong>Allow link following</strong><small>Let crawlers follow article links</small></span></label></div>
        </section>
    </div>

    <aside class="blog-editor-sidebar">
        <section class="editor-publish-card">
            <div class="editor-publish-head"><span>Publish controls</span><i class="bi bi-lightning-charge"></i></div>
            <div class="editor-field"><label for="status">Status</label><select id="status" name="status"><option value="draft" @selected(old('status', $post->status ?: 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $post->status) === 'published')>Published</option></select></div>
            <div class="editor-field"><label for="published_at">Publish date & time</label><input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\\TH:i')) }}"></div>
            <button class="editor-save-button" type="submit"><i class="bi bi-check2-circle"></i> {{ $post->exists ? 'Save changes' : 'Create article' }}</button>
            <p>Publishing immediately uses the current time when no date is provided.</p>
        </section>

        <section class="editor-seo-card">
            <div class="seo-score-ring" style="--score: {{ $seo['score'] }}"><strong>{{ $seo['score'] }}</strong><span>SEO score</span></div>
            <div><h2>{{ $seo['score'] >= 80 ? 'Ready to compete' : ($seo['score'] >= 55 ? 'Good foundation' : 'Needs attention') }}</h2><p>The score recalculates after saving.</p></div>
            <ul>@foreach($seo['checks'] as $check)<li class="{{ $check['passed'] ? 'passed' : '' }}"><i class="bi {{ $check['passed'] ? 'bi-check-circle-fill' : 'bi-circle' }}"></i><span><strong>{{ $check['label'] }}</strong>@unless($check['passed'])<small>{{ $check['advice'] }}</small>@endunless</span></li>@endforeach</ul>
        </section>

        <section class="blog-editor-panel compact">
            <h2>Organisation & image</h2>
            <div class="editor-field"><label for="category">Category</label><input id="category" name="category" type="text" maxlength="80" value="{{ old('category', $post->category) }}"></div>
            <div class="editor-field"><label for="tags">Tags <small>Comma-separated</small></label><input id="tags" name="tags" type="text" value="{{ old('tags', implode(', ', $post->tags ?? [])) }}"></div>
            <div class="editor-field"><label for="featured_image_url">Featured image URL</label><input id="featured_image_url" name="featured_image_url" type="text" value="{{ old('featured_image_url', $post->featured_image_url) }}"></div>
            <div class="editor-field"><label for="featured_image_alt">Image alternative text</label><textarea id="featured_image_alt" name="featured_image_alt" rows="3" maxlength="180">{{ old('featured_image_alt', $post->featured_image_alt) }}</textarea></div>
        </section>
    </aside>
</div>
