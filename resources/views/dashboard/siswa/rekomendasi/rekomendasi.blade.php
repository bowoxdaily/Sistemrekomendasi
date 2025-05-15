@extends('layout.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">Rekomendasi Pekerjaan</h3>

        <div class="row">
            @forelse($recommendations as $index => $recommendation)
                @php
                    $job = $jobDetails[$recommendation['job_id']] ?? null;
                @endphp
                @if ($job)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-primary mb-2">Rekomendasi #{{ $index + 1 }}</span>
                                        <h5 class="card-title mb-1">{{ $job->name }}</h5>
                                        <p class="text-muted">{{ $job->industry_type }}</p>
                                    </div>
                                    <span class="badge bg-success">
                                        {{ number_format($recommendation['score'] * 100, 1) }}% Match
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <strong>Deskripsi:</strong>
                                    <p>{{ $job->description }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Persyaratan:</strong>
                                    <ul class="mb-0">
                                        @foreach ($job->requirements as $requirement)
                                            <li>{{ $requirement }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <strong>Keahlian yang Dibutuhkan:</strong>
                                    <div class="mt-2">
                                        @foreach ($job->skills_needed as $skill)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <strong>Gaji:</strong>
                                    <p class="mb-0">Rp {{ number_format($job->average_salary, 0, ',', '.') }}</p>
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
