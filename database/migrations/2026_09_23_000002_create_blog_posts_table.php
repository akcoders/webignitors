<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 180);
            $table->string('slug', 190)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('category', 80)->nullable()->index();
            $table->json('tags')->nullable();
            $table->string('featured_image_url', 2048)->nullable();
            $table->string('featured_image_alt', 180)->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 180)->nullable();
            $table->string('focus_keyword', 120)->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->string('og_title', 100)->nullable();
            $table->string('og_description', 220)->nullable();
            $table->string('og_image_url', 2048)->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('schema_type', 40)->default('BlogPosting');
            $table->unsignedTinyInteger('seo_score')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['author_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
