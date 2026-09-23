<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_an_administrator_can_manage_articles(): void
    {
        $customer = User::factory()->create();

        $this->get(route('admin.blog.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($customer)->get(route('admin.blog.index'))->assertForbidden();
    }

    public function test_administrator_can_create_update_and_trash_an_article(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.blog.index'))->assertOk()->assertSee('Ideas, built to rank.');
        $this->actingAs($admin)->get(route('admin.blog.create'))->assertOk()->assertSee('Write a new article.');
        $this->actingAs($admin)->get(route('admin.blog.api-docs'))->assertOk()->assertSee('Blog publishing API.');

        $response = $this->actingAs($admin)->post(route('admin.blog.store'), [
            ...$this->payload(),
            'status' => 'published',
        ]);

        $post = BlogPost::query()->sole();
        $response->assertRedirect(route('admin.blog.edit', $post));
        $this->assertSame($admin->id, $post->author_id);
        $this->assertSame(['Laravel', 'AI'], $post->tags);
        $this->assertNotNull($post->published_at);
        $this->actingAs($admin)->get(route('admin.blog.edit', $post))->assertOk()->assertSee($post->title);

        $this->actingAs($admin)->put(route('admin.blog.update', $post), [
            ...$this->payload(),
            'title' => 'Updated reliable AI applications guide',
            'status' => 'draft',
        ])->assertRedirect();

        $post->refresh();
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);

        $this->actingAs($admin)
            ->delete(route('admin.blog.destroy', $post))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertSoftDeleted($post);
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'title' => 'Reliable AI applications guide',
            'slug' => 'reliable-ai-applications-guide',
            'excerpt' => 'A practical and detailed guide to designing AI applications that remain reliable, secure and useful in real business workflows.',
            'content' => "## Reliable AI applications\n\n".str_repeat('Reliable AI applications need tests, controls, observable workflows, safe fallbacks and accountable owners. ', 100),
            'category' => 'AI Engineering',
            'tags' => 'Laravel, AI',
            'featured_image_url' => '/images/blog/agentic-ai-architecture-business-systems.jpg',
            'featured_image_alt' => 'Modular AI application connected to safe business tools',
            'meta_title' => 'Reliable AI Applications: Practical Guide',
            'meta_description' => 'Build reliable AI applications with practical evaluation, security, observability and human controls for real production business workflows.',
            'focus_keyword' => 'reliable AI applications',
            'secondary_keywords' => 'AI quality, AI testing',
            'canonical_url' => 'https://webignitors.in/blog/reliable-ai-applications-guide',
            'og_title' => 'Reliable AI Applications',
            'og_description' => 'A practical production guide for useful, secure and measurable AI applications.',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'TechArticle',
        ];
    }
}
