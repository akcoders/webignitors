<?php

namespace App\Services\WebsiteAudit;

use DOMDocument;
use DOMElement;
use DOMXPath;

class WebsiteAnalyzer
{
    /**
     * @param  array<string, mixed>  $headers
     * @return array<string, mixed>
     */
    public function analyze(string $html, string $url, array $headers): array
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($document);
        $title = trim($xpath->evaluate('string(//title[1])'));
        $description = $this->meta($xpath, 'name', 'description');
        $viewport = $this->meta($xpath, 'name', 'viewport');
        $robots = strtolower($this->meta($xpath, 'name', 'robots'));
        $canonical = trim($xpath->evaluate('string(//link[contains(concat(" ", translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), " "), " canonical ")]/@href)'));
        $language = trim($document->documentElement?->getAttribute('lang') ?? '');

        $headings = [];
        foreach (range(1, 6) as $level) {
            $headings[$level] = $xpath->query("//h{$level}")?->length ?? 0;
        }

        $images = $xpath->query('//img');
        $imageCount = $images?->length ?? 0;
        $missingAlt = 0;
        $missingDimensions = 0;
        foreach ($images ?: [] as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }
            if (! $image->hasAttribute('alt')) {
                $missingAlt++;
            }
            if (! $image->hasAttribute('width') || ! $image->hasAttribute('height')) {
                $missingDimensions++;
            }
        }

        $links = $xpath->query('//a[@href]');
        $internalLinks = 0;
        $externalLinks = 0;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        foreach ($links ?: [] as $link) {
            $href = trim($link->getAttribute('href'));
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
                continue;
            }
            $linkHost = strtolower((string) parse_url($href, PHP_URL_HOST));
            if ($linkHost === '' || $linkHost === $host) {
                $internalLinks++;
            } else {
                $externalLinks++;
            }
        }

        $scripts = $xpath->query('//script');
        $scriptCount = $scripts?->length ?? 0;
        $externalScripts = 0;
        $inlineScriptBytes = 0;
        foreach ($scripts ?: [] as $script) {
            if ($script->hasAttribute('src')) {
                $externalScripts++;
            } else {
                $inlineScriptBytes += strlen($script->textContent);
            }
        }

        $stylesheets = $xpath->query('//link[contains(translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "stylesheet")]')?->length ?? 0;
        $inlineStyleBytes = 0;
        foreach ($xpath->query('//style') ?: [] as $style) {
            $inlineStyleBytes += strlen($style->textContent);
        }

        $formInputs = $xpath->query('//input[not(@type="hidden") and not(@type="submit") and not(@type="button")]|//select|//textarea');
        $unlabelledInputs = 0;
        foreach ($formInputs ?: [] as $input) {
            if (! $input instanceof DOMElement || $this->hasAccessibleLabel($xpath, $input)) {
                continue;
            }
            $unlabelledInputs++;
        }

        $structuredData = 0;
        $invalidStructuredData = 0;
        foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $node) {
            $structuredData++;
            json_decode(trim($node->textContent), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $invalidStructuredData++;
            }
        }

        $plainText = $this->visibleText($document);
        $lowerText = strtolower($plainText);
        $wordCount = str_word_count($plainText);
        $ctaCount = $this->countCallsToAction($xpath);
        $hasContact = (bool) preg_match('/mailto:|tel:/i', $html)
            || str_contains($lowerText, 'contact us')
            || str_contains($lowerText, 'get in touch');
        $hasTrust = (bool) preg_match('/testimonial|case stud|review|trusted by|our clients|success stor/i', $plainText);
        $hasAnalytics = (bool) preg_match('/googletagmanager|gtag\\(|google-analytics|plausible\\.io|clarity\\.ms|matomo|fbq\\(/i', $html);
        $openGraphCount = $xpath->query('//meta[starts-with(translate(@property, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "og:")]')?->length ?? 0;
        $domNodes = $xpath->query('//*')?->length ?? 0;

        $securityHeaders = [
            'strict-transport-security' => $this->header($headers, 'strict-transport-security'),
            'content-security-policy' => $this->header($headers, 'content-security-policy'),
            'x-content-type-options' => $this->header($headers, 'x-content-type-options'),
            'referrer-policy' => $this->header($headers, 'referrer-policy'),
            'permissions-policy' => $this->header($headers, 'permissions-policy'),
            'x-frame-options' => $this->header($headers, 'x-frame-options'),
        ];

        $meta = [
            'title' => $title,
            'title_length' => mb_strlen($title),
            'description' => $description,
            'description_length' => mb_strlen($description),
            'canonical' => $canonical,
            'robots' => $robots,
            'language' => $language,
            'viewport' => $viewport,
            'headings' => $headings,
            'images' => $imageCount,
            'images_missing_alt' => $missingAlt,
            'images_missing_dimensions' => $missingDimensions,
            'links' => $links?->length ?? 0,
            'internal_links' => $internalLinks,
            'external_links' => $externalLinks,
            'scripts' => $scriptCount,
            'external_scripts' => $externalScripts,
            'inline_script_bytes' => $inlineScriptBytes,
            'stylesheets' => $stylesheets,
            'inline_style_bytes' => $inlineStyleBytes,
            'dom_nodes' => $domNodes,
            'word_count' => $wordCount,
            'forms' => $xpath->query('//form')?->length ?? 0,
            'unlabelled_form_controls' => $unlabelledInputs,
            'structured_data_blocks' => $structuredData,
            'invalid_structured_data_blocks' => $invalidStructuredData,
            'open_graph_tags' => $openGraphCount,
            'calls_to_action' => $ctaCount,
            'has_contact_path' => $hasContact,
            'has_trust_signals' => $hasTrust,
            'has_analytics' => $hasAnalytics,
            'security_headers' => $securityHeaders,
        ];

        $findings = $this->buildFindings($meta, $url);
        $scores = $this->calculateScores($meta);

        return compact('meta', 'findings', 'scores');
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<int, array<string, mixed>>
     */
    private function buildFindings(array $meta, string $url): array
    {
        $findings = [];

        if ($meta['title'] === '') {
            $findings[] = $this->finding('seo', 'missing-title', 'high', 'The page has no title tag', 'Search engines and browser tabs do not have a clear name for this page.', 'No <title> element was detected.', 'Add a unique, descriptive title of approximately 30–60 characters.', 'high', 'small');
        } elseif ($meta['title_length'] < 30 || $meta['title_length'] > 60) {
            $findings[] = $this->finding('seo', 'title-length', 'medium', 'The page title could be more search-friendly', 'Very short or long titles can weaken relevance or be truncated in search results.', "{$meta['title_length']} characters detected.", 'Rewrite the title to communicate the page topic and primary value in roughly 30–60 characters.', 'medium', 'small');
        }

        if ($meta['description'] === '') {
            $findings[] = $this->finding('seo', 'missing-description', 'high', 'The meta description is missing', 'The page cannot propose its own persuasive summary for search results.', 'No meta description was detected.', 'Write a unique, benefit-led summary of approximately 120–160 characters.', 'high', 'small');
        } elseif ($meta['description_length'] < 70 || $meta['description_length'] > 170) {
            $findings[] = $this->finding('seo', 'description-length', 'medium', 'The meta description needs refinement', 'Descriptions outside a useful length range can be uninformative or truncated.', "{$meta['description_length']} characters detected.", 'Rewrite the description around the search intent, differentiator and next action.', 'medium', 'small');
        }

        if (($meta['headings'][1] ?? 0) !== 1) {
            $count = $meta['headings'][1] ?? 0;
            $findings[] = $this->finding('seo', 'h1-count', $count === 0 ? 'high' : 'medium', 'The page needs one clear H1', 'A single primary heading helps people and search engines understand the page hierarchy.', "{$count} H1 headings detected.", 'Use one descriptive H1 and organise supporting sections with H2 and H3 headings.', 'high', 'small');
        }

        if ($meta['canonical'] === '') {
            $findings[] = $this->finding('seo', 'missing-canonical', 'medium', 'Canonical URL is not declared', 'A canonical tag helps consolidate duplicate or parameterised versions of a page.', 'No canonical link was detected.', "Add a self-referencing canonical URL for {$url}.", 'medium', 'small');
        }

        if (str_contains($meta['robots'], 'noindex')) {
            $findings[] = $this->finding('seo', 'noindex', 'critical', 'The page asks search engines not to index it', 'A noindex directive prevents this page from appearing in organic search results.', "Robots directive: {$meta['robots']}.", 'Remove noindex if this page is intended to attract search traffic.', 'high', 'small');
        }

        if ($meta['language'] === '') {
            $findings[] = $this->finding('accessibility', 'missing-language', 'medium', 'Document language is not declared', 'Assistive technologies need the page language to pronounce and interpret content correctly.', 'The <html> element has no lang attribute.', 'Set a valid language such as lang="en" on the HTML element.', 'medium', 'small');
        }

        if ($meta['images_missing_alt'] > 0) {
            $findings[] = $this->finding('accessibility', 'missing-image-alt', 'high', 'Images are missing alternative text', 'Visitors using screen readers may miss important image meaning.', "{$meta['images_missing_alt']} of {$meta['images']} images have no alt attribute.", 'Add meaningful alt text to informative images and empty alt attributes to decorative images.', 'high', 'medium');
        }

        if ($meta['unlabelled_form_controls'] > 0) {
            $findings[] = $this->finding('accessibility', 'unlabelled-controls', 'high', 'Form fields do not have accessible labels', 'Unlabelled inputs are difficult to understand for screen-reader and voice-control users.', "{$meta['unlabelled_form_controls']} form controls appear unlabelled.", 'Connect visible labels with each field or provide an accurate accessible name.', 'high', 'medium');
        }

        if ($meta['viewport'] === '') {
            $findings[] = $this->finding('design', 'missing-viewport', 'critical', 'The mobile viewport is not configured', 'Without a viewport declaration the layout may render as a scaled-down desktop page on phones.', 'No viewport meta tag was detected.', 'Add width=device-width and initial-scale=1 to the viewport declaration.', 'high', 'small');
        }

        if ($meta['images_missing_dimensions'] > 0) {
            $findings[] = $this->finding('design', 'image-dimensions', 'medium', 'Images may cause layout movement', 'Images without intrinsic dimensions can push content after loading and make the page feel unstable.', "{$meta['images_missing_dimensions']} images do not declare both width and height.", 'Provide intrinsic dimensions or aspect-ratio for every content image.', 'medium', 'small');
        }

        if ($meta['dom_nodes'] > 1500) {
            $findings[] = $this->finding('code', 'large-dom', 'medium', 'The page has a very large DOM', 'Large document trees increase style calculation, layout and memory cost.', number_format($meta['dom_nodes']).' HTML elements detected.', 'Simplify deeply nested components, repeated markup and hidden interface states.', 'medium', 'large');
        }

        if ($meta['scripts'] > 15) {
            $findings[] = $this->finding('code', 'script-count', 'medium', 'The page loads many scripts', 'Every script can add network, parsing and main-thread work, especially on mobile devices.', "{$meta['scripts']} script elements detected, including {$meta['external_scripts']} external scripts.", 'Remove duplicate libraries, delay non-essential scripts and review third-party tags.', 'high', 'medium');
        }

        if ($meta['stylesheets'] > 8) {
            $findings[] = $this->finding('code', 'stylesheet-count', 'medium', 'Styles are split across many files', 'Multiple render-blocking stylesheets can delay the first useful paint.', "{$meta['stylesheets']} external stylesheets detected.", 'Bundle critical styles sensibly and load non-critical styles without blocking rendering.', 'medium', 'medium');
        }

        if ($meta['calls_to_action'] === 0) {
            $findings[] = $this->finding('marketing', 'missing-cta', 'high', 'The next action is not obvious', 'Visitors are more likely to leave when the page does not present a clear next step.', 'No recognisable action-oriented link or button was detected.', 'Add a prominent primary CTA aligned with the page intent and repeat it at natural decision points.', 'high', 'small');
        }

        if (! $meta['has_contact_path']) {
            $findings[] = $this->finding('marketing', 'contact-path', 'medium', 'A direct contact path is difficult to find', 'Qualified visitors need a low-friction way to continue the conversation.', 'No obvious telephone, email or contact action was detected.', 'Add a visible contact route in the navigation, page content and footer.', 'high', 'small');
        }

        if (! $meta['has_trust_signals']) {
            $findings[] = $this->finding('marketing', 'trust-signals', 'medium', 'The page needs stronger proof', 'Claims become more persuasive when supported by outcomes, clients, reviews or case studies.', 'No common trust or proof language was detected.', 'Add credible testimonials, measurable outcomes, case studies, certifications or client evidence.', 'high', 'medium');
        }

        if (! $meta['has_analytics']) {
            $findings[] = $this->finding('marketing', 'analytics', 'medium', 'No common analytics implementation was detected', 'Without measurement it is difficult to understand acquisition, engagement and conversion.', 'Common analytics tags were not found in the page HTML.', 'Install a privacy-conscious analytics platform and define events for meaningful business actions.', 'high', 'medium');
        }

        if ($meta['open_graph_tags'] < 3) {
            $findings[] = $this->finding('marketing', 'open-graph', 'low', 'Social sharing metadata is incomplete', 'Shared links may appear without a persuasive title, description or image.', "{$meta['open_graph_tags']} Open Graph tags detected.", 'Add og:title, og:description, og:image, og:url and appropriate Twitter card metadata.', 'medium', 'small');
        }

        if ($meta['structured_data_blocks'] === 0) {
            $findings[] = $this->finding('seo', 'structured-data', 'low', 'No structured data was detected', 'Relevant schema can help search engines understand the organisation, content and available rich-result features.', 'No JSON-LD blocks were detected.', 'Add valid schema appropriate to the page, such as Organization, Service, Product, Article or BreadcrumbList.', 'medium', 'medium');
        } elseif ($meta['invalid_structured_data_blocks'] > 0) {
            $findings[] = $this->finding('seo', 'invalid-structured-data', 'high', 'Structured data contains invalid JSON', 'Invalid markup cannot be interpreted reliably by search engines.', "{$meta['invalid_structured_data_blocks']} JSON-LD blocks could not be parsed.", 'Correct the JSON syntax and validate the structured data before deployment.', 'high', 'small');
        }

        foreach ($meta['security_headers'] as $header => $value) {
            if ($value !== '') {
                continue;
            }

            $highImpact = in_array($header, ['strict-transport-security', 'content-security-policy'], true);
            $findings[] = $this->finding(
                'security',
                'header-'.$header,
                $highImpact ? 'high' : 'medium',
                'Security header missing: '.$header,
                'Modern response headers reduce exposure to browser-based attacks and data leakage.',
                'The header was not present in the audited response.',
                'Configure the header at the web server or application level and test it before enforcing a strict policy.',
                $highImpact ? 'high' : 'medium',
                'medium'
            );
        }

        return $findings;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, int>
     */
    private function calculateScores(array $meta): array
    {
        $seo = 100;
        $seo -= $meta['title'] === '' ? 20 : (($meta['title_length'] < 30 || $meta['title_length'] > 60) ? 8 : 0);
        $seo -= $meta['description'] === '' ? 18 : (($meta['description_length'] < 70 || $meta['description_length'] > 170) ? 7 : 0);
        $seo -= ($meta['headings'][1] ?? 0) !== 1 ? 15 : 0;
        $seo -= $meta['canonical'] === '' ? 8 : 0;
        $seo -= $meta['structured_data_blocks'] === 0 ? 5 : 0;

        $design = 100;
        $design -= $meta['viewport'] === '' ? 35 : 0;
        $design -= ($meta['headings'][1] ?? 0) !== 1 ? 12 : 0;
        $design -= min(18, $meta['images_missing_dimensions'] * 3);
        $design -= min(20, $meta['unlabelled_form_controls'] * 5);
        $design -= $meta['dom_nodes'] > 1500 ? 10 : 0;

        $marketing = 100;
        $marketing -= $meta['calls_to_action'] === 0 ? 25 : 0;
        $marketing -= ! $meta['has_contact_path'] ? 18 : 0;
        $marketing -= ! $meta['has_trust_signals'] ? 18 : 0;
        $marketing -= ! $meta['has_analytics'] ? 12 : 0;
        $marketing -= $meta['open_graph_tags'] < 3 ? 8 : 0;
        $marketing -= $meta['word_count'] < 180 ? 10 : 0;

        $code = 100;
        $code -= $meta['scripts'] > 15 ? min(25, ($meta['scripts'] - 15) * 2) : 0;
        $code -= $meta['stylesheets'] > 8 ? min(15, ($meta['stylesheets'] - 8) * 3) : 0;
        $code -= $meta['dom_nodes'] > 1500 ? 20 : 0;
        $code -= $meta['inline_script_bytes'] > 75_000 ? 15 : 0;
        $code -= $meta['inline_style_bytes'] > 75_000 ? 10 : 0;

        $security = 100 - (count(array_filter($meta['security_headers'], fn ($value) => $value === '')) * 12);

        return [
            'seo' => max(0, $seo),
            'design' => max(0, $design),
            'marketing' => max(0, $marketing),
            'code' => max(0, $code),
            'security' => max(0, $security),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function finding(
        string $category,
        string $rule,
        string $severity,
        string $title,
        string $description,
        ?string $evidence,
        string $recommendation,
        string $impact,
        string $effort
    ): array {
        return [
            'category' => $category,
            'rule_key' => 'webignitors-'.$rule,
            'severity' => $severity,
            'title' => $title,
            'description' => $description,
            'evidence' => $evidence,
            'recommendation' => $recommendation,
            'impact' => $impact,
            'effort' => $effort,
            'source' => 'WebIgnitors analyser',
            'details' => null,
        ];
    }

    private function meta(DOMXPath $xpath, string $attribute, string $value): string
    {
        $query = sprintf(
            '//meta[translate(@%s, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="%s"]/@content',
            $attribute,
            strtolower($value)
        );

        return trim($xpath->evaluate("string({$query})"));
    }

    private function header(array $headers, string $name): string
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) !== strtolower($name)) {
                continue;
            }

            return trim(is_array($value) ? implode(', ', $value) : (string) $value);
        }

        return '';
    }

    private function hasAccessibleLabel(DOMXPath $xpath, DOMElement $input): bool
    {
        if ($input->hasAttribute('aria-label') || $input->hasAttribute('aria-labelledby') || $input->hasAttribute('title')) {
            return true;
        }

        $id = $input->getAttribute('id');
        if ($id !== '' && ($xpath->query('//label[@for='.$this->xpathLiteral($id).']')?->length ?? 0) > 0) {
            return true;
        }

        for ($parent = $input->parentNode; $parent; $parent = $parent->parentNode) {
            if ($parent instanceof DOMElement && strtolower($parent->tagName) === 'label') {
                return true;
            }
            if ($parent instanceof DOMElement && strtolower($parent->tagName) === 'form') {
                break;
            }
        }

        return false;
    }

    private function xpathLiteral(string $value): string
    {
        if (! str_contains($value, "'")) {
            return "'{$value}'";
        }

        if (! str_contains($value, '"')) {
            return "\"{$value}\"";
        }

        $parts = explode("'", $value);

        return 'concat('.implode(', "\'", ', array_map(
            fn (string $part): string => "'{$part}'",
            $parts
        )).')';
    }

    private function countCallsToAction(DOMXPath $xpath): int
    {
        $count = 0;
        foreach ($xpath->query('//a|//button|//input[@type="submit"]') ?: [] as $node) {
            $text = strtolower(trim($node instanceof DOMElement && $node->tagName === 'input'
                ? $node->getAttribute('value')
                : $node->textContent));

            if (preg_match('/get started|contact|book|buy|shop|request|quote|demo|sign up|register|call|download|subscribe|start|learn more|enquire/', $text)) {
                $count++;
            }
        }

        return $count;
    }

    private function visibleText(DOMDocument $document): string
    {
        $copy = clone $document;
        $xpath = new DOMXPath($copy);

        foreach ($xpath->query('//script|//style|//noscript|//svg') ?: [] as $node) {
            $node->parentNode?->removeChild($node);
        }

        return trim(preg_replace('/\\s+/', ' ', $copy->textContent) ?? '');
    }
}
