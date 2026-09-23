# WebIgnitors Blog Publishing API

This private API manages blog content and SEO metadata. It does not call an AI
provider. Use it from a trusted server-side editorial workflow only.

## Configure authentication

Generate a random production token:

~~~bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
~~~

Add it to .env, then rebuild Laravel's configuration cache:

~~~dotenv
BLOG_API_TOKEN=your_64_character_secret
~~~

~~~bash
php artisan config:clear
php artisan config:cache
~~~

Send one of these headers with every request:

~~~http
Authorization: Bearer YOUR_BLOG_API_TOKEN
~~~

or:

~~~http
X-Blog-Token: YOUR_BLOG_API_TOKEN
~~~

Never expose the token in browser JavaScript, a mobile application, analytics,
source control, or a public automation template.

## Endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| GET | /api/v1/blog/posts | Paginated list |
| POST | /api/v1/blog/posts | Create an article |
| GET | /api/v1/blog/posts/{slug} | Retrieve one article |
| PATCH or PUT | /api/v1/blog/posts/{slug} | Update fields |
| DELETE | /api/v1/blog/posts/{slug} | Soft-delete an article |

List queries support status=draft|published, q=search text, and
per_page=1..100. Requests are limited to 60 per minute per client.

## Create an article

~~~bash
curl -X POST "https://webignitors.in/api/v1/blog/posts" \
  -H "Authorization: Bearer YOUR_BLOG_API_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Reliable AI applications: a practical guide",
    "content": "## Start with the outcome\n\nLong-form Markdown content...",
    "excerpt": "A clear summary of what the reader will learn.",
    "category": "AI Engineering",
    "tags": ["AI applications", "Software engineering"],
    "featured_image_url": "/images/blog/reliable-ai-applications.jpg",
    "featured_image_alt": "A modular AI system connected through verified paths",
    "status": "draft",
    "meta_title": "Reliable AI Applications: Practical Guide",
    "meta_description": "Learn how to build reliable AI applications with evaluation, security, observability and meaningful human control.",
    "focus_keyword": "reliable AI applications",
    "secondary_keywords": ["AI app development", "AI quality"],
    "canonical_url": "https://webignitors.in/blog/reliable-ai-applications",
    "og_title": "Reliable AI Applications",
    "og_description": "A practical production guide for trustworthy AI software.",
    "og_image_url": "/images/blog/reliable-ai-applications.jpg",
    "robots_index": true,
    "robots_follow": true,
    "schema_type": "TechArticle"
  }'
~~~

Title and content are required on creation. Content accepts Markdown. Slug is
optional and generated from the title; duplicate slugs receive a numeric suffix.
Tags and secondary_keywords accept either JSON arrays or comma-separated
strings.

Publishing with no published_at value uses the current time. Sending
status=draft clears the publication date. A future ISO 8601 published_at value
schedules the article.

## SEO fields

- meta_title: maximum 70 characters; 30–60 is recommended.
- meta_description: maximum 180 characters; 120–160 is recommended.
- focus_keyword: one primary phrase used by the built-in SEO score.
- secondary_keywords: related phrases as an array or comma-separated value.
- canonical_url: absolute HTTP or HTTPS preferred URL.
- og_title, og_description, og_image_url: social-link preview data.
- robots_index, robots_follow: boolean search crawler directives.
- schema_type: BlogPosting, Article, or TechArticle.

Responses include a calculated seo.score. The current deterministic score checks
title, metadata length, keyword placement, content depth, headings, excerpt,
image alt text, canonical URL and social metadata. It does not depend on an
external AI service.

## Responses

- Success: JSON resource or paginated resource collection.
- Invalid token: 401.
- API token not configured: 503.
- Invalid article data: 422 with field-level errors.
- Deleted successfully: 204.

Use HTTPS, rotate the token periodically, review external drafts in the admin,
and keep publication authority inside a trusted workflow.
