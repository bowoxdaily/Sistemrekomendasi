@extends('layout.app')

@section('title', 'Dashboard | Siswa')

@section('content')
    <!-- Alert jika profil belum lengkap -->
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Alert untuk info -->
    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Top Row: Welcome message and date -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->student->nama_lengkap }}</h3>
                    <h6 class="font-weight-normal mb-0">
                        @php
                            $status = Auth::user()->student->status_setelah_lulus;
                            $badgeClass = 'primary';
                            $iconClass = 'user-graduate';

                            if ($status === 'belum_kerja') {
                                $badgeClass = 'warning';
                                $iconClass = 'search';
                            } elseif ($status === 'kerja') {
                                $badgeClass = 'success';
                                $iconClass = 'briefcase';
                            } elseif ($status === 'kuliah') {
                                $badgeClass = 'info';
                                $iconClass = 'university';
                            }

                            // Get the second badge info based on status
                            $secondBadgeClass = '';
                            $secondBadgeText = '';
                            $secondBadgeIcon = '';

                            if ($status === 'belum_kerja') {
                                if (Auth::user()->student->has_completed_questionnaire) {
                                    $secondBadgeClass = 'success';
                                    $secondBadgeText = 'Kuesioner Selesai';
                                    $secondBadgeIcon = 'check-circle';
                                } else {
                                    $secondBadgeClass = 'secondary';
                                    $secondBadgeText = 'Kuesioner Belum Selesai';
                                    $secondBadgeIcon = 'clipboard-list';
                                }
                            } elseif ($status === 'kerja') {
                                if (Auth::user()->student->sesuai_jurusan === 'ya') {
                                    $secondBadgeClass = 'success';
                                    $secondBadgeText = 'Sesuai Jurusan';
                                    $secondBadgeIcon = 'check-double';
                                } else {
                                    $secondBadgeClass = 'info';
                                    $secondBadgeText = 'Beda Jurusan';
                                    $secondBadgeIcon = 'random';
                                }
                            } elseif ($status === 'kuliah') {
                                $dataKuliah = \App\Models\DataKuliah::where(
                                    'student_id',
                                    Auth::user()->student->id,
                                )->first();
                                if ($dataKuliah && $dataKuliah->status_beasiswa === 'ya') {
                                    $secondBadgeClass = 'success';
                                    $secondBadgeText = 'Penerima Beasiswa';
                                    $secondBadgeIcon = 'award';
                                } else {
                                    $secondBadgeClass = 'secondary';
                                    $secondBadgeText = 'Non-Beasiswa';
                                    $secondBadgeIcon = 'user-graduate';
                                }
                            }
                        @endphp

                        <!-- First Badge: Status Setelah Lulus -->
                        <span class="badge badge-{{ $badgeClass }} font-weight-bold mr-2">
                            <i class="fas fa-{{ $iconClass }} mr-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>

                        <!-- Second Badge: Status Specific Context -->
                        <span class="badge badge-{{ $secondBadgeClass }} font-weight-bold">
                            <i class="fas fa-{{ $secondBadgeIcon }} mr-1"></i>
                            {{ $secondBadgeText }}
                        </span>
                    </h6>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex">
                        <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                            <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="mdi mdi-calendar"></i> {{ date('d M Y') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                <a class="dropdown-item" href="#">Lihat Kalender</a>
                                <a class="dropdown-item" href="#">Lihat Event</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card tale-bg">
                <div class="card-people">
                    <img src="{{ asset('admin/images/dashboard/people.svg') }}" alt="people">
                    <div class="alumni-info">
                        <h4 class="location font-weight-normal">Alumni SMKN 1 Terisi</h4>
                        <h6 class="font-weight-normal mb-0">
                            Tahun Lulus:
                            {{ Auth::user()->student->tanggal_lulus ? date('Y', strtotime(Auth::user()->student->tanggal_lulus)) : 'Belum diatur' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7 grid-margin transparent">
            <div class="row">
                <!-- Card 1: Status Profil -->
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Status Profil</p>
                            <p class="fs-30 mb-2">
                                {{ Auth::user()->student->is_profile_complete ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            <p>{{ Auth::user()->student->is_profile_complete ? 'Profil Anda sudah diisi dengan lengkap' : 'Silahkan lengkapi profil Anda' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Dynamic based on status -->
                <div class="col-md-6 mb-4 stretch-card transparent">
                    @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Status Kuesioner</p>
                                <p class="fs-30 mb-2">
                                    {{ Auth::user()->student->has_completed_questionnaire ? 'Selesai' : 'Belum Diisi' }}
                                </p>
                                <p>{{ Auth::user()->student->has_completed_questionnaire ? 'Anda sudah mengisi kuesioner' : 'Isi kuesioner untuk mendapat rekomendasi' }}
                                </p>
                                @if (Auth::user()->student->has_completed_questionnaire)
                                    @php
                                        $latestResponse = \App\Models\QuestionnaireResponse::where(
                                            'student_id',
                                            Auth::user()->student->id,
                                        )
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                    @endphp
                                    @if ($latestResponse && $latestResponse->hasRecommendations())
                                        <div class="mt-2">
                                            <a href="{{ route('student.recommendation.show') }}"
                                                class="btn btn-sm btn-outline-light">
                                                <i class="fas fa-briefcase mr-1"></i> Lihat Rekomendasi
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="mt-2">
                                        <a href="{{ url('siswa/questionnaire') }}" class="btn btn-sm btn-outline-light">
                                            <i class="fas fa-clipboard-list mr-1"></i> Isi Kuesioner
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif(Auth::user()->student->status_setelah_lulus === 'kerja')
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4">Informasi Pekerjaan</p>
                                @php
                                    $dataKerja = \App\Models\DataKerja::where(
                                        'student_id',
                                        Auth::user()->student->id,
                                    )->first();
                                @endphp

                                @if ($dataKerja)
                                    <p class="fs-30 mb-2">{{ ucfirst($dataKerja->jenis_pekerjaan ?? 'Aktif') }}</p>
                                    <p>
                                        @if ($dataKerja->nama_perusahaan)
                                            Anda bekerja di {{ $dataKerja->nama_perusahaan }}
                                            @if ($dataKerja->posisi)
                                                sebagai {{ $dataKerja->posisi }}
                                            @endif
                                        @else
                                            Posisi: {{ $dataKerja->posisi ?? 'Belum diisi' }}
                                        @endif
                                    </p>
                                    @if ($dataKerja->sesuai_jurusan)
                                        <div
                                            class="badge badge-{{ $dataKerja->sesuai_jurusan === 'ya' ? 'success' : 'info' }} mt-2">
                                            {{ $dataKerja->sesuai_jurusan === 'ya' ? 'Sesuai Jurusan' : 'Beda Jurusan' }}
                                        </div>
                                    @endif
                                @else
                                    <p class="fs-30 mb-2">Belum Lengkap</p>
                                    <p>Data pekerjaan belum tersedia</p>

                                    <button class="btn btn-outline-light btn-sm mt-2" data-toggle="modal"
                                        data-target="#kerjaModal">
                                        <i class="fas fa-plus-circle mr-1"></i> Lengkapi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card card-light-danger">
                            <div class="card-body">
                                <p class="mb-4">Informasi Pendidikan</p>
                                @php
                                    $dataKuliah = \App\Models\DataKuliah::where(
                                        'student_id',
                                        Auth::user()->student->id,
                                    )->first();
                                @endphp

                                @if ($dataKuliah)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="education-icon mr-3">
                                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="fs-30 mb-0 font-weight-medium">{{ $dataKuliah->jenjang }}
                                                {{ $dataKuliah->jurusan }}</p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-university mr-1"></i> {{ $dataKuliah->nama_pt }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mt-3">
                                        <div class="mr-4">
                                            <small class="text-muted d-block">Tahun Masuk</small>
                                            <span class="font-weight-medium">{{ $dataKuliah->tahun_masuk }}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Status Beasiswa</small>
                                            @if ($dataKuliah->status_beasiswa === 'ya')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-award mr-1"></i>
                                                    {{ $dataKuliah->nama_beasiswa ?: 'Penerima Beasiswa' }}
                                                </span>
                                            @else
                                                <span class="badge badge-light">Tidak Menerima</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-2"></i>
                                        <p class="fs-30 mb-1">Belum Lengkap</p>
                                        <p>Data pendidikan belum tersedia</p>

                                        <button class="btn btn-outline-light btn-sm mt-2" data-toggle="modal"
                                            data-target="#kuliahModal">
                                            <i class="fas fa-plus-circle mr-1"></i> Lengkapi Data
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="row">
        <!-- Recommendation/Information Section -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                            Rekomendasi Pekerjaan
                        @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                            Informasi Pekerjaan Anda
                        @else
                            Informasi Pendidikan Anda
                        @endif
                    </h4>

                    @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                        @if (Auth::user()->student->has_completed_questionnaire)
                            <div class="text-center">
                                <h5>Rekomendasi Pekerjaan untuk Anda:</h5>
                                <div class="list-group">
                                    @php
                                        $latestResponse = \App\Models\QuestionnaireResponse::where(
                                            'student_id',
                                            Auth::user()->student->id,
                                        )
                                            ->orderBy('created_at', 'desc')
                                            ->first();

                                        $topRecommendations = $latestResponse
                                            ? $latestResponse->getTopRecommendations(5)
                                            : [];
                                    @endphp

                                    @if (!empty($topRecommendations))
                                        @foreach ($topRecommendations as $recommendation)
                                            <a href="#" class="list-group-item list-group-item-action">
                                                {{ $recommendation['job']->job_title ?? $recommendation['job']->name }}
                                                <span
                                                    class="badge badge-{{ $recommendation['match_percentage'] >= 80
                                                        ? 'success'
                                                        : ($recommendation['match_percentage'] >= 60
                                                            ? 'info'
                                                            : 'primary') }} float-right">{{ number_format($recommendation['match_percentage'], 1) }}%
                                                    Match</span>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-1"></i> Rekomendasi Anda sedang diproses atau
                                            belum tersedia.
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('student.recommendation.show') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-list mr-1"></i> Lihat Semua Rekomendasi
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <img src="{{ asset('admin/images/undraw_career_progress.svg') }}"
                                    style="max-height: 150px;" class="img-fluid mb-3">
                                <h5>Dapatkan Rekomendasi Pekerjaan</h5>
                                <p class="text-muted">Lengkapi kuesioner untuk mendapatkan rekomendasi pekerjaan yang
                                    sesuai
                                    dengan keterampilan dan minat Anda.</p>
                                <a href="{{ url('siswa/questionnaire') }}" class="btn btn-primary">
                                    <i class="fas fa-clipboard-list mr-1"></i> Isi Kuesioner Sekarang
                                </a>
                            </div>
                        @endif
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                        <div class="text-center py-3">
                            <img src="{{ asset('admin/images/undraw_work.svg') }}" style="max-height: 150px;"
                                class="img-fluid mb-3">
                            <h5><i class="fas fa-briefcase mr-2"></i>Status: Sudah Bekerja</h5>

                            @php
                                $dataKerja = \App\Models\DataKerja::where(
                                    'student_id',
                                    Auth::user()->student->id,
                                )->first();
                            @endphp

                            @if ($dataKerja)
                                <div class="card bg-light p-3 mt-3">
                                    <div class="row">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Perusahaan:</strong></p>
                                            <p class="text-muted">{{ $dataKerja->nama_perusahaan ?? 'Belum diisi' }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Posisi:</strong></p>
                                            <p class="text-muted">{{ $dataKerja->posisi ?? 'Belum diisi' }}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Jenis Pekerjaan:</strong></p>
                                            <p class="text-muted">{{ $dataKerja->jenis_pekerjaan ?? 'Belum diisi' }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Mulai Bekerja:</strong></p>
                                            <p class="text-muted">
                                                {{ $dataKerja->tanggal_mulai ? date('d-m-Y', strtotime($dataKerja->tanggal_mulai)) : 'Belum diisi' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($dataKerja->gaji)
                                        <div class="row mt-2">
                                            <div class="col-md-6 text-left border-right">
                                                <p class="mb-1"><strong>Gaji:</strong></p>
                                                <p class="text-muted">Rp
                                                    {{ number_format($dataKerja->gaji, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="col-md-6 text-left">
                                                <p class="mb-1"><strong>Kesesuaian:</strong></p>
                                                <p class="text-muted">
                                                    <span
                                                        class="badge badge-{{ $dataKerja->sesuai_jurusan === 'ya' ? 'success' : 'info' }}">
                                                        {{ $dataKerja->sesuai_jurusan === 'ya' ? 'Sesuai Jurusan' : 'Beda Jurusan' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($dataKerja->kompetensi_dibutuhkan)
                                        <div class="row mt-2">
                                            <div class="col-12 text-left">
                                                <p class="mb-1"><strong>Kompetensi yang Dibutuhkan:</strong></p>
                                                <p class="text-muted">{{ $dataKerja->kompetensi_dibutuhkan }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Data pekerjaan belum lengkap. Silahkan lengkapi data Anda.
                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#kerjaModal">
                                            <i class="fas fa-plus-circle mr-1"></i> Lengkapi Data
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kuliah')
                        <div class="text-center py-3">
                            <img src="{{ asset('admin/images/undraw_education.svg') }}" style="max-height: 150px;"
                                class="img-fluid mb-3">
                            <h5><i class="fas fa-university mr-2"></i>Status: Kuliah</h5>
                            @php
                                $dataKuliah = \App\Models\DataKuliah::where(
                                    'student_id',
                                    Auth::user()->student->id,
                                )->first();
                            @endphp

                            @if ($dataKuliah)
                                <div class="card bg-light p-3 mt-3">
                                    <div class="row">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Perguruan Tinggi:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->nama_pt }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Program Studi:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->jurusan }} ({{ $dataKuliah->jenjang }})
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Tahun Masuk:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->tahun_masuk }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Status Beasiswa:</strong></p>
                                            <p class="text-muted">
                                                @if ($dataKuliah->status_beasiswa === 'ya')
                                                    <span class="badge badge-success">Menerima Beasiswa</span>
                                                    @if ($dataKuliah->nama_beasiswa)
                                                        <small
                                                            class="d-block mt-1">{{ $dataKuliah->nama_beasiswa }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Tidak Menerima Beasiswa</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if ($dataKuliah->prestasi_akademik)
                                        <div class="row mt-2">
                                            <div class="col-12 text-left">
                                                <p class="mb-1"><strong>Prestasi Akademik:</strong></p>
                                                <p class="text-muted">{{ $dataKuliah->prestasi_akademik }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Data pendidikan belum lengkap. Silahkan lengkapi data Anda.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info and Resources Section -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Informasi & Berita</h4>
                        <a href="#" class="text-info">Lihat Semua</a>
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Tips Mencari Kerja</h6>
                                <small class="text-muted">3 hari yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Panduan lengkap untuk lulusan baru dalam mencari pekerjaan
                                pertama.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Info Beasiswa Perguruan Tinggi</h6>
                                <small class="text-muted">1 minggu yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Informasi tentang berbagai program beasiswa untuk melanjutkan
                                pendidikan.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Workshop Persiapan Karir</h6>
                                <small class="text-muted">2 minggu yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Workshop online untuk mempersiapkan karir setelah lulus.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk belum kerja - Dengan pengecekan has_completed_questionnaire -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire)
        <!-- Modal Rekomendasi Pekerjaan - Kondisi disederhanakan -->
        <div class="modal fade" id="rekomendasiModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-lightbulb mr-2"></i>Rekomendasi Pekerjaan
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-4">
                            <h4 class="font-weight-bold">Butuh Saran Karir?</h4>
                            <p class="text-muted">
                                Kami akan membantu Anda menemukan pekerjaan yang sesuai dengan kemampuan dan minat Anda
                                melalui kuesioner singkat.
                            </p>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dengan mengisi kuesioner, Anda akan mendapatkan rekomendasi pekerjaan yang lebih akurat.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="btnSkipRekomendasi2">
                            <i class="fas fa-times mr-2"></i>Nanti Saja
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" id="btnMauRekomendasi">
                            <i class="fas fa-check mr-2"></i>Dapatkan Rekomendasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal input data kuliah jika status lulus dan kuliah -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'kuliah' &&
            !Auth::user()->student->is_profile_complete)
        {{-- Modal untuk Data Kuliah --}}
        <div class="modal fade" id="kuliahModal" tabindex="-1" role="dialog" aria-labelledby="kuliahModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="kuliahForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kuliahModalLabel">Lengkapi Data Kuliah Anda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Perguruan Tinggi</label>
                                <input type="text" class="form-control" name="nama_pt" placeholder="Universitas XXX"
                                    required>
                            </div>
                            <input type="hidden" name="status_setelah_lulus" value="kuliah">

                            <div class="form-group">
                                <label>Jurusan</label>
                                <input type="text" class="form-control" name="jurusan"
                                    placeholder="Teknik Informatika, RPL" required>
                            </div>
                            <div class="form-group">
                                <label>Jenjang</label>
                                <select class="form-control" name="jenjang" required>
                                    <option value="" disabled selected hidden>Pilih Jenjang</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun Masuk</label>
                                <input type="number" class="form-control" name="tahun_masuk" placeholder="2023"
                                    min="1900" max="2099" step="1" required>
                            </div>
                            <div class="form-group">
                                <label>Status Beasiswa</label>
                                <select class="form-control" name="status_beasiswa" id="status_beasiswa" required>
                                    <option value="" disabled selected hidden>Pilih Status Beasiswa</option>
                                    <option value="ya">Ya</option>
                                    <option value="tidak">Tidak</option>
                                </select>
                            </div>

                            <div class="form-group" id="beasiswa_nama_group" style="display: none;">
                                <label>Nama Beasiswa</label>
                                <input type="text" class="form-control" placeholder="BEASISWA KIP"
                                    name="nama_beasiswa">
                            </div>
                            <div class="form-group mt-3">
                                <label>Prestasi Akademik</label>
                                <textarea class="form-control" name="prestasi_akademik" rows="3"
                                    placeholder="Contoh: Juara 1 Lomba Karya Tulis Ilmiah, IPK 3.9, Sertifikasi TOEFL 600, dll"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal input data kerja jika status kerja -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'kerja' &&
            !Auth::user()->student->is_profile_complete)
        <div class="modal fade" id="kerjaModal" tabindex="-1" role="dialog" aria-labelledby="kerjaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="kerjaForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kerjaModalLabel">Lengkapi Data Pekerjaan Anda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Perusahaan</label>
                                <input type="text" class="form-control" name="nama_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label>Posisi</label>
                                <input type="text" class="form-control" name="posisi" required>
                            </div>
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <input type="text" class="form-control" name="jenis_pekerjaan" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                            <div class="form-group">
                                <label>Gaji (opsional)</label>
                                <input type="number" class="form-control" name="gaji">
                            </div>
                            <div class="form-group">
                                <label>Pekerjaan Sesuai Jurusan?</label>
                                <select class="form-control" name="sesuai_jurusan" required>
                                    <option value="ya">Ya</option>
                                    <option value="tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kompetensi yang Dibutuhkan (opsional)</label>
                                <textarea class="form-control" name="kompetensi_dibutuhkan" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Rekomendasi Pekerjaan -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire)
        <div class="modal fade" id="rekomendasiModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-lightbulb mr-2"></i>Rekomendasi Pekerjaan
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-4">
                            <h4 class="font-weight-bold">Butuh Saran Karir?</h4>
                            <p class="text-muted">
                                Kami dapat membantu Anda menemukan pekerjaan yang sesuai dengan kemampuan dan minat Anda
                                melalui kuesioner singkat.
                            </p>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dengan mengisi kuesioner, Anda akan mendapatkan rekomendasi pekerjaan yang lebih akurat.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="btnSkipRekomendasi">
                            <i class="fas fa-times mr-2"></i>Nanti Saja
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" id="btnMauRekomendasi">
                            <i class="fas fa-check mr-2"></i>Dapatkan Rekomendasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        /* Enhanced Card Styling with Modern Effects */
        .card-tale,
        .card-dark-blue,
        .card-light-blue,
        .card-light-danger {
            background: linear-gradient(135deg, #6254e7, #9289f1);
            color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(98, 84, 231, 0.2);
            transition: all 0.3s ease;
            height: 100%;
            /* Make all cards same height */
            margin-bottom: 0;
            /* Remove bottom margin */
            display: flex;
            flex-direction: column;
        }

        .card-dark-blue {
            background: linear-gradient(135deg, #0062ff, #6cb3ff);
            box-shadow: 0 6px 18px rgba(0, 98, 255, 0.2);
        }

        .card-light-blue {
            background: linear-gradient(135deg, #0998d3, #5be6ff);
            box-shadow: 0 6px 18px rgba(9, 152, 211, 0.2);
        }

        .card-light-danger {
            background: linear-gradient(135deg, #ff7e54, #ffaa8a);
            box-shadow: 0 6px 18px rgba(255, 126, 84, 0.2);
        }

        /* Card body to fill available space */
        .card-body {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            padding-bottom: 1.25rem;
            /* Consistent padding */
        }

        /* Remove extra spacing from the row and stretch-card */
        .grid-margin.transparent {
            margin-bottom: 0;
        }

        .stretch-card {
            margin-bottom: 1.5rem;
            /* Consistent spacing between cards */
            display: flex;
        }

        .stretch-card>.card {
            width: 100%;
        }

        /* Hover effects for cards */
        .card-tale:hover,
        .card-dark-blue:hover,
        .card-light-blue:hover,
        .card-light-danger:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Attention card styling - for important information */
        .card-attention {
            background: linear-gradient(135deg, #ffad00, #ff7e00);
            color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(255, 173, 0, 0.2);
            border-left: 5px solid #ff5500;
            transition: all 0.3s ease;
        }

        /* Enhanced badge styling */
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            border-radius: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-success {
            background: linear-gradient(to right, #28a745, #4fd375) !important;
            border: none;
        }

        .badge-info {
            background: linear-gradient(to right, #17a2b8, #4cd3e9) !important;
            border: none;
        }

        .badge-warning {
            background: linear-gradient(to right, #ffc107, #ffdb6b) !important;
            border: none;
            color: #212529 !important;
        }

        .badge-light {
            background: linear-gradient(to right, #f8f9fa, #ffffff) !important;
            border: 1px solid #e9ecef;
            color: #333 !important;
        }

        /* Education section styling */
        .education-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        /* Typography improvements */
        .card-body p.fs-30 {
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .card-body .text-muted {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Button enhancements */
        .btn-outline-light {
            border-width: 2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Tampilkan modal konfirmasi untuk siswa dengan status belum kerja
            @if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                $('#konfirmasiRekomendasiModal').modal('show');

                // Handle tombol "Ya, Saya Mau"
                $('#btnGetRekomendasi').click(function() {
                    $('#konfirmasiRekomendasiModal').modal('hide');
                    window.location.href = _baseURL + 'student/kuis';
                });

                // Handle tombol "Nanti Saja"
                $('#btnSkipRekomendasi, #btnSkipRekomendasi2').click(function() {
                    $('#konfirmasiRekomendasiModal').modal('hide');
                    $('#rekomendasiModal').modal('hide');
                    $('#belumKerjaModal').modal('show');
                });

                // Handler untuk tombol di modal rekomendasi
                $('#btnMauRekomendasi').click(function() {
                    window.location.href = _baseURL + 'student/kuis';
                });
            @endif

            // Show recommendation modal for unemployed users
            @if (Auth::check() &&
                    Auth::user()->role === 'siswa' &&
                    Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
                    !Auth::user()->student->has_completed_questionnaire)

                // Show recommendation modal first
                $('#rekomendasiModal').modal('show');

                // Handle recommendation buttons
                $('#btnMauRekomendasi').click(function() {
                    window.location.href = _baseURL + 'siswa/questionnaire';
                });

                $('#btnSkipRekomendasi').click(function() {
                    $('#rekomendasiModal').modal('hide');
                    // Show form for those who skip recommendation
                    $('#belumKerjaModal').modal('show');
                });
            @endif

            // ======= SHOW MODAL SESUAI STATUS =======
            @if (Auth::check() && Auth::user()->role === 'siswa')
                @if (!Auth::user()->student->is_profile_complete)
                    @if (Auth::user()->student->status_setelah_lulus === 'kuliah')
                        $('#kuliahModal').modal('show');
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                        $('#kerjaModal').modal('show');
                    @endif
                @endif
            @endif

            // ======= HANDLE BEASISWA (KHUSUS KULIAH) =======
            $('#status_beasiswa').on('change', function() {
                const beasiswaGroup = $('#beasiswa_nama_group');
                const beasiswaInput = beasiswaGroup.find('input');

                if ($(this).val() === 'ya') {
                    beasiswaGroup.slideDown();
                    beasiswaInput.attr('required', true);
                } else {
                    beasiswaGroup.slideUp();
                    beasiswaInput.removeAttr('required').val('');
                }
            });

            // ======= SUBMIT DATA KULIAH =======
            const kuliahForm = $('#kuliahForm');
            if (kuliahForm.length) {
                kuliahForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: kuliahForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#kuliahModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data kuliah berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data kuliah.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data kuliah.'
                            });
                        }
                    });
                });
            }

            // ======= SUBMIT DATA KERJA =======
            const kerjaForm = $('#kerjaForm');
            if (kerjaForm.length) {
                kerjaForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: kerjaForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#kerjaModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data kerja berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data kerja.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data kerja.'
                            });
                        }
                    });
                });
            }

            // ======= SUBMIT DATA BELUM KERJA =======
            const belumKerjaForm = $('#belumKerjaForm');
            if (belumKerjaForm.length) {
                belumKerjaForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: belumKerjaForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#belumKerjaModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data belum kerja berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data belum kerja.'
                            });
                        }
                    });
                });
            }
        });
    </script>
@endpush
