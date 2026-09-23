<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogSeoAnalyzer
{
    /**
     * @param  BlogPost|array<string, mixed>  $source
     * @return array{score: int, checks: array<int, array{label: string, passed: bool, advice: string}>}
     */
    public function analyze(BlogPost|array $source): array
    {
        $data = $source instanceof BlogPost ? $source->toArray() : $source;
        $title = trim((string) ($data['title'] ?? ''));
        $slug = trim((string) ($data['slug'] ?? Str::slug($title)));
        $excerpt = trim((string) ($data['excerpt'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $metaTitle = trim((string) ($data['meta_title'] ?? ''));
        $metaDescription = trim((string) ($data['meta_description'] ?? ''));
        $keyword = Str::lower(trim((string) ($data['focus_keyword'] ?? '')));
        $plainContent = Str::lower(strip_tags(Str::markdown($content, ['html_input' => 'strip'])));
        $wordCount = str_word_count($plainContent);

        $rules = [
            [$title !== '', 8, 'Article title', 'Add a clear, specific article title.'],
            [mb_strlen($metaTitle) >= 30 && mb_strlen($metaTitle) <= 60, 10, 'Meta title length', 'Keep the meta title between 30 and 60 characters.'],
            [mb_strlen($metaDescription) >= 120 && mb_strlen($metaDescription) <= 160, 12, 'Meta description length', 'Use a persuasive meta description between 120 and 160 characters.'],
            [$keyword !== '', 8, 'Focus keyword', 'Choose one primary search phrase.'],
            [$keyword !== '' && str_contains(Str::lower($title), $keyword), 8, 'Keyword in title', 'Use the focus keyword naturally in the title.'],
            [$keyword !== '' && str_contains(Str::lower($slug), Str::slug($keyword)), 7, 'Keyword in URL', 'Include the focus keyword in the slug.'],
            [$keyword !== '' && str_contains(Str::lower($metaDescription), $keyword), 7, 'Keyword in description', 'Use the focus keyword once in the meta description.'],
            [$keyword !== '' && str_contains($plainContent, $keyword), 8, 'Keyword in content', 'Use the focus keyword naturally in the article.'],
            [$wordCount >= 800, 12, 'Content depth', 'Aim for at least 800 useful words for a detailed article.'],
            [(bool) preg_match('/^#{2,3}\s+.+/m', $content), 6, 'Useful headings', 'Break the article into descriptive H2/H3 sections.'],
            [mb_strlen($excerpt) >= 80 && mb_strlen($excerpt) <= 320, 5, 'Article excerpt', 'Write an 80–320 character summary.'],
            [empty($data['featured_image_url']) || filled($data['featured_image_alt'] ?? null), 4, 'Image alternative text', 'Describe the featured image for accessibility and image search.'],
            [filled($data['canonical_url'] ?? null), 3, 'Canonical URL', 'Set the preferred canonical article URL.'],
            [filled($data['og_title'] ?? null) && filled($data['og_description'] ?? null), 2, 'Social metadata', 'Add an Open Graph title and description.'],
        ];

        $score = 0;
        $checks = [];
        foreach ($rules as [$passed, $weight, $label, $advice]) {
            if ($passed) {
                $score += $weight;
            }
            $checks[] = compact('label', 'passed', 'advice');
        }

        return ['score' => min(100, $score), 'checks' => $checks];
    }
}
