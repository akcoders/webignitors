@extends('layouts.admin')
@section('title', 'New Blog Article')
@section('content')
<div class="admin-shell admin-blog-shell">
    <header class="admin-page-head"><div><a class="admin-back-link" href="{{ route('admin.blog.index') }}"><i class="bi bi-arrow-left"></i> Content library</a><h1>Write a new article.</h1><p>Draft in Markdown, then shape how the page appears in search and social feeds.</p></div></header>
    <form class="blog-editor" method="POST" action="{{ route('admin.blog.store') }}">
        @csrf
        @include('admin.blog.partials.form')
    </form>
</div>
@endsection
