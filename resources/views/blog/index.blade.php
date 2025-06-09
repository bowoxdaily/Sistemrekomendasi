@extends('layout.app')

@section('title', 'Blog')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">Blog & Informasi</h1>
            
            <!-- Search and filter bar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('blog.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Cari artikel...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="category" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $categoryItem)
                                <option value="{{ $categoryItem }}" {{ ($category == $categoryItem) ? 'selected' : '' }}>
                                    {{ $categoryItem }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($search || $category)
                        <div class="col-md-2">
                            <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary w-100">
                                Reset Filter
                            </a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            
            @if($search || $category)
            <div class="alert alert-info">
                Menampilkan hasil 
                @if($search) pencarian "{{ $search }}" @endif
                @if($search && $category) dalam @endif
                @if($category) kategori "{{ $category }}" @endif
                ({{ $blogs->total() }} artikel)
            </div>
            @endif
        </div>
    </div>
    
    <div class="row">
        @forelse($blogs as $blog)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                @if($blog->image)
                <div style="height: 200px; overflow: hidden;">
                    <img src="{{ asset($blog->image) }}" class="card-img-top" style="object-fit: cover; height: 100%; width: 100%;" alt="{{ $blog->title }}">
                </div>
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="mdi mdi-image-filter text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif
                <div class="card-body d-flex flex-column">
                    @if($blog->category)
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ $blog->category }}</span>
                    </div>
                    @endif
                    <h5 class="card-title">{{ $blog->title }}</h5>
                    <p class="card-text text-muted">
                        {{ Str::limit(strip_tags($blog->content), 120) }}
                    </p>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($blog->created_at)->locale('id')->isoFormat('D MMM YYYY') }}
                        </small>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary">
                            Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="mdi mdi-information-outline me-2"></i>
                Tidak ada artikel ditemukan.
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->withQueryString()->links() }}
    </div>
</div>
@endsection
