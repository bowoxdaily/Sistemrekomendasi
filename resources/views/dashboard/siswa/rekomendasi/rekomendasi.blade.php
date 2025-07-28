@extends('layout.app')

@section('title', 'Rekomendasi Pekerjaan')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-between mb-4">
            <div class="col">
                <h3>3 Rekomendasi Pekerjaan Terbaik Untuk Anda</h3>
                <p class="text-muted">Berdasarkan hasil analisis kecocokan dari kuesioner yang telah Anda isi</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" id="alternativeDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list-alt me-2"></i>Rekomendasi Alternatif
                        @if (count($recommendations) > 3)
                            <span class="badge bg-success ms-1">{{ count($recommendations) - 3 }}</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="alternativeDropdown"
                        style="min-width: 350px;">
                        <li>
                            <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Pilihan Pekerjaan Lainnya</span>
                                <small class="text-muted">{{ count($recommendations) }} total ditemukan</small>
                            </h6>
                        </li>
                        @if (count($recommendations) > 0)
                            @foreach ($recommendations as $index => $recommendation)
                                @php
                                    $job = $jobDetails[$recommendation['job_id']] ?? null;
                                    $matchPercentage =
                                        $recommendation['match_percentage'] ?? $recommendation['score'] * 100;
                                    $isMainRecommendation = $index < 3;
                                @endphp
                                @if ($job)
                                    <li>
                                        <a class="dropdown-item py-3 {{ $isMainRecommendation ? 'bg-light' : '' }}"
                                            href="javascript:void(0)"
                                            onclick="showJobDetail({{ $job->id }}, '{{ addslashes($job->name) }}', '{{ addslashes($job->description) }}', {{ $matchPercentage }}, '{{ addslashes($job->industry_type) }}', '{{ addslashes($job->formatted_salary) }}', {{ json_encode($job->skills_needed) }})">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1 me-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <strong class="me-2">{{ $job->name }}</strong>
                                                        @if ($isMainRecommendation)
                                                            <span class="badge bg-primary text-white"
                                                                style="font-size: 0.65rem;">TOP {{ $index + 1 }}</span>
                                                        @endif
                                                    </div>
                                                    <small
                                                        class="text-muted d-block">{{ Str::limit($job->description, 60) }}</small>
                                                    <small
                                                        class="text-success fw-bold">{{ $job->formatted_salary }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge bg-{{ $matchPercentage >= 70 ? 'success' : ($matchPercentage >= 50 ? 'warning' : 'secondary') }}">
                                                        {{ number_format($matchPercentage, 1) }}%
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ $job->industry_type }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    @if (!$loop->last)
                                        <li>
                                            <hr class="dropdown-divider my-1">
                                        </li>
                                    @endif
                                @endif
                            @endforeach
                        @else
                            <li><span class="dropdown-item-text text-muted">Tidak ada rekomendasi alternatif</span></li>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div class="dropdown-item-text text-center">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Klik pada pekerjaan untuk melihat detail lengkap
                                </small>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="row g-4">
            @forelse(array_slice($recommendations, 0, 3) as $index => $recommendation)
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

        <!-- Modal Detail Pekerjaan Alternatif -->
        <div class="modal fade" id="jobDetailModal" tabindex="-1" aria-labelledby="jobDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="jobDetailModalLabel">Detail Pekerjaan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 id="modalJobName" class="text-primary mb-3"></h4>
                                <p id="modalJobDescription" class="text-muted mb-3"></p>

                                <h6 class="fw-bold mb-2">Keahlian yang Dibutuhkan:</h6>
                                <div id="modalJobSkills" class="d-flex flex-wrap gap-1 mb-3"></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Kisaran Gaji:</h6>
                                        <span id="modalJobSalary" class="text-success fs-5"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Jenis Industri:</h6>
                                        <span id="modalJobIndustry" class="badge bg-info fs-6"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Tingkat Kecocokan</h6>
                                        <div class="position-relative mb-3">
                                            <div class="progress" style="height: 10px;">
                                                <div id="modalMatchProgress" class="progress-bar" role="progressbar"
                                                    style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <h3 id="modalMatchPercentage" class="text-primary mb-2">0%</h3>
                                        <p id="modalMatchStatus" class="small text-muted mb-0"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" onclick="addToFavorites()">
                            <i class="fas fa-star me-2"></i>Tambah ke Favorit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this somewhere appropriate in your recommendation view -
                                 perhaps near the end before closing the main container -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Opsi Tambahan</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Ingin mengambil ulang kuesioner? Anda dapat mengisi kembali kuesioner untuk mendapatkan rekomendasi yang
                    lebih sesuai.
                </p>
                <a href="{{ route('student.kuis', ['retake' => true]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i> Ambil Ulang Kuesioner
                </a>
                <small class="d-block mt-2 text-muted">
                    <i class="fas fa-info-circle me-1"></i> Mengambil ulang kuesioner akan menggantikan hasil rekomendasi
                    sebelumnya.
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

        .dropdown-menu {
            max-height: 450px;
            overflow-y: auto;
            border: 2px solid #dee2e6;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        .dropdown-item.bg-light {
            background-color: #e3f2fd !important;
            border-left: 4px solid #2196f3;
        }

        .dropdown-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .progress {
            border-radius: 10px;
        }

        .modal-body .card {
            border: 2px solid #e9ecef;
        }

        .badge.fs-6 {
            font-size: 1rem !important;
            padding: 0.5rem 1rem;
        }
    </style>

    <script>
        function showJobDetail(jobId, jobName, jobDescription, matchPercentage, industryType, salary, skillsNeeded) {
            // Set modal content with proper escaping
            document.getElementById('modalJobName').innerHTML = escapeHtml(jobName);
            document.getElementById('modalJobDescription').innerHTML = escapeHtml(jobDescription);
            document.getElementById('modalJobSalary').innerHTML = escapeHtml(salary);
            document.getElementById('modalJobIndustry').innerHTML = escapeHtml(industryType);

            // Set match percentage and progress bar
            document.getElementById('modalMatchPercentage').textContent = matchPercentage.toFixed(1) + '%';

            const progressBar = document.getElementById('modalMatchProgress');
            progressBar.style.width = matchPercentage + '%';

            // Set progress bar color and status based on percentage
            const statusElement = document.getElementById('modalMatchStatus');
            if (matchPercentage >= 85) {
                progressBar.className = 'progress-bar bg-success';
                statusElement.textContent = 'Sangat Cocok - Rekomendasi terbaik untuk Anda';
                statusElement.className = 'small text-success mb-0';
            } else if (matchPercentage >= 70) {
                progressBar.className = 'progress-bar bg-primary';
                statusElement.textContent = 'Cocok - Sesuai dengan profil Anda';
                statusElement.className = 'small text-primary mb-0';
            } else if (matchPercentage >= 50) {
                progressBar.className = 'progress-bar bg-warning';
                statusElement.textContent = 'Cukup Cocok - Pertimbangkan dengan baik';
                statusElement.className = 'small text-warning mb-0';
            } else if (matchPercentage >= 30) {
                progressBar.className = 'progress-bar bg-info';
                statusElement.textContent = 'Alternatif - Bisa menjadi pilihan';
                statusElement.className = 'small text-info mb-0';
            } else {
                progressBar.className = 'progress-bar bg-danger';
                statusElement.textContent = 'Kurang Cocok - Tidak direkomendasikan';
                statusElement.className = 'small text-danger mb-0';
            }

            // Set skills
            const skillsContainer = document.getElementById('modalJobSkills');
            skillsContainer.innerHTML = '';

            if (Array.isArray(skillsNeeded)) {
                skillsNeeded.forEach(skill => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light text-dark border me-1 mb-1';
                    badge.textContent = skill;
                    skillsContainer.appendChild(badge);
                });
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('jobDetailModal'));
            modal.show();
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        function addToFavorites() {
            // Get current job details from modal
            const jobName = document.getElementById('modalJobName').textContent;
            const matchPercentage = document.getElementById('modalMatchPercentage').textContent;

            // Show success notification
            showNotification('success', `${jobName} telah ditambahkan ke daftar favorit Anda!`);
        }

        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Add notification when dropdown is opened
        document.getElementById('alternativeDropdown').addEventListener('click', function() {
            console.log('Dropdown rekomendasi alternatif dibuka');
        });

        // Add smooth scrolling to dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.style.scrollBehavior = 'smooth';
            }
        });
    </script>
@endsection
