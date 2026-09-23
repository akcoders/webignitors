<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BlogPostWriter
{
    public function __construct(private readonly BlogSeoAnalyzer $seoAnalyzer) {}

    /** @param array<string, mixed> $data */
    public function save(BlogPost $post, array $data, ?int $authorId = null): BlogPost
    {
        $fields = [
            'title', 'excerpt', 'content', 'category', 'featured_image_url',
            'featured_image_alt', 'status', 'published_at', 'meta_title',
            'meta_description', 'focus_keyword', 'canonical_url', 'og_title',
            'og_description', 'og_image_url', 'robots_index', 'robots_follow',
            'schema_type',
        ];
        $attributes = Arr::only($data, $fields);

        if (array_key_exists('tags', $data)) {
            $attributes['tags'] = $this->cleanList($data['tags']);
        }
        if (array_key_exists('secondary_keywords', $data)) {
            $attributes['secondary_keywords'] = $this->cleanList($data['secondary_keywords']);
        }

        if (! $post->exists && $authorId) {
            $attributes['author_id'] = $authorId;
        }

        if (array_key_exists('title', $data) || array_key_exists('slug', $data)) {
            $attributes['slug'] = $this->uniqueSlug(
                (string) ($data['slug'] ?? $data['title'] ?? $post->title),
                $post->exists ? $post->id : null
            );
        }

        if (($attributes['status'] ?? $post->status) === 'published'
            && empty($attributes['published_at'])
            && $post->published_at === null) {
            $attributes['published_at'] = now();
        }

        if (($attributes['status'] ?? null) === 'draft') {
            $attributes['published_at'] = null;
        }

        $post->fill($attributes)->save();
        $post->forceFill([
            'seo_score' => $this->seoAnalyzer->analyze($post)['score'],
        ])->save();

        return $post->fresh('author');
    }

    /** @return array<int, string> */
    private function cleanList(mixed $values): array
    {
        if (is_string($values)) {
            $values = preg_split('/[,\n]+/', $values) ?: [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            is_array($values) ? $values : []
        ))));
    }

    private function uniqueSlug(string $value, ?int $ignoreId): string
    {
        $base = Str::slug($value) ?: 'article';
        $slug = $base;
        $counter = 2;

        while (BlogPost::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
