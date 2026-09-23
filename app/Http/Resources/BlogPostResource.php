<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category' => $this->category,
            'tags' => $this->tags ?? [],
            'featured_image_url' => $this->featured_image_url,
            'thumbnail_url' => $this->thumbnail_url,
            'featured_image_alt' => $this->featured_image_alt,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'seo' => [
                'score' => $this->seo_score,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'focus_keyword' => $this->focus_keyword,
                'secondary_keywords' => $this->secondary_keywords ?? [],
                'canonical_url' => $this->canonical_url,
                'og_title' => $this->og_title,
                'og_description' => $this->og_description,
                'og_image_url' => $this->og_image_url,
                'robots_index' => $this->robots_index,
                'robots_follow' => $this->robots_follow,
                'schema_type' => $this->schema_type,
            ],
            'author' => $this->whenLoaded('author', fn (): ?array => $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ] : null),
            'public_url' => $this->isPubliclyVisible() ? route('blog.show', $this->resource) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
