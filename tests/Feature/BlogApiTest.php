<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.blog_api.token' => 'test-editorial-token']);
    }

    public function test_api_rejects_missing_or_invalid_tokens(): void
    {
        $this->getJson('/api/v1/blog/posts')->assertUnauthorized();
        $this->withToken('incorrect')->getJson('/api/v1/blog/posts')->assertUnauthorized();
    }

    public function test_api_can_create_read_update_list_and_delete_an_article(): void
    {
        $created = $this->withToken('test-editorial-token')
            ->postJson('/api/v1/blog/posts', $this->payload())
            ->assertOk()
            ->assertJsonPath('data.slug', 'api-managed-ai-article')
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonStructure(['data' => ['seo' => ['score', 'meta_title', 'focus_keyword']]]);

        $slug = $created->json('data.slug');

        $this->withToken('test-editorial-token')
            ->getJson('/api/v1/blog/posts?status=draft&q=managed')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withToken('test-editorial-token')
            ->getJson('/api/v1/blog/posts/'.$slug)
            ->assertOk()
            ->assertJsonPath('data.title', 'API managed AI article');

        $this->withToken('test-editorial-token')
            ->patchJson('/api/v1/blog/posts/'.$slug, [
                'status' => 'published',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'published');

        $this->assertNotNull(BlogPost::query()->sole()->published_at);

        $this->withToken('test-editorial-token')
            ->deleteJson('/api/v1/blog/posts/'.$slug)
            ->assertNoContent();
        $this->assertSoftDeleted('blog_posts', ['slug' => $slug]);
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'title' => 'API managed AI article',
            'slug' => 'api-managed-ai-article',
            'excerpt' => 'A detailed editorial workflow demonstration for securely managing practical software and AI articles through an API.',
            'content' => "## API managed AI article\n\n".str_repeat('An API managed AI article needs secure authentication, useful content, complete SEO data and a deliberate review workflow. ', 100),
            'category' => 'Software Engineering',
            'tags' => ['API', 'AI'],
            'status' => 'draft',
            'meta_title' => 'API Managed AI Article Workflow',
            'meta_description' => 'Use an API managed AI article workflow to publish useful software content with secure access, complete metadata and consistent editorial review.',
            'focus_keyword' => 'API managed AI article',
            'secondary_keywords' => ['publishing API', 'blog automation'],
            'canonical_url' => 'https://webignitors.in/blog/api-managed-ai-article',
            'og_title' => 'API Managed AI Article',
            'og_description' => 'A secure and practical API publishing workflow for software articles.',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'TechArticle',
        ];
    }
}
