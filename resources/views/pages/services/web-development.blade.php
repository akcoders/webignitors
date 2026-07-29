@extends('layouts.app')

@section('title', 'Web Development')
@section('meta_description', 'High-performance website and web application development, including Laravel, e-commerce, SaaS, APIs, UX, and ongoing optimization.')

@section('content')
@php
    $theme = 'violet-theme';
    $eyebrow = 'Web development';
    $serviceKey = 'web-development';
    $headline = 'Web experiences built to';
    $accent = 'earn attention.';
    $lead = 'Distinctive on the surface. Serious underneath. We design and engineer fast, accessible websites and web products that turn curiosity into action.';
    $icon = 'bi-code-slash';
    $opportunityTitle = 'Your website should be your hardest-working teammate.';
    $opportunityCopy = [
        'Most websites are either beautiful brochures or capable machines. The best ones are both: a memorable expression of the brand and a measurable growth system.',
        'We connect research, conversion thinking, expressive design, and robust engineering so the experience feels effortless for customers and stays manageable for your team.'
    ];
    $deliverableTitle = 'Everything between first sketch and scale.';
    $deliverableCopy = 'A senior product team shaped around your challenge, with one connected workflow from strategy through launch.';
    $deliverables = [
        ['icon' => 'bi-compass', 'title' => 'Strategy & architecture', 'copy' => 'Audience, positioning, journeys, content structure, requirements, and the right technical shape.'],
        ['icon' => 'bi-bezier2', 'title' => 'UX & visual design', 'copy' => 'Wireframes, prototypes, accessible interfaces, and a visual language with a point of view.'],
        ['icon' => 'bi-braces', 'title' => 'Laravel applications', 'copy' => 'Secure portals, SaaS platforms, custom workflows, APIs, and dependable backend systems.'],
        ['icon' => 'bi-bag-check', 'title' => 'Commerce', 'copy' => 'Shopify, custom storefronts, subscriptions, checkout optimization, and business integrations.'],
        ['icon' => 'bi-speedometer2', 'title' => 'Performance & SEO', 'copy' => 'Core Web Vitals, technical SEO, accessibility, analytics, and conversion instrumentation.'],
        ['icon' => 'bi-arrow-repeat', 'title' => 'Care & optimization', 'copy' => 'Monitoring, upgrades, experiments, content support, and an experienced team after launch.'],
    ];
    $featureTitle = 'Built for the real world.';
    $featureList = ['Responsive behavior across modern devices', 'WCAG-minded accessibility and semantic markup', 'Search-friendly structure and performance budgets', 'Secure coding, validation, and sensible data protection', 'Analytics events tied to meaningful business actions'];
    $steps = [
        ['label' => 'Discover', 'title' => 'Define the win', 'copy' => 'We align users, business goals, content, constraints, and the evidence that will show the site is working.'],
        ['label' => 'Prototype', 'title' => 'Prove the journey', 'copy' => 'We map and test the most important flows before committing effort to polish or code.'],
        ['label' => 'Design & build', 'title' => 'Create in one loop', 'copy' => 'Designers and developers work together in visible sprints, protecting both ambition and feasibility.'],
        ['label' => 'Launch & learn', 'title' => 'Release with confidence', 'copy' => 'We test, migrate, monitor, and improve the experience using real behavior after release.'],
    ];
    $quote = 'The new site finally feels as sophisticated as our product—and qualified demo requests climbed in the first month.';
    $quoteInitials = 'CF'; $quoteName = 'Client feedback'; $quoteRole = 'Web platform partner';
    $faqs = [
        ['question' => 'What kinds of web projects do you take on?', 'answer' => 'We build marketing websites, e-commerce experiences, Laravel platforms, SaaS products, member portals, internal tools, and API-driven experiences.'],
        ['question' => 'Can you work with our existing brand or codebase?', 'answer' => 'Yes. We begin with a focused audit to understand the system, technical debt, opportunities, and whether an evolution or rebuild is the smarter investment.'],
        ['question' => 'How long does a typical build take?', 'answer' => 'Focused marketing sites often take 8–12 weeks. Larger platforms commonly run 14–24 weeks. We confirm timing after discovery and can phase complex products.'],
        ['question' => 'Do you provide hosting and maintenance?', 'answer' => 'We can configure deployment and cloud infrastructure, then stay involved through a monthly care and optimization plan tailored to the product.'],
    ];
    $ctaTitle = 'Need a website that does more than exist?';
    $ctaCopy = 'Share your goals, rough scope, and current sticking point. We will turn it into a practical first move.';
@endphp
@include('partials.service-detail')
@endsection
