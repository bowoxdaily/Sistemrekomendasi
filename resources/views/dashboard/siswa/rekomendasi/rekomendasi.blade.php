@extends('layout.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-between mb-4">
            <div class="col">
                <h3>Rekomendasi Pekerjaan Terbaik Untuk Anda</h3>
                <p class="text-muted">Diurutkan berdasarkan tingkat kesesuaian tertinggi</p>
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
                    $cardClass = $index === 0 ? 'border-primary' : '';
                    $isTopMatch = $index === 0;
                @endphp
                @if ($job)
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm {{ $cardClass }}">
                            @if ($isTopMatch)
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-star me-2"></i>Rekomendasi Terbaik
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-{{ $isTopMatch ? 'primary' : 'secondary' }}">
                                        Rekomendasi #{{ $index + 1 }}
                                    </span>
                                    <span
                                        class="badge bg-{{ $matchPercentage >= 80 ? 'success' : ($matchPercentage >= 60 ? 'warning' : 'danger') }}">
                                        {{ number_format($matchPercentage, 1) }}% Match
                                    </span>
                                </div>

                                <h4 class="card-title mb-3">{{ $job->name }}</h4>
                                <div class="mb-3">
                                    <h6 class="text-muted">Deskripsi Pekerjaan:</h6>
                                    <p>{{ $job->description }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted">Keahlian yang Dibutuhkan:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($job->skills_needed as $skill)
                                            <span class="badge bg-light text-dark">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Kisaran Gaji:</h6>
                                        <h5 class="text-success mb-0">{{ $job->formatted_salary }}</h5>
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
    </div>
@endsection
