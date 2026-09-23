<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogPost> */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(7, false);

        return [
            'author_id' => User::factory(),
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt' => fake()->paragraph(2),
            'content' => "## The opportunity\n\n".fake()->paragraphs(5, true)."\n\n## What to do next\n\n".fake()->paragraphs(4, true),
            'category' => fake()->randomElement(['Engineering', 'Growth', 'Automation', 'Strategy']),
            'tags' => fake()->randomElements(['Laravel', 'SEO', 'AI', 'Automation', 'Commerce'], 3),
            'status' => 'draft',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'BlogPosting',
            'seo_score' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }
}
