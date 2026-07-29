<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'WebIgnitors builds ambitious websites, apps, growth systems, and practical AI integrations.')">
    <meta name="theme-color" content="#f6f3eb">
    <title>@yield('title', 'Creative Development & Growth Agency') — WebIgnitors</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="visually-hidden-focusable position-fixed top-0 start-0 z-3 m-3 btn btn-lime" href="#main-content">
        Skip to content
    </a>

    <div class="scroll-progress" aria-hidden="true"><span></span></div>
    <div class="site-ambient" aria-hidden="true">
        <span class="ambient-orb ambient-orb-one"></span>
        <span class="ambient-orb ambient-orb-two"></span>
        <span class="ambient-noise"></span>
    </div>
    <div class="cursor-core" aria-hidden="true"></div>
    <div class="cursor-aura" aria-hidden="true"></div>

    @include('partials.nav')

    <main id="main-content" class="page-shell">
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
