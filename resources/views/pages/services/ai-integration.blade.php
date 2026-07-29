@extends('layouts.app')

@section('title', 'AI Integration')
@section('meta_description', 'Practical AI consulting and integration for copilots, RAG search, workflow automation, support, content operations, and AI-enabled products.')

@section('content')
@php
    $theme = 'aqua-theme';
    $eyebrow = 'AI integration';
    $serviceKey = 'ai-integration';
    $headline = 'Put AI where it';
    $accent = 'actually helps.';
    $lead = 'We find high-value use cases, connect the right models to your data and workflows, and build trustworthy AI experiences your team can use every day.';
    $icon = 'bi-stars';
    $opportunityTitle = 'The model is the easy part. The workflow is the product.';
    $opportunityCopy = [
        'Useful AI depends on context, clean inputs, careful product design, evaluation, and a clear place in the way people already work.',
        'We start with the bottleneck—not the buzzword. Then we prototype against real data, measure quality and cost, and engineer the surrounding system with human control where it matters.'
    ];
    $deliverableTitle = 'Practical intelligence, responsibly shipped.';
    $deliverableCopy = 'From opportunity mapping through production monitoring, we cover the whole AI product—not only the API call.';
    $deliverables = [
        ['icon' => 'bi-map', 'title' => 'AI opportunity sprint', 'copy' => 'Workflow mapping, feasibility, value, risk, data readiness, and a prioritized pilot roadmap.'],
        ['icon' => 'bi-chat-square-dots', 'title' => 'Copilots & assistants', 'copy' => 'Role-specific tools that draft, summarize, analyze, and guide work with the right context.'],
        ['icon' => 'bi-database-check', 'title' => 'RAG & knowledge search', 'copy' => 'Grounded answers across company documents with retrieval, citations, permissions, and evaluation.'],
        ['icon' => 'bi-diagram-3', 'title' => 'Workflow automation', 'copy' => 'AI connected to business rules, review steps, and existing systems through reliable orchestration.'],
        ['icon' => 'bi-headset', 'title' => 'Support intelligence', 'copy' => 'Triage, agent assist, self-service, conversation insight, and escalation designed around trust.'],
        ['icon' => 'bi-boxes', 'title' => 'AI-enabled products', 'copy' => 'New customer-facing features and products with model routing, usage controls, and thoughtful UX.'],
    ];
    $featureTitle = 'Guardrails are part of the design.';
    $featureList = ['Representative evaluation sets and quality thresholds', 'Human review and escalation for consequential actions', 'Permissions and tenant-aware data retrieval', 'Cost, latency, usage, and failure monitoring', 'Provider flexibility and sensible fallbacks'];
    $steps = [
        ['label' => 'Map', 'title' => 'Find leverage in the workflow', 'copy' => 'We identify repeated judgment, search, drafting, or handoff work where AI can create meaningful value.'],
        ['label' => 'Prototype', 'title' => 'Test with real examples', 'copy' => 'A narrow prototype lets us evaluate quality, usability, risk, speed, and cost before scaling scope.'],
        ['label' => 'Integrate', 'title' => 'Build the surrounding system', 'copy' => 'We connect data, permissions, interfaces, business rules, review, and monitoring into one dependable flow.'],
        ['label' => 'Evaluate', 'title' => 'Improve with evidence', 'copy' => 'Production feedback and structured evaluations drive prompts, retrieval, models, and experience improvements.'],
    ];
    $quote = 'They moved us from scattered AI experiments to one production workflow that saves our operations team more than 300 hours a month.';
    $quoteInitials = 'CF'; $quoteName = 'Client feedback'; $quoteRole = 'AI integration partner';
    $faqs = [
        ['question' => 'How do we know where AI is worth using?', 'answer' => 'We score opportunities against repetition, data availability, error tolerance, business value, adoption friction, and implementation risk. A short opportunity sprint produces a prioritized answer.'],
        ['question' => 'Can you integrate with our existing tools and data?', 'answer' => 'Yes. We regularly connect AI workflows to CRMs, support systems, knowledge bases, databases, internal APIs, document stores, and communication tools.'],
        ['question' => 'How do you reduce hallucinations and risk?', 'answer' => 'We use grounded retrieval, constrained outputs, validation, representative evaluations, permissions, human review, monitoring, and clear UX around uncertainty.'],
        ['question' => 'Are we locked into one model provider?', 'answer' => 'Not by default. We select models by task and design abstraction where provider flexibility has real operational value.'],
    ];
    $ctaTitle = 'Have an AI use case—or just a bottleneck?';
    $ctaCopy = 'Tell us where work slows down. We will help separate a valuable AI opportunity from an expensive demo.';
@endphp
@include('partials.service-detail')
@endsection
