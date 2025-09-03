@extends('layout.app')

@section('title', 'Laporan Detail Tracer Study')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="font-weight-bold mb-0">
                                @switch($reportType)
                                    @case('employment')
                                        📊 Laporan Data Pekerjaan Alumni
                                    @break

                                    @case('education')
                                        🎓 Laporan Data Pendidikan Alumni
                                    @break

                                    @case('unemployment')
                                        📋 Laporan Alumni Belum Bekerja
                                    @break

                                    @default
                                        📈 Laporan Umum Alumni
                                @endswitch
                            </h4>
                            <p class="text-muted">Detail data berdasarkan kriteria yang telah dipilih</p>
                        </div>
                        <div>
                            <div class="btn-group">
                                <a href="{{ route('operator.reports.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-gradient-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                        <i class="mdi mdi-download"></i> Export Laporan
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('operator.reports.generate', array_merge($filters, ['format' => 'pdf', 'report_type' => $reportType])) }}"
                                            class="dropdown-item" target="_blank">
                                            <i class="mdi mdi-file-pdf text-danger mr-2"></i>
                                            <span>Export PDF</span>
                                            <small class="text-muted d-block">Format siap cetak & share</small>
                                        </a>
                                        <a href="{{ route('operator.reports.generate', array_merge($filters, ['format' => 'excel', 'report_type' => $reportType])) }}"
                                            class="dropdown-item">
                                            <i class="mdi mdi-file-excel text-success mr-2"></i>
                                            <span>Export Excel</span>
                                            <small class="text-muted d-block">Data untuk analisis lebih lanjut</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Summary Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-filter-variant mr-2"></i>Kriteria & Ringkasan Laporan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <div class="info-icon bg-primary">
                                            <i class="mdi mdi-file-document text-white"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="mb-1">Tipe Laporan</h6>
                                            @switch($reportType)
                                                @case('employment')
                                                    <span class="badge badge-lg badge-success">💼 Data Pekerjaan</span>
                                                @break

                                                @case('education')
                                                    <span class="badge badge-lg badge-info">🎓 Data Pendidikan</span>
                                                @break

                                                @case('unemployment')
                                                    <span class="badge badge-lg badge-warning">📋 Belum Bekerja</span>
                                                @break

                                                @default
                                                    <span class="badge badge-lg badge-primary">📈 Laporan Umum</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <div class="info-icon bg-info">
                                            <i class="mdi mdi-calendar text-white"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="mb-1">Tahun Kelulusan</h6>
                                            <p class="mb-0">{{ $filters['year'] ?? '🕐 Semua Tahun' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <div class="info-icon bg-success">
                                            <i class="mdi mdi-school text-white"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="mb-1">Jurusan</h6>
                                            <p class="mb-0">{{ $filters['department'] ?? '🎯 Semua Jurusan' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <div class="info-icon bg-warning">
                                            <i class="mdi mdi-account-group text-white"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="mb-1">Total Data</h6>
                                            <p class="mb-0 font-weight-bold">{{ count($students) }} alumni</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Statistics Summary -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="summary-stats">
                                        @php
                                            $totalStudents = count($students);
                                            $workingCount = collect($students)
                                                ->where('status_setelah_lulus', 'kerja')
                                                ->count();
                                            $studyingCount = collect($students)
                                                ->where('status_setelah_lulus', 'kuliah')
                                                ->count();
                                            $unemployedCount = collect($students)
                                                ->where('status_setelah_lulus', 'belum_kerja')
                                                ->count();

                                            $workingPercentage =
                                                $totalStudents > 0
                                                    ? round(($workingCount / $totalStudents) * 100, 1)
                                                    : 0;
                                            $studyingPercentage =
                                                $totalStudents > 0
                                                    ? round(($studyingCount / $totalStudents) * 100, 1)
                                                    : 0;
                                            $unemployedPercentage =
                                                $totalStudents > 0
                                                    ? round(($unemployedCount / $totalStudents) * 100, 1)
                                                    : 0;
                                        @endphp

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="stat-card bg-gradient-success">
                                                    <div class="stat-content">
                                                        <div class="stat-number">{{ $workingCount }}</div>
                                                        <div class="stat-label">Bekerja</div>
                                                        <div class="stat-percentage">{{ $workingPercentage }}%</div>
                                                    </div>
                                                    <div class="stat-icon">
                                                        <i class="mdi mdi-briefcase"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card bg-gradient-info">
                                                    <div class="stat-content">
                                                        <div class="stat-number">{{ $studyingCount }}</div>
                                                        <div class="stat-label">Kuliah</div>
                                                        <div class="stat-percentage">{{ $studyingPercentage }}%</div>
                                                    </div>
                                                    <div class="stat-icon">
                                                        <i class="mdi mdi-school"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card bg-gradient-warning">
                                                    <div class="stat-content">
                                                        <div class="stat-number">{{ $unemployedCount }}</div>
                                                        <div class="stat-label">Belum Kerja</div>
                                                        <div class="stat-percentage">{{ $unemployedPercentage }}%</div>
                                                    </div>
                                                    <div class="stat-icon">
                                                        <i class="mdi mdi-account-search"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card bg-gradient-primary">
                                                    <div class="stat-content">
                                                        <div class="stat-number">{{ $totalStudents }}</div>
                                                        <div class="stat-label">Total Alumni</div>
                                                        <div class="stat-percentage">100%</div>
                                                    </div>
                                                    <div class="stat-icon">
                                                        <i class="mdi mdi-account-group"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border-left-primary mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="mdi mdi-information text-primary mr-2"></i>
                                                <strong>Info Laporan:</strong> Data diambil berdasarkan filter yang dipilih
                                            </div>
                                            <div class="text-muted">
                                                <i class="mdi mdi-clock mr-1"></i>
                                                <small>Generated: {{ now()->format('d M Y, H:i') }} WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Report Data -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="mdi mdi-table-large mr-2"></i>
                                    @switch($reportType)
                                        @case('employment')
                                            📊 Data Pekerjaan Alumni
                                        @break

                                        @case('education')
                                            🎓 Data Pendidikan Alumni
                                        @break

                                        @case('unemployment')
                                            📋 Data Alumni Belum Bekerja
                                        @break

                                        @default
                                            📈 Data Umum Alumni
                                    @endswitch
                                </h5>
                                <div class="header-actions">
                                    <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                                        <i class="mdi mdi-printer mr-1"></i>Print
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm ml-2" onclick="toggleTableView()">
                                        <i class="mdi mdi-view-grid mr-1"></i>Toggle View
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if (count($students) > 0)
                                <!-- Enhanced Table Container -->
                                <div class="enhanced-table-container">
                                    @if ($reportType == 'employment')
                                        <!-- Enhanced Employment Report Table -->
                                        <div class="table-responsive">
                                            <table class="table table-hover enhanced-table mb-0">
                                                <thead class="enhanced-thead">
                                                    <tr>
                                                        <th class="text-center" width="5%">#</th>
                                                        <th width="20%">
                                                            <i class="mdi mdi-account mr-1"></i>Alumni
                                                        </th>
                                                        <th width="15%">
                                                            <i class="mdi mdi-school mr-1"></i>Jurusan
                                                        </th>
                                                        <th class="text-center" width="10%">
                                                            <i class="mdi mdi-calendar mr-1"></i>Lulus
                                                        </th>
                                                        <th width="20%">
                                                            <i class="mdi mdi-office-building mr-1"></i>Perusahaan
                                                        </th>
                                                        <th width="15%">
                                                            <i class="mdi mdi-briefcase mr-1"></i>Posisi
                                                        </th>
                                                        <th width="15%">
                                                            <i class="mdi mdi-tag mr-1"></i>Jenis Kerja
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $i = 1; @endphp
                                                    @foreach ($students as $student)
                                                        @php
                                                            $dataKerja = $reportData
                                                                ->where('student_id', $student->id)
                                                                ->first();
                                                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                        @endphp
                                                        @if ($dataKerja)
                                                            <tr class="enhanced-row">
                                                                <td class="text-center">
                                                                    <div class="row-number">{{ $i++ }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="student-info">
                                                                        <div class="student-name">
                                                                            {{ $student->nama_lengkap }}</div>
                                                                        <small class="student-meta">
                                                                            <i
                                                                                class="mdi mdi-card-account-details mr-1"></i>
                                                                            NISN:
                                                                            {{ $student->nisn ?? ($student->nis ?? '-') }}
                                                                        </small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="department-badge">
                                                                        {{ $jurusan ? $jurusan->nama : '-' }}
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="year-badge">
                                                                        {{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="company-info">
                                                                        <div class="company-name">
                                                                            {{ $dataKerja->nama_perusahaan ?? '-' }}</div>
                                                                        @if ($dataKerja->tanggal_mulai)
                                                                            <small class="work-start">
                                                                                <i class="mdi mdi-calendar-start mr-1"></i>
                                                                                Mulai:
                                                                                {{ date('M Y', strtotime($dataKerja->tanggal_mulai)) }}
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="position-info">
                                                                        <div class="position-title">
                                                                            {{ $dataKerja->posisi ?? '-' }}</div>
                                                                        @if ($dataKerja->sesuai_jurusan)
                                                                            <small class="compatibility">
                                                                                @if ($dataKerja->sesuai_jurusan == 'ya')
                                                                                    <span
                                                                                        class="badge badge-success badge-sm">
                                                                                        <i
                                                                                            class="mdi mdi-check mr-1"></i>Sesuai
                                                                                        Jurusan
                                                                                    </span>
                                                                                @else
                                                                                    <span
                                                                                        class="badge badge-warning badge-sm">
                                                                                        <i
                                                                                            class="mdi mdi-alert mr-1"></i>Tidak
                                                                                        Sesuai
                                                                                    </span>
                                                                                @endif
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="job-type-badge">
                                                                        {{ $dataKerja->jenis_pekerjaan ?? '-' }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif($reportType == 'education')
                                        <!-- Enhanced Education Report Table -->
                                        <div class="table-responsive">
                                            <table class="table table-hover enhanced-table mb-0">
                                                <thead class="enhanced-thead">
                                                    <tr>
                                                        <th class="text-center" width="5%">#</th>
                                                        <th width="20%">
                                                            <i class="mdi mdi-account mr-1"></i>Alumni
                                                        </th>
                                                        <th width="15%">
                                                            <i class="mdi mdi-school mr-1"></i>Jurusan SMK
                                                        </th>
                                                        <th class="text-center" width="10%">
                                                            <i class="mdi mdi-calendar mr-1"></i>Lulus
                                                        </th>
                                                        <th width="20%">
                                                            <i class="mdi mdi-school-outline mr-1"></i>Perguruan Tinggi
                                                        </th>
                                                        <th width="15%">
                                                            <i class="mdi mdi-book-open mr-1"></i>Program Studi
                                                        </th>
                                                        <th class="text-center" width="10%">
                                                            <i class="mdi mdi-chart-line mr-1"></i>Jenjang
                                                        </th>
                                                        <th class="text-center" width="10%">
                                                            <i class="mdi mdi-calendar-check mr-1"></i>Masuk
                                                        </th>
                                                        <th class="text-center" width="15%">
                                                            <i class="mdi mdi-star mr-1"></i>Beasiswa
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $i = 1; @endphp
                                                    @foreach ($students as $student)
                                                        @php
                                                            $dataKuliah = $reportData
                                                                ->where('student_id', $student->id)
                                                                ->first();
                                                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                        @endphp
                                                        @if ($dataKuliah)
                                                            <tr class="enhanced-row">
                                                                <td class="text-center">
                                                                    <div class="row-number">{{ $i++ }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="student-info">
                                                                        <div class="student-name">
                                                                            {{ $student->nama_lengkap }}</div>
                                                                        <div class="student-details">
                                                                            <small class="student-meta">
                                                                                <i
                                                                                    class="mdi mdi-card-account-details mr-1"></i>
                                                                                NISN:
                                                                                {{ $student->nisn ?? ($student->nis ?? '-') }}
                                                                            </small>
                                                                            <small class="student-meta d-block">
                                                                                <i
                                                                                    class="mdi mdi-gender-{{ strtolower($student->jenis_kelamin) == 'perempuan' ? 'female' : 'male' }} mr-1"></i>
                                                                                {{ ucfirst($student->jenis_kelamin ?? '-') }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="department-badge">
                                                                        {{ $jurusan ? $jurusan->nama : '-' }}
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="year-badge">
                                                                        {{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="university-info">
                                                                        <div class="university-name">
                                                                            {{ $dataKuliah->nama_pt ?? '-' }}</div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="major-info">
                                                                        {{ $dataKuliah->jurusan ?? '-' }}
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $jenjangColors = [
                                                                            'D3' => 'primary',
                                                                            'D4' => 'info',
                                                                            'S1' => 'success',
                                                                            'S2' => 'warning',
                                                                            'S3' => 'danger',
                                                                        ];
                                                                        $jenjang = $dataKuliah->jenjang ?? '-';
                                                                        $badgeColor =
                                                                            $jenjangColors[$jenjang] ?? 'secondary';
                                                                    @endphp
                                                                    @if ($jenjang != '-')
                                                                        <span
                                                                            class="badge badge-{{ $badgeColor }}">{{ $jenjang }}</span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="year-badge">
                                                                        {{ $dataKuliah->tahun_masuk ?? '-' }}
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (($dataKuliah->status_beasiswa ?? '') == 'ya')
                                                                        <span class="badge badge-success">
                                                                            <i class="mdi mdi-star mr-1"></i>Beasiswa
                                                                        </span>
                                                                        @if ($dataKuliah->nama_beasiswa)
                                                                            <small
                                                                                class="d-block text-muted mt-1">{{ $dataKuliah->nama_beasiswa }}</small>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge badge-secondary">
                                                                            <i class="mdi mdi-close mr-1"></i>Mandiri
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif($reportType == 'unemployment')
                                        <!-- Enhanced Unemployment Report Table -->
                                        <div class="table-responsive">
                                            <table class="table table-hover enhanced-table mb-0">
                                                <thead class="enhanced-thead">
                                                    <tr>
                                                        <th class="text-center" width="5%">#</th>
                                                        <th width="25%">
                                                            <i class="mdi mdi-account mr-1"></i>Alumni
                                                        </th>
                                                        <th width="20%">
                                                            <i class="mdi mdi-school mr-1"></i>Jurusan
                                                        </th>
                                                        <th class="text-center" width="10%">
                                                            <i class="mdi mdi-calendar mr-1"></i>Lulus
                                                        </th>
                                                        <th class="text-center" width="15%">
                                                            <i class="mdi mdi-clock mr-1"></i>Durasi Lulus
                                                        </th>
                                                        <th width="25%">
                                                            <i class="mdi mdi-map-marker mr-1"></i>Alamat
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $i = 1; @endphp
                                                    @foreach ($reportData as $student)
                                                        @php
                                                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);

                                                            // Calculate duration since graduation
                                                            $duration = '-';
                                                            if ($student->tanggal_lulus) {
                                                                $graduationDate = \Carbon\Carbon::parse(
                                                                    $student->tanggal_lulus,
                                                                );
                                                                $now = \Carbon\Carbon::now();
                                                                $durationMonths = $graduationDate->diffInMonths($now);
                                                                if ($durationMonths < 12) {
                                                                    $duration = $durationMonths . ' bulan';
                                                                } else {
                                                                    $years = floor($durationMonths / 12);
                                                                    $months = $durationMonths % 12;
                                                                    $duration =
                                                                        $years .
                                                                        ' tahun ' .
                                                                        ($months > 0 ? $months . ' bulan' : '');
                                                                }
                                                            }
                                                        @endphp
                                                        <tr class="enhanced-row">
                                                            <td class="text-center">
                                                                <div class="row-number">{{ $i++ }}</div>
                                                            </td>
                                                            <td>
                                                                <div class="student-info">
                                                                    <div class="student-name">{{ $student->nama_lengkap }}
                                                                    </div>
                                                                    <div class="student-details">
                                                                        <small class="student-meta">
                                                                            <i
                                                                                class="mdi mdi-card-account-details mr-1"></i>
                                                                            NISN:
                                                                            {{ $student->nisn ?? ($student->nis ?? '-') }}
                                                                        </small>
                                                                        <small class="student-meta d-block">
                                                                            <i
                                                                                class="mdi mdi-gender-{{ strtolower($student->jenis_kelamin) == 'perempuan' ? 'female' : 'male' }} mr-1"></i>
                                                                            {{ ucfirst($student->jenis_kelamin ?? '-') }}
                                                                        </small>
                                                                        @if ($student->no_hp)
                                                                            <small class="student-meta d-block">
                                                                                <i class="mdi mdi-phone mr-1"></i>
                                                                                {{ $student->no_hp }}
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="department-badge">
                                                                    {{ $jurusan ? $jurusan->nama : '-' }}
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="year-badge">
                                                                    {{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="duration-info">
                                                                    <span
                                                                        class="duration-badge 
                                                                        @if (str_contains($duration, 'tahun')) badge-danger
                                                                        @elseif(intval($duration) > 6)
                                                                            badge-warning
                                                                        @else
                                                                            badge-info @endif
                                                                    ">
                                                                        {{ $duration }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="address-info">
                                                                    {{ $student->alamat ?? '-' }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        $now = \Carbon\Carbon::now();
                                        $durationMonths = $graduationDate->diffInMonths($now);

                                        if ($durationMonths < 12) { $duration=$durationMonths . ' bulan' ; } else {
                                            $years=floor($durationMonths / 12); $months=$durationMonths % 12;
                                            $duration=$years . ' tahun ' . ($months> 0 ? $months . ' bulan' : '');
                                            }
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $student->nama_lengkap }}</td>
                                                <td>{{ $jurusan ? $jurusan->nama : '-' }}</td>
                                                <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                                </td>
                                                <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                                                <td>{{ $duration }}</td>
                                                <td>{{ $student->alamat ?? '-' }}</td>
                                                <td>{{ $student->no_hp ?? '-' }}</td>
                                            </tr>
                                    @endforeach
                                    </tbody>
                                    </table>
                                @else
                                    <!-- Enhanced General Report Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover enhanced-table mb-0">
                                            <thead class="enhanced-thead">
                                                <tr>
                                                    <th class="text-center" width="5%">#</th>
                                                    <th width="25%">
                                                        <i class="mdi mdi-account mr-1"></i>Alumni
                                                    </th>
                                                    <th width="15%">
                                                        <i class="mdi mdi-school mr-1"></i>Jurusan
                                                    </th>
                                                    <th class="text-center" width="10%">
                                                        <i class="mdi mdi-calendar mr-1"></i>Lulus
                                                    </th>
                                                    <th class="text-center" width="15%">
                                                        <i class="mdi mdi-chart-line mr-1"></i>Status
                                                    </th>
                                                    <th class="text-center" width="10%">
                                                        <i class="mdi mdi-gender-male-female mr-1"></i>Gender
                                                    </th>
                                                    <th width="20%">
                                                        <i class="mdi mdi-map-marker mr-1"></i>Alamat
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i = 1; @endphp
                                                @foreach ($reportData as $student)
                                                    @php
                                                        $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                    @endphp
                                                    <tr class="enhanced-row">
                                                        <td class="text-center">
                                                            <div class="row-number">{{ $i++ }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="student-info">
                                                                <div class="student-name">{{ $student->nama_lengkap }}
                                                                </div>
                                                                <div class="student-details">
                                                                    <small class="student-meta">
                                                                        <i class="mdi mdi-card-account-details mr-1"></i>
                                                                        NISN: {{ $student->nisn ?? '-' }}
                                                                    </small>
                                                                    @if ($student->no_hp)
                                                                        <small class="student-meta d-block">
                                                                            <i class="mdi mdi-phone mr-1"></i>
                                                                            {{ $student->no_hp }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="department-badge">
                                                                {{ $jurusan ? $jurusan->nama : '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="year-badge">
                                                                {{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($student->status_setelah_lulus == 'kerja')
                                                                <span class="badge badge-success">
                                                                    <i class="mdi mdi-briefcase mr-1"></i>Bekerja
                                                                </span>
                                                            @elseif($student->status_setelah_lulus == 'kuliah')
                                                                <span class="badge badge-info">
                                                                    <i class="mdi mdi-school mr-1"></i>Kuliah
                                                                </span>
                                                            @else
                                                                <span class="badge badge-warning">
                                                                    <i class="mdi mdi-clock mr-1"></i>Belum Bekerja
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span
                                                                class="gender-badge {{ strtolower($student->jenis_kelamin) == 'perempuan' ? 'female' : 'male' }}">
                                                                <i
                                                                    class="mdi mdi-gender-{{ strtolower($student->jenis_kelamin) == 'perempuan' ? 'female' : 'male' }} mr-1"></i>
                                                                {{ ucfirst($student->jenis_kelamin ?? '-') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="address-info">
                                                                {{ $student->alamat ?? '-' }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="mdi mdi-database-remove"></i>
                        <h4>Tidak Ada Data Ditemukan</h4>
                        <p>Tidak ada data alumni yang sesuai dengan kriteria yang Anda pilih.</p>
                        <div class="mt-3">
                            <small class="text-muted">
                                💡 <strong>Saran:</strong> Coba ubah filter kriteria atau periksa kembali pengaturan laporan
                            </small>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('operator.reports.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-arrow-left mr-1"></i>Kembali ke Filter
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Representations Section -->
    @if (count($students) > 0)
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Visualisasi Data</h5>
                        <div class="chart-container" style="height: 300px; max-height: 300px; overflow: hidden;">
                            @if ($reportType == 'employment')
                                <canvas id="employmentChart"></canvas>
                            @elseif($reportType == 'education')
                                <canvas id="educationChart"></canvas>
                            @elseif($reportType == 'unemployment')
                                <canvas id="unemploymentChart"></canvas>
                            @else
                                <canvas id="generalChart"></canvas>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ringkasan Statistik</h5>
                        <div class="statistics-container" style="max-height: 300px; overflow-y: auto;">
                            @if ($reportType == 'employment')
                                <!-- Employment Statistics -->
                                @php
                                    $matchCount = $reportData->where('sesuai_jurusan', 'ya')->count();
                                    $notMatchCount = $reportData->where('sesuai_jurusan', 'tidak')->count();
                                    $totalCount = $matchCount + $notMatchCount;
                                    $matchPercentage = $totalCount > 0 ? round(($matchCount / $totalCount) * 100) : 0;
                                @endphp

                                <div class="alert alert-info mb-4">
                                    <h6 class="alert-heading">Kesesuaian dengan Jurusan</h6>
                                    <div class="progress mt-2 mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $matchPercentage }}%">
                                            {{ $matchPercentage }}% Sesuai
                                        </div>
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            style="width: {{ 100 - $matchPercentage }}%">
                                            {{ 100 - $matchPercentage }}% Tidak
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span>Sesuai Jurusan: {{ $matchCount }} alumni</span>
                                        <span>Tidak Sesuai: {{ $notMatchCount }} alumni</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Job Types - Limited display -->
                                    <div class="col-md-6">
                                        <div class="card border-primary mb-3">
                                            <div class="card-body text-primary">
                                                <h5 class="card-title">Jenis Pekerjaan</h5>
                                                <ul class="list-group list-group-flush"
                                                    style="max-height: 150px; overflow-y: auto;">
                                                    @php
                                                        $jobTypes = $reportData
                                                            ->groupBy('jenis_pekerjaan')
                                                            ->map->count();
                                                        $sortedJobTypes = $jobTypes->sortDesc();
                                                    @endphp

                                                    @foreach ($sortedJobTypes->take(5) as $jobType => $count)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $jobType ?: 'Tidak diketahui' }}
                                                            <span
                                                                class="badge badge-primary badge-pill">{{ $count }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if ($sortedJobTypes->count() > 5)
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted">Menampilkan 5 dari
                                                            {{ $sortedJobTypes->count() }} jenis pekerjaan</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Average Wait Time -->
                                    <div class="col-md-6">
                                        <div class="card border-success mb-3">
                                            <div class="card-body text-success">
                                                <h5 class="card-title">Rata-rata Waktu Tunggu</h5>
                                                @php
                                                    $waitingTimes = [];
                                                    foreach ($reportData as $data) {
                                                        $student = $students->where('id', $data->student_id)->first();
                                                        if (
                                                            $student &&
                                                            $student->tanggal_lulus &&
                                                            $data->tanggal_mulai
                                                        ) {
                                                            $gradDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                                            $startDate = \Carbon\Carbon::parse($data->tanggal_mulai);
                                                            if ($startDate >= $gradDate) {
                                                                $waitingTimes[] = $gradDate->diffInMonths($startDate);
                                                            }
                                                        }
                                                    }

                                                    $avgWaitingTime =
                                                        count($waitingTimes) > 0
                                                            ? array_sum($waitingTimes) / count($waitingTimes)
                                                            : 0;
                                                @endphp

                                                <div class="text-center my-3">
                                                    <h1 class="display-4">{{ number_format($avgWaitingTime, 1) }}
                                                    </h1>
                                                    <p class="lead">Bulan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($reportType == 'education')
                                <!-- Education Statistics - Modified for better space usage -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-info mb-3">
                                            <div class="card-body text-info">
                                                <h5 class="card-title">Jenjang Pendidikan</h5>
                                                <ul class="list-group list-group-flush"
                                                    style="max-height: 150px; overflow-y: auto;">
                                                    @php
                                                        $levels = $reportData->groupBy('jenjang')->map->count();
                                                    @endphp

                                                    @foreach ($levels as $level => $count)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $level ?: 'Tidak diketahui' }}
                                                            <span
                                                                class="badge badge-info badge-pill">{{ $count }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-warning mb-3">
                                            <div class="card-body text-warning">
                                                <h5 class="card-title">Status Beasiswa</h5>
                                                @php
                                                    $scholarshipCount = $reportData
                                                        ->where('status_beasiswa', 'ya')
                                                        ->count();
                                                    $totalCount = count($reportData);
                                                    $scholarshipPercentage =
                                                        $totalCount > 0
                                                            ? round(($scholarshipCount / $totalCount) * 100)
                                                            : 0;
                                                @endphp

                                                <div class="text-center my-3">
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $scholarshipPercentage }}%">
                                                            {{ $scholarshipPercentage }}%
                                                        </div>
                                                    </div>
                                                    <p class="mt-2">
                                                        {{ $scholarshipCount }} dari {{ $totalCount }} alumni
                                                        mendapatkan beasiswa
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <h6 class="alert-heading">Program Studi Terpopuler</h6>
                                    <div style="max-height: 100px; overflow-y: auto;">
                                        <ul class="mb-0 mt-2">
                                            @php
                                                $majors = $reportData->groupBy('jurusan')->map->count()->sortDesc();
                                            @endphp

                                            @foreach ($majors->take(5) as $major => $count)
                                                <li>{{ $major ?: 'Tidak diketahui' }} ({{ $count }} alumni)
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if ($majors->count() > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Menampilkan 5 dari
                                                    {{ $majors->count() }} program studi</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($reportType == 'unemployment')
                                <!-- Unemployment Statistics - More compact presentation -->
                                <div class="alert alert-warning mb-4">
                                    <h6 class="alert-heading">Durasi Pencarian Kerja</h6>
                                    @php
                                        $durations = [];
                                        foreach ($reportData as $student) {
                                            if ($student->tanggal_lulus) {
                                                $gradDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                                $now = \Carbon\Carbon::now();
                                                $durations[] = $gradDate->diffInMonths($now);
                                            }
                                        }

                                        $avgDuration =
                                            count($durations) > 0 ? array_sum($durations) / count($durations) : 0;

                                        // Group by duration ranges
                                        $durationRanges = [
                                            '0-6 bulan' => 0,
                                            '7-12 bulan' => 0,
                                            '1-2 tahun' => 0,
                                            '> 2 tahun' => 0,
                                        ];

                                        foreach ($durations as $duration) {
                                            if ($duration <= 6) {
                                                $durationRanges['0-6 bulan']++;
                                            } elseif ($duration <= 12) {
                                                $durationRanges['7-12 bulan']++;
                                            } elseif ($duration <= 24) {
                                                $durationRanges['1-2 tahun']++;
                                            } else {
                                                $durationRanges['> 2 tahun']++;
                                            }
                                        }
                                    @endphp

                                    <div class="text-center my-3">
                                        <h1 class="display-4">{{ number_format($avgDuration, 1) }}</h1>
                                        <p class="lead">Bulan rata-rata pencarian kerja</p>
                                    </div>

                                    <div class="row mt-3">
                                        @foreach ($durationRanges as $range => $count)
                                            <div class="col-md-3 text-center">
                                                <div class="card bg-light">
                                                    <div class="card-body p-2">
                                                        <h3>{{ $count }}</h3>
                                                        <small>{{ $range }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- General Statistics - Optimized layout -->
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="card border-success mb-3">
                                            <div class="card-body">
                                                <h3 class="text-success">
                                                    {{ $reportData->where('status_setelah_lulus', 'kerja')->count() }}
                                                </h3>
                                                <p>Bekerja</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="card border-info mb-3">
                                            <div class="card-body">
                                                <h3 class="text-info">
                                                    {{ $reportData->where('status_setelah_lulus', 'kuliah')->count() }}
                                                </h3>
                                                <p>Kuliah</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="card border-warning mb-3">
                                            <div class="card-body">
                                                <h3 class="text-warning">
                                                    {{ $reportData->where('status_setelah_lulus', 'belum_kerja')->count() }}
                                                </h3>
                                                <p>Belum Bekerja</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <h6 class="alert-heading">Distribusi Jurusan</h6>
                                    <div style="max-height: 120px; overflow-y: auto;">
                                        <ul class="mb-0 mt-2">
                                            @php
                                                $departmentStats = [];
                                                foreach ($reportData as $student) {
                                                    $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                    $jurusanName = $jurusan ? $jurusan->nama : 'Tidak diketahui';
                                                    if (!isset($departmentStats[$jurusanName])) {
                                                        $departmentStats[$jurusanName] = 0;
                                                    }
                                                    $departmentStats[$jurusanName]++;
                                                }
                                                arsort($departmentStats);
                                            @endphp

                                            @foreach (array_slice($departmentStats, 0, 5) as $dept => $count)
                                                <li>{{ $dept }} ({{ $count }} alumni)</li>
                                            @endforeach
                                        </ul>
                                        @if (count($departmentStats) > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Menampilkan 5 dari
                                                    {{ count($departmentStats) }} jurusan</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            @if (count($students) > 0)
                @if ($reportType == 'employment')
                    // Employment Chart
                    var employmentCtx = document.getElementById('employmentChart').getContext('2d');

                    @php
                        $jobTypes = $reportData->groupBy('jenis_pekerjaan')->map->count()->sortDesc()->take(8);
                    @endphp

                    new Chart(employmentCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($jobTypes->keys()->toArray()) !!},
                            datasets: [{
                                label: 'Jumlah Alumni',
                                data: {!! json_encode($jobTypes->values()->toArray()) !!},
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Alumni'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Jenis Pekerjaan'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Distribusi Jenis Pekerjaan'
                                }
                            }
                        }
                    });
                @elseif ($reportType == 'education')
                    // Education Chart
                    var educationCtx = document.getElementById('educationChart').getContext('2d');

                    @php
                        $educationLevels = $reportData->groupBy('jenjang')->map->count();
                    @endphp

                    new Chart(educationCtx, {
                        type: 'pie',
                        data: {
                            labels: {!! json_encode($educationLevels->keys()->toArray()) !!},
                            datasets: [{
                                data: {!! json_encode($educationLevels->values()->toArray()) !!},
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 99, 132, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Distribusi Jenjang Pendidikan'
                                },
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });
                @elseif ($reportType == 'unemployment')
                    // Unemployment Chart
                    var unemploymentCtx = document.getElementById('unemploymentChart').getContext('2d');

                    @php
                        // Group by duration ranges for chart
                        $durationLabels = ['0-6 bulan', '7-12 bulan', '1-2 tahun', '> 2 tahun'];
                        $durationData = array_values($durationRanges);
                    @endphp

                    new Chart(unemploymentCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($durationLabels) !!},
                            datasets: [{
                                label: 'Jumlah Alumni',
                                data: {!! json_encode($durationData) !!},
                                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Alumni'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Durasi Mencari Kerja'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Distribusi Durasi Mencari Kerja'
                                }
                            }
                        }
                    });
                @else
                    // General Chart
                    var generalCtx = document.getElementById('generalChart').getContext('2d');

                    @php
                        $statusCounts = [$reportData->where('status_setelah_lulus', 'kerja')->count(), $reportData->where('status_setelah_lulus', 'kuliah')->count(), $reportData->where('status_setelah_lulus', 'belum_kerja')->count()];
                    @endphp

                    new Chart(generalCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Bekerja', 'Kuliah', 'Belum Bekerja'],
                            datasets: [{
                                data: {!! json_encode($statusCounts) !!},
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.7)',
                                    'rgba(23, 162, 184, 0.7)',
                                    'rgba(255, 193, 7, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Distribusi Status Alumni'
                                },
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });
                @endif
            @endif
        });

        // Enhanced UI Functions
        function toggleTableView() {
            const table = document.querySelector('.enhanced-table');
            const container = document.querySelector('.enhanced-table-container');

            if (table.classList.contains('compact-view')) {
                table.classList.remove('compact-view');
                container.style.fontSize = '12px';
                showToast('Switched to normal view', 'View Mode', 'info');
            } else {
                table.classList.add('compact-view');
                container.style.fontSize = '10px';
                showToast('Switched to compact view', 'View Mode', 'info');
            }
        }

        // Enhanced notification system
        function showToast(message, title = '', type = 'success') {
            if (typeof toastr !== 'undefined') {
                toastr[type](message, title);
            } else {
                // Fallback for browsers without toastr
                console.log(`${title}: ${message}`);
            }
        }

        // Add smooth scrolling and enhanced interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced row interactions
            const rows = document.querySelectorAll('.enhanced-row');
            rows.forEach((row, index) => {
                // Add sequential animation delay
                row.style.animationDelay = `${index * 0.05}s`;

                // Enhanced click feedback
                row.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('div');
                    ripple.className = 'ripple-effect';
                    ripple.style.left = e.offsetX + 'px';
                    ripple.style.top = e.offsetY + 'px';
                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Enhanced table loading state
            const table = document.querySelector('.enhanced-table');
            if (table) {
                table.classList.add('table-loading');
                setTimeout(() => {
                    table.classList.remove('table-loading');
                    showToast('Table loaded successfully', 'Ready', 'success');
                }, 800);
            }
        });

        // Print functionality with better formatting
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
            showToast('Preparing document for printing...', 'Print', 'info');
        });

        window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
        showToast('Print dialog closed', 'Print', 'info');
        });
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });

        // Add search functionality
        function initTableSearch() {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Cari alumni...';
            searchInput.className = 'form-control form-control-sm';
            searchInput.style.width = '200px';
            searchInput.style.marginRight = '10px';

            const headerActions = document.querySelector('.header-actions');
            if (headerActions) {
                headerActions.insertBefore(searchInput, headerActions.firstChild);

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('.enhanced-row');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        }

        // Initialize search when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initTableSearch();
        });
    </script>
@endpush

@push('styles')
    <style>
        .table-responsive {
            margin-bottom: 1rem;
        }

        .card-title {
            margin-bottom: 1.5rem;
        }

        .alert {
            margin-bottom: 1rem;
        }

        .badge {
            font-size: 85%;
        }

        .progress {
            border-radius: 0.25rem;
        }

        /* Make tables more readable */
        .table th {
            background-color: #f8f9fa;
            white-space: nowrap;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Enhanced Table Styling */
        .enhanced-table-container {
            background: #ffffff;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }

        .enhanced-table {
            margin-bottom: 0;
            background: transparent;
        }

        .enhanced-thead {
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            color: white;
        }

        .enhanced-thead th {
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .enhanced-row {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .enhanced-row:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .enhanced-row td {
            padding: 15px 12px;
            border: none;
            vertical-align: middle;
        }

        /* Row Number Styling */
        .row-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            margin: 0 auto;
        }

        /* Student Info Styling */
        .student-info {
            position: relative;
        }

        .student-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .student-meta {
            color: #6c757d;
            font-size: 11px;
            display: block;
            margin-bottom: 2px;
        }

        .student-details {
            margin-top: 5px;
        }

        /* Badge Styling */
        .department-badge {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            max-width: 100%;
            word-wrap: break-word;
        }

        .year-badge {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }

        .job-type-badge {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
        }

        /* Company Info Styling */
        .company-info .company-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .work-start {
            color: #28a745;
            font-size: 10px;
            font-weight: 500;
        }

        /* Position Info Styling */
        .position-info .position-title {
            font-weight: 600;
            color: #495057;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .compatibility {
            margin-top: 3px;
        }

        .badge-sm {
            padding: 3px 8px;
            font-size: 9px;
            border-radius: 10px;
        }

        /* Duration Badge Styling */
        .duration-info {
            text-align: center;
        }

        .duration-badge {
            padding: 8px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
            color: white;
        }

        .duration-badge.badge-info {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
        }

        .duration-badge.badge-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .duration-badge.badge-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }

        /* Address Info */
        .address-info {
            font-size: 12px;
            color: #495057;
            line-height: 1.4;
        }

        /* Gender Badge Styling */
        .gender-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .gender-badge.male {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .gender-badge.female {
            background: linear-gradient(135deg, #e83e8c 0%, #d91a72 100%);
            color: white;
        }

        /* University Info Styling */
        .university-info .university-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 3px;
        }

        /* Major Info Styling */
        .major-info {
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
        }

        /* Year Badge Enhanced */
        .year-badge {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        /* Compact View */
        .enhanced-table.compact-view {
            font-size: 10px;
        }

        .enhanced-table.compact-view .student-name {
            font-size: 11px;
        }

        .enhanced-table.compact-view .student-meta {
            font-size: 9px;
        }

        .enhanced-table.compact-view .enhanced-row td {
            padding: 8px 6px;
        }

        .enhanced-table.compact-view .row-number {
            width: 24px;
            height: 24px;
            font-size: 10px;
        }

        /* Printing styles */
        .printing .enhanced-table-container {
            box-shadow: none !important;
            border: 1px solid #dee2e6;
        }

        .printing .enhanced-row:hover {
            background: white !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Loading animation for data */
        .loading-row {
            opacity: 0.6;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .empty-state h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 14px;
        }

        /* Header Actions */
        .header-actions .btn {
            border-radius: 20px;
            font-size: 11px;
            padding: 6px 12px;
            margin-left: 5px;
        }

        /* Print Styles */
        @media print {

            .header-actions,
            .card-header .btn {
                display: none !important;
            }

            .enhanced-table-container {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .enhanced-row:hover {
                background: white !important;
                transform: none !important;
                box-shadow: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .enhanced-table {
                font-size: 11px !important;
            }

            .student-name {
                font-size: 12px !important;
            }

            .student-meta {
                font-size: 10px !important;
            }

            body {
                background: white !important;
            }
        }

        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .enhanced-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .enhanced-table {
                min-width: 800px;
            }

            .student-info {
                min-width: 150px;
            }

            .department-badge {
                min-width: 80px;
            }
        }

        /* Export button enhancements */
        .export-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .export-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .export-btn i {
            margin-right: 5px;
        }

        /* Additional professional enhancements */
        .department-badge {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        /* Loading animation for enhanced UX */
        .table-loading {
            position: relative;
            overflow: hidden;
        }

        .table-loading::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* Enhanced table header with better visual hierarchy */
        .enhanced-thead th {
            position: relative;
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px 12px;
            white-space: nowrap;
        }

        /* Improved hover effects */
        .enhanced-row {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }

        .enhanced-row:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(3px);
            border-left-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        }

        /* Status indicators with pulse animation */
        .badge {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Ripple effect for interactive feedback */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            width: 20px;
            height: 20px;
            margin-left: -10px;
            margin-top: -10px;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Enhanced row animations */
        .enhanced-row {
            animation: slideInUp 0.3s ease-out;
            animation-fill-mode: both;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Smooth table appearance */
        .enhanced-table-container {
            animation: tableSlideIn 0.5s ease-out;
        }

        @keyframes tableSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-box {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #dee2e6;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .info-content h6 {
            margin-bottom: 5px;
            color: #495057;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content p {
            margin-bottom: 0;
            color: #2c3e50;
            font-weight: 500;
        }

        /* Enhanced Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-content {
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 3px;
        }

        .stat-percentage {
            font-size: 12px;
            opacity: 0.8;
            font-weight: 500;
        }

        .stat-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 30px;
            opacity: 0.3;
            z-index: 1;
        }

        /* Badge enhancements */
        .badge-lg {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Alert enhancements */
        .alert-light {
            background-color: #fefefe;
            border-color: #dee2e6;
            color: #495057;
        }

        .border-left-primary {
            border-left: 4px solid #667eea !important;
        }

        /* Button group enhancements */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
            transform: translateY(-1px);
        }

        /* Dropdown menu enhancements */
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: none;
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 16px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
        }

        .dropdown-item small {
            font-size: 11px;
            line-height: 1.2;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table {
                white-space: nowrap;
            }

            .info-box {
                flex-direction: column;
                text-align: center;
            }

            .info-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .stat-card {
                margin-bottom: 10px;
            }

            .stat-number {
                font-size: 24px;
            }
        }

        /* Print styles */
        @media print {

            .btn-group,
            .dropdown,
            .alert {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush
