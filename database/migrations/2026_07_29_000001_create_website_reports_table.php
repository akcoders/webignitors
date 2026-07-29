<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('requested_url');
            $table->text('final_url')->nullable();
            $table->string('domain');
            $table->string('website_title')->nullable();
            $table->string('status', 30)->default('queued')->index();
            $table->string('current_stage')->default('Waiting to begin');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->unsignedTinyInteger('page_limit')->default(1);
            $table->json('scores')->nullable();
            $table->json('summary')->nullable();
            $table->json('top_recommendations')->nullable();
            $table->json('tool_versions')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('data_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['domain', 'created_at']);
        });

        Schema::create('website_report_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_report_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->string('title')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->json('meta')->nullable();
            $table->json('scores')->nullable();
            $table->json('metrics')->nullable();
            $table->json('audit_data')->nullable();
            $table->string('mobile_screenshot_path')->nullable();
            $table->string('desktop_screenshot_path')->nullable();
            $table->timestamps();
        });

        Schema::create('website_report_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('website_report_page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 40)->index();
            $table->string('rule_key', 120);
            $table->string('severity', 20)->index();
            $table->string('title');
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->text('recommendation');
            $table->string('impact', 20)->default('medium');
            $table->string('effort', 20)->default('medium');
            $table->string('source', 60);
            $table->json('details')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['website_report_id', 'category']);
            $table->index(['website_report_id', 'severity']);
        });

        Schema::create('website_audit_api_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_report_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 60)->index();
            $table->string('operation', 80);
            $table->string('status', 20)->index();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('response_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_audit_api_runs');
        Schema::dropIfExists('website_report_findings');
        Schema::dropIfExists('website_report_pages');
        Schema::dropIfExists('website_reports');
    }
};
