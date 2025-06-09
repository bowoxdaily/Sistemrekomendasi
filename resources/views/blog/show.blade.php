@extends('layout.app')

@section('title', $blog->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Blog Post -->
            <div class="card border-0 shadow-sm mb-4">
                @if($blog->image)
                <img src="{{ asset($blog->image) }}" class="card-img-top img-fluid" alt="{{ $blog->title }}">
                @endif
                <div class="card-body p-4">
                    <h1 class="card-title mb-3">{{ $blog->title }}</h1>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-2">
                                <i class="mdi mdi-account-circle text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $blog->author ?: ($blog->user ? $blog->user->name : 'Admin') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($blog->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                            </div>
                        </div>
                        
                        @if($blog->category)
                        <span class="badge bg-primary py-2 px-3">{{ $blog->category }}</span>
                        @endif
                    </div>
                    
                    <div class="blog-content">
                        {!! $blog->content !!}
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Blog
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-share-variant"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-linkedin"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-3">Artikel Terkait</h4>
                    
                    <div class="row">
                        @foreach($relatedPosts as $relatedPost)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                @if($relatedPost->image)
                                <img src="{{ asset($relatedPost->image) }}" class="card-img-top" alt="{{ $relatedPost->title }}">
                                @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="mdi mdi-image-filter text-muted" style="font-size: 2rem;"></i>
                                </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('blog.show', $relatedPost->slug) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($relatedPost->title, 50) }}
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Sidebar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Cari Artikel</h5>
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Kata kunci...">
                            <button class="btn btn-primary" type="submit">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Artikel Terbaru</h5>
                    <div class="list-group list-group-flush">
                        @foreach($recentPosts as $recentPost)
                        <a href="{{ route('blog.show', $recentPost->slug) }}" class="list-group-item list-group-item-action px-0 py-3 border-top">
                            <div class="d-flex">
                                @if($recentPost->image)
                                <div class="flex-shrink-0" style="width: 70px; height: 50px; overflow: hidden;">
                                    <img src="{{ asset($recentPost->image) }}" class="img-fluid" alt="{{ $recentPost->title }}" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                                @else
                                <div class="flex-shrink-0 bg-light d-flex align-items-center justify-content-center" style="width: 70px; height: 50px;">
                                    <i class="mdi mdi-image-filter text-muted"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Str::limit($recentPost->title, 60) }}</h6>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($recentPost->created_at)->locale('id')->isoFormat('D MMM YYYY') }}</small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Kategori</h5>
                    <div class="list-group list-group-flush">
                        @php
                        $categories = \App\Models\Blog::distinct()
                            ->whereNotNull('category')
                            ->where('category', '!=', '')
                            ->where('is_published', true)
                            ->pluck('category');
                        @endphp
                        
                        @foreach($categories as $categoryItem)
                        <a href="{{ route('blog.index', ['category' => $categoryItem]) }}" class="list-group-item list-group-item-action px-0 py-2 border-top d-flex justify-content-between align-items-center">
                            {{ $categoryItem }}
                            <span class="badge bg-primary rounded-pill">
                                {{ \App\Models\Blog::where('category', $categoryItem)->where('is_published', true)->count() }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .blog-content {
        line-height: 1.8;
        font-size: 1.1rem;
    }
    
    .blog-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .blog-content h2, .blog-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .blog-content p {
        margin-bottom: 1.5rem;
    }
    
    .blog-content ul, .blog-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .blog-content blockquote {
        border-left: 4px solid #3490dc;
        padding-left: 1rem;
        font-style: italic;
        margin: 1.5rem 0;
        color: #6c757d;
    }
</style>
@endpush
