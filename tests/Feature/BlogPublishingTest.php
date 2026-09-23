<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Database\Seeders\BlogPostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_lists_only_currently_published_articles(): void
    {
        $published = BlogPost::factory()->published()->create([
            'title' => 'A visible production article',
            'slug' => 'visible-production-article',
        ]);
        BlogPost::factory()->create([
            'title' => 'A private draft',
            'slug' => 'private-draft',
        ]);
        BlogPost::factory()->published()->create([
            'title' => 'A future scheduled article',
            'slug' => 'future-scheduled-article',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('A private draft')
            ->assertDontSee('A future scheduled article');

        $this->get(route('blog.show', $published))
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee('application/ld+json', false);

        $this->get('/blog/private-draft')->assertNotFound();
        $this->get('/blog/future-scheduled-article')->assertNotFound();
    }

    public function test_sitemap_contains_published_articles_only(): void
    {
        $published = BlogPost::factory()->published()->create();
        $draft = BlogPost::factory()->create();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('blog.show', $published), false)
            ->assertDontSee($draft->slug);
    }

    public function test_twenty_complete_articles_are_seeded_with_matching_images_and_seo(): void
    {
        User::factory()->create(['is_admin' => true]);

        $this->seed(BlogPostSeeder::class);

        $this->assertDatabaseCount('blog_posts', 20);

        BlogPost::query()->each(function (BlogPost $post): void {
            $this->assertSame('published', $post->status);
            $this->assertNotEmpty($post->meta_title);
            $this->assertNotEmpty($post->meta_description);
            $this->assertNotEmpty($post->focus_keyword);
            $this->assertNotEmpty($post->secondary_keywords);
            $this->assertNotEmpty($post->canonical_url);
            $this->assertNotEmpty($post->featured_image_alt);
            $this->assertGreaterThanOrEqual(80, $post->seo_score, $post->title);
            $this->assertGreaterThanOrEqual(800, str_word_count(strip_tags($post->content_html)), $post->title);
            $this->assertFileExists(public_path(ltrim($post->featured_image_url, '/')));
            $this->assertFileExists(public_path(ltrim($post->thumbnail_url, '/')));
        });
    }
}
