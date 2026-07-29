@extends('layouts.app')

@section('title', 'Digital Marketing')
@section('meta_description', 'Connected digital marketing across SEO, content, paid media, lifecycle, analytics, and conversion optimization.')

@section('content')
@php
    $theme = 'coral-theme';
    $eyebrow = 'Digital marketing';
    $serviceKey = 'digital-marketing';
    $headline = 'Turn attention into';
    $accent = 'compounding growth.';
    $lead = 'Connected strategy across search, content, paid media, lifecycle, and conversion—measured by business outcomes, not decorative dashboards.';
    $icon = 'bi-graph-up-arrow';
    $opportunityTitle = 'More activity is not the same as more momentum.';
    $opportunityCopy = [
        'Fragmented channels create fragmented learning. When search, content, media, product, and creative share one view of the customer, every campaign makes the next one smarter.',
        'We build that connected growth system—then test methodically, report plainly, and concentrate investment where it produces a durable advantage.'
    ];
    $deliverableTitle = 'A growth system, not a bag of tactics.';
    $deliverableCopy = 'Senior strategy and hands-on execution, connected to a measurement framework everyone can understand.';
    $deliverables = [
        ['icon' => 'bi-search', 'title' => 'SEO & search strategy', 'copy' => 'Technical foundations, demand research, information architecture, content, and authority-building.'],
        ['icon' => 'bi-journal-text', 'title' => 'Content systems', 'copy' => 'Editorial strategy, high-intent pages, thought leadership, distribution, and reusable production workflows.'],
        ['icon' => 'bi-megaphone', 'title' => 'Paid acquisition', 'copy' => 'Search and social campaigns with sharper creative, landing pages, audience testing, and budget discipline.'],
        ['icon' => 'bi-envelope-paper', 'title' => 'Lifecycle marketing', 'copy' => 'Welcome, nurture, onboarding, activation, retention, and win-back journeys that move the relationship forward.'],
        ['icon' => 'bi-pie-chart', 'title' => 'Analytics & attribution', 'copy' => 'Clean events, dashboards, experiments, and reporting built around decisions rather than vanity.'],
        ['icon' => 'bi-toggles2', 'title' => 'Conversion optimization', 'copy' => 'Research-led experiments across messaging, UX, forms, offers, and the highest-impact friction points.'],
    ];
    $featureTitle = 'Signal over noise.';
    $featureList = ['Goals and measurement mapped before channel activity', 'Creative, landing pages, and campaigns built in one loop', 'Transparent weekly insights and monthly strategy review', 'First-party data and privacy-aware measurement', 'A documented backlog of hypotheses and experiments'];
    $steps = [
        ['label' => 'Diagnose', 'title' => 'Find the real constraint', 'copy' => 'We audit demand, audience, funnel, channels, creative, data, and economics to locate the best opportunity.'],
        ['label' => 'Focus', 'title' => 'Build the growth thesis', 'copy' => 'A practical strategy links the audience, message, offer, journey, channel, and number that matters.'],
        ['label' => 'Experiment', 'title' => 'Create fast learning loops', 'copy' => 'We launch controlled tests, measure real movement, and document what the market teaches us.'],
        ['label' => 'Compound', 'title' => 'Scale what earns it', 'copy' => 'Winning messages, audiences, content, and journeys become reusable systems rather than one-off wins.'],
    ];
    $quote = 'For the first time, paid, content, SEO, and our product story felt like one strategy. Pipeline quality changed within a quarter.';
    $quoteInitials = 'CF'; $quoteName = 'Client feedback'; $quoteRole = 'Growth strategy partner';
    $faqs = [
        ['question' => 'Which channels do you manage?', 'answer' => 'Our core work spans SEO, content, paid search, paid social, lifecycle email, landing pages, analytics, and conversion optimization. We recommend the mix based on the customer journey.'],
        ['question' => 'Do you work with an in-house marketing team?', 'answer' => 'Often. We can lead a focused growth workstream, add specialist capability, or operate as an embedded cross-functional pod alongside internal owners.'],
        ['question' => 'How soon should we expect results?', 'answer' => 'Paid and conversion experiments can create useful signal within weeks; organic search and content compound over months. We set leading and lagging indicators up front.'],
        ['question' => 'Is media spend included in your fee?', 'answer' => 'No. Platform spend is paid directly by you and stays transparent. Our proposal separates strategy and management fees from media budget.'],
    ];
    $ctaTitle = 'Ready for marketing that learns as it grows?';
    $ctaCopy = 'Share your target, current channels, and what is not working. We will help find the constraint.';
@endphp
@include('partials.service-detail')
@endsection
