@extends('layouts.app')

@section('title', 'iOS & Android App Development')
@section('meta_description', 'End-to-end iOS and Android app development covering product strategy, UX/UI, backend engineering, launch, analytics, and iteration.')

@section('content')
@php
    $theme = 'lime-theme';
    $eyebrow = 'Mobile apps';
    $serviceKey = 'mobile-apps';
    $headline = 'Apps people keep';
    $accent = 'within reach.';
    $lead = 'We turn promising concepts into intuitive iOS and Android products—thoughtfully scoped, beautifully designed, and engineered for everyday use.';
    $icon = 'bi-phone';
    $opportunityTitle = 'The best app is not the one with the most features.';
    $opportunityCopy = [
        'It is the one that solves a recurring problem so cleanly that opening it becomes a habit. That requires product judgment before it requires code.',
        'We find the smallest valuable experience, prototype it with real users, then build a stable foundation that can grow without dragging yesterday’s decisions behind it.'
    ];
    $deliverableTitle = 'Product thinking, all the way through.';
    $deliverableCopy = 'From MVP definition to store release and iteration, one team owns the complete product experience.';
    $deliverables = [
        ['icon' => 'bi-lightbulb', 'title' => 'Product discovery', 'copy' => 'Opportunity framing, user interviews, market scan, MVP definition, and a realistic delivery roadmap.'],
        ['icon' => 'bi-intersect', 'title' => 'Mobile UX/UI', 'copy' => 'Flows, interactive prototypes, design systems, and native patterns that make the app feel familiar.'],
        ['icon' => 'bi-apple', 'title' => 'iOS development', 'copy' => 'Polished Swift experiences with platform conventions, performance, privacy, and App Store readiness.'],
        ['icon' => 'bi-android2', 'title' => 'Android development', 'copy' => 'Reliable Kotlin or cross-platform applications designed for the realities of Android devices.'],
        ['icon' => 'bi-cloud-arrow-up', 'title' => 'Backend & integrations', 'copy' => 'APIs, authentication, payments, notifications, subscriptions, dashboards, and third-party services.'],
        ['icon' => 'bi-activity', 'title' => 'Launch & evolution', 'copy' => 'Store preparation, analytics, crash monitoring, user feedback, and a focused post-launch roadmap.'],
    ];
    $featureTitle = 'Ready beyond the happy path.';
    $featureList = ['Pragmatic native or cross-platform recommendations', 'Offline, error, and low-connectivity states', 'Secure authentication and privacy-aware data handling', 'Analytics, crash reporting, and release observability', 'App Store and Play Store submission support'];
    $steps = [
        ['label' => 'Shape', 'title' => 'Find the core habit', 'copy' => 'We define the user, recurring need, value loop, and smallest release that can prove the proposition.'],
        ['label' => 'Test', 'title' => 'Put a prototype in hands', 'copy' => 'A realistic interactive model lets us improve critical flows before engineering begins.'],
        ['label' => 'Build', 'title' => 'Ship in usable slices', 'copy' => 'App and backend development progress together through testable releases and weekly product reviews.'],
        ['label' => 'Evolve', 'title' => 'Follow the evidence', 'copy' => 'Behavior, retention, reviews, and business results shape an intentional product roadmap.'],
    ];
    $quote = 'They kept us focused on the habit that mattered. We launched lighter, learned faster, and reached 100,000 users without rebuilding.';
    $quoteInitials = 'CF'; $quoteName = 'Client feedback'; $quoteRole = 'Mobile product partner';
    $faqs = [
        ['question' => 'Should we build native or cross-platform?', 'answer' => 'It depends on the experience, team, integrations, performance needs, and roadmap. We make a recommendation during discovery based on total product risk—not fashion.'],
        ['question' => 'Can you build the backend too?', 'answer' => 'Yes. We design and build APIs, admin tools, cloud services, authentication, subscriptions, notifications, and integrations as part of one system.'],
        ['question' => 'Can you help validate the idea before a full build?', 'answer' => 'Absolutely. A focused product sprint can clarify the market, value proposition, MVP, prototype, technical approach, budget, and next-stage evidence.'],
        ['question' => 'What happens after the app launches?', 'answer' => 'We support store releases, stability monitoring, analytics, user-feedback synthesis, experiments, and continued feature development.'],
    ];
    $ctaTitle = 'Have an app idea living in your notes?';
    $ctaCopy = 'Send the rough version. We will help you shape the smallest, strongest product worth building.';
@endphp
@include('partials.service-detail')
@endsection
