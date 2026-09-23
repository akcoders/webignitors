<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'tags',
        'featured_image_url',
        'featured_image_alt',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'focus_keyword',
        'secondary_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_url',
        'robots_index',
        'robots_follow',
        'schema_type',
        'seo_score',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'secondary_keywords' => 'array',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    protected function contentHtml(): Attribute
    {
        return Attribute::get(fn (): string => Str::markdown($this->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }

    protected function readingTime(): Attribute
    {
        return Attribute::get(function (): int {
            $words = str_word_count(strip_tags($this->content_html));

            return max(1, (int) ceil($words / 220));
        });
    }

    protected function seoTitle(): Attribute
    {
        return Attribute::get(fn (): string => $this->meta_title ?: $this->title);
    }

    protected function seoDescription(): Attribute
    {
        return Attribute::get(fn (): string => $this->meta_description
            ?: (string) Str::of($this->excerpt ?: strip_tags($this->content_html))->squish()->limit(160, ''));
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->featured_image_url || ! str_starts_with($this->featured_image_url, '/images/blog/')) {
                return null;
            }

            $extensionPosition = strrpos($this->featured_image_url, '.');

            return $extensionPosition === false
                ? $this->featured_image_url
                : substr($this->featured_image_url, 0, $extensionPosition).'-thumb'.substr($this->featured_image_url, $extensionPosition);
        });
    }
}
