@extends('layouts.admin')
@section('title', 'Edit '.$post->title)
@section('content')
<div class="admin-shell admin-blog-shell">
    <header class="admin-page-head"><div><a class="admin-back-link" href="{{ route('admin.blog.index') }}"><i class="bi bi-arrow-left"></i> Content library</a><h1>Edit article.</h1><p>Last updated {{ $post->updated_at->diffForHumans() }} · SEO score {{ $post->seo_score }}/100.</p></div>@if($post->isPubliclyVisible())<a class="admin-secondary-button" href="{{ route('blog.show', $post) }}" target="_blank">View live <i class="bi bi-box-arrow-up-right"></i></a>@endif</header>
    <form class="blog-editor" method="POST" action="{{ route('admin.blog.update', $post) }}">
        @csrf
        @method('PUT')
        @include('admin.blog.partials.form')
    </form>
    <form class="blog-delete-form" method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm('Move this article to trash?');">@csrf @method('DELETE')<button type="submit"><i class="bi bi-trash3"></i> Move article to trash</button></form>
</div>
@endsection
