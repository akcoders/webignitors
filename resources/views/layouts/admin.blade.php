<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111715">
    <title>@yield('title', 'Admin Console') — WebIgnitors</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <header class="admin-mobile-bar">
        <a href="{{ route('admin.dashboard') }}"><span class="brand-mark"><span>WI</span></span> WebIgnitors</a>
        <button type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-label="Open admin navigation"><i class="bi bi-list"></i></button>
    </header>

    <aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar">
        <div class="offcanvas-header d-lg-none">
            <strong>Admin navigation</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
        </div>
        <div class="admin-sidebar-inner">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark"><span>WI</span></span>
                <span>WebIgnitors<small>Control room</small></span>
            </a>
            <nav aria-label="Admin navigation">
                <a class="active" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i> Overview</a>
                <a href="{{ route('admin.dashboard') }}#reports"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
                <a href="{{ route('admin.dashboard') }}#inquiries"><i class="bi bi-chat-square-text"></i> Inquiries</a>
                <a href="{{ route('admin.dashboard') }}#users"><i class="bi bi-people"></i> Users</a>
                <a href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View website</a>
            </nav>
            <div class="admin-profile">
                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="admin-signout" type="submit"><i class="bi bi-box-arrow-left"></i> Sign out</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        @include('partials.flash')
        @yield('content')
    </main>
</body>
</html>
