@extends('layout.app')

@section('title', 'Rekomendasi Pekerjaan')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-between mb-4">
            <div class="col">
                <h3>3 Rekomendasi Pekerjaan Terbaik Untuk Anda</h3>
                <p class="text-muted">Berdasarkan hasil analisis kecocokan dari kuesioner yang telah Anda isi</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('student.kuis') }}" class="btn btn-outline-primary">
                    <i class="fas fa-redo me-2"></i>Isi Ulang Kuesioner
                </a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($recommendations as $index => $recommendation)
                @php
                    $job = $jobDetails[$recommendation['job_id']] ?? null;
                    $matchPercentage = $recommendation['match_percentage'] ?? $recommendation['score'] * 100;
                    $matchClass = match (true) {
                        $matchPercentage >= 85 => ['bg-success', 'Sangat Cocok'],
                        $matchPercentage >= 70 => ['bg-primary', 'Cocok'],
                        $matchPercentage >= 50 => ['bg-warning', 'Cukup Cocok'],
                        default => ['bg-danger', 'Kurang Cocok'],
                    };
                    $cardClass = $index === 0 ? 'border-primary shadow' : 'shadow-sm';
                    $isTopMatch = $index === 0;
                @endphp
                @if ($job)
                    <div class="col-md-4">
                        <div class="card h-100 {{ $cardClass }}">
                            @if ($isTopMatch)
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-trophy me-2"></i>Rekomendasi Terbaik
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">{{ $job->name }}</h5>
                                    <span class="badge {{ $matchClass[0] }} position-relative">
                                        {{ $matchClass[1] }}
                                        <span
                                            class="position-absolute top-100 start-50 translate-middle badge rounded-pill bg-dark">
                                            {{ number_format($matchPercentage, 1) }}%
                                        </span>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <p class="text-muted small mb-2">{{ $job->description }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted fs-7">Keahlian yang Dibutuhkan:</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($job->skills_needed as $skill)
                                            <span class="badge bg-light text-dark border">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Kisaran Gaji:</small>
                                        <span class="text-success fw-bold">{{ $job->formatted_salary }}</span>
                                    </div>
                                    <span class="badge bg-info">{{ $job->industry_type }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Belum ada rekomendasi pekerjaan yang tersedia.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Add this somewhere appropriate in your recommendation view - 
             perhaps near the end before closing the main container -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Opsi Tambahan</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Ingin mengambil ulang kuesioner? Anda dapat mengisi kembali kuesioner untuk mendapatkan rekomendasi yang lebih sesuai.
                </p>
                <a href="{{ route('student.kuis', ['retake' => true]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i> Ambil Ulang Kuesioner
                </a>
                <small class="d-block mt-2 text-muted">
                    <i class="fas fa-info-circle me-1"></i> Mengambil ulang kuesioner akan menggantikan hasil rekomendasi sebelumnya.
                </small>
            </div>
        </div>
    </div>

    <style>
        .fs-7 {
            font-size: 0.875rem;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .badge.position-relative {
            padding-bottom: 1.5rem;
        }
    </style>
@endsection
