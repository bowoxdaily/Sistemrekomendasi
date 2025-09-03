<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        @switch($reportType)
            @case('employment')
                Laporan Data Pekerjaan Alumni
            @break

            @case('education')
                Laporan Data Pendidikan Alumni
            @break

            @case('unemployment')
                Laporan Alumni Belum Bekerja
            @break

            @default
                Laporan Umum Alumni
        @endswitch
    </title>
    <style>
        /* Base styles */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Layout containers */
        .container {
            padding: 15px;
            max-width: 100%;
        }

        /* Header styling */
        .report-header {
            position: relative;
            padding: 20px 0;
            margin-bottom: 20px;
            border-bottom: 3px solid #2e7d32;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px 8px 0 0;
        }

        .report-header h1 {
            color: #2e7d32;
            font-size: 22px;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
        }

        .report-header .subtitle {
            color: #495057;
            font-size: 14px;
            margin: 8px 0;
            font-weight: 600;
        }

        .report-header p {
            color: #6c757d;
            margin: 4px 0;
            font-size: 10px;
        }

        .school-info {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0;
            text-align: center;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 5px;
        }

        .school-address {
            font-size: 10px;
            color: #6c757d;
        }

        /* Summary section */
        .summary-box {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            border: 2px solid #2e7d32;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(46, 125, 50, 0.1);
        }

        .summary-box h2 {
            color: #2e7d32;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2e7d32;
            text-align: center;
            font-weight: bold;
        }

        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-stat-item {
            display: table-cell;
            text-align: center;
            padding: 12px 8px;
            background: #ffffff;
            border: 1px solid #d4edda;
            border-radius: 6px;
            margin: 0 2px;
        }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2e7d32;
            display: block;
        }

        .stat-label {
            font-size: 10px;
            color: #495057;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .summary-info {
            margin-bottom: 15px;
        }

        .summary-info-item {
            margin-bottom: 8px;
            padding: 6px 0;
            border-bottom: 1px dotted #dee2e6;
        }

        .summary-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 180px;
        }

        .summary-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .highlight-value {
            color: #2e7d32;
            font-weight: bold;
            font-size: 13px;
        }

        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table th {
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px 8px;
            font-size: 11px;
            border: 1px solid #1b5e20;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 10px 8px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            font-size: 10px;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .data-table tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .data-table tr:hover {
            background-color: #e8f5e8;
        }

        /* Enhanced cell styling */
        .data-table .name-cell {
            font-weight: bold;
            color: #2e7d32;
            font-size: 11px;
        }

        .data-table .id-cell {
            text-align: center;
            font-weight: bold;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .data-table .status-cell {
            text-align: center;
            font-weight: bold;
        }

        .data-table .info-block {
            margin-bottom: 4px;
            padding: 2px 0;
        }

        .data-table .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
            color: #495057;
        }

        .data-table .info-value {
            display: inline-block;
            color: #2c3e50;
        }

        /* Footer styling */
        .report-footer {
            margin-top: 30px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }

        .report-footer .footer-content {
            margin-bottom: 8px;
        }

        .report-footer .footer-date {
            font-weight: bold;
            color: #495057;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .badge-info {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
        }

        .badge-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #212529;
        }

        .badge-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }

        /* Page elements */
        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Utilities */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        /* Print optimizations */
        @media print {
            .container {
                padding: 10px;
            }

            .report-header {
                margin-bottom: 15px;
            }

            .summary-box {
                margin-bottom: 15px;
            }
        }

        .text-right {
            text-align: right;
        }

        .text-small {
            font-size: 10px;
        }

        .text-muted {
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Report Header -->
        <div class="report-header">
            <div class="school-info">
                <div class="school-name">SMK [NAMA SEKOLAH]</div>
                <div class="school-address">Alamat Sekolah | Telepon: (0000) 000-0000 | Email: admin@sekolah.sch.id</div>
            </div>

            <h1>LAPORAN TRACER STUDY ALUMNI</h1>
            <div class="subtitle">
                @switch($reportType)
                    @case('employment')
                        ANALISIS DATA PEKERJAAN ALUMNI
                    @break

                    @case('education')
                        ANALISIS DATA PENDIDIKAN LANJUTAN ALUMNI
                    @break

                    @case('unemployment')
                        ANALISIS ALUMNI BELUM BEKERJA
                    @break

                    @default
                        LAPORAN UMUM KONDISI ALUMNI
                @endswitch
            </div>
            <p><strong>Tanggal Generate:</strong> {{ $generatedAt }}</p>
            <p><strong>Sistem:</strong> Tracer Study Alumni Digital</p>
        </div>

        <!-- Summary Box -->
        <div class="summary-box">
            <h2>📊 RINGKASAN EKSEKUTIF</h2>

            @php
                $totalStudents = $students->count();
                $workingCount = $students->where('status_setelah_lulus', 'kerja')->count();
                $studyingCount = $students->where('status_setelah_lulus', 'kuliah')->count();
                $unemployedCount = $students->where('status_setelah_lulus', 'belum_kerja')->count();

                $workingPercentage = $totalStudents > 0 ? round(($workingCount / $totalStudents) * 100, 1) : 0;
                $studyingPercentage = $totalStudents > 0 ? round(($studyingCount / $totalStudents) * 100, 1) : 0;
                $unemployedPercentage = $totalStudents > 0 ? round(($unemployedCount / $totalStudents) * 100, 1) : 0;
            @endphp

            <div class="summary-stats">
                <div class="summary-stat-item">
                    <span class="stat-number">{{ $totalStudents }}</span>
                    <div class="stat-label">Total Alumni</div>
                </div>
                <div class="summary-stat-item">
                    <span class="stat-number">{{ $workingCount }}</span>
                    <div class="stat-label">Bekerja ({{ $workingPercentage }}%)</div>
                </div>
                <div class="summary-stat-item">
                    <span class="stat-number">{{ $studyingCount }}</span>
                    <div class="stat-label">Kuliah ({{ $studyingPercentage }}%)</div>
                </div>
                <div class="summary-stat-item">
                    <span class="stat-number">{{ $unemployedCount }}</span>
                    <div class="stat-label">Belum Kerja ({{ $unemployedPercentage }}%)</div>
                </div>
            </div>

            <div class="summary-info">
                <div class="summary-info-item">
                    <span class="summary-label">🎯 Fokus Laporan:</span>
                    <span class="summary-value highlight-value">
                        @switch($reportType)
                            @case('employment')
                                Analisis mendalam tentang kondisi pekerjaan alumni
                            @break

                            @case('education')
                                Analisis mendalam tentang pendidikan lanjutan alumni
                            @break

                            @case('unemployment')
                                Analisis alumni yang belum mendapatkan pekerjaan
                            @break

                            @default
                                Gambaran umum kondisi semua alumni
                        @endswitch
                    </span>
                </div>

                <div class="summary-info-item">
                    <span class="summary-label">📈 Total Data Tercakup:</span>
                    <span class="summary-value highlight-value">{{ $totalStudents }} alumni</span>
                </div>

                <div class="summary-info-item">
                    <span class="summary-label">📅 Periode Data:</span>
                    <span class="summary-value">
                        @php
                            $minYear = $students->min('tanggal_lulus')
                                ? date('Y', strtotime($students->min('tanggal_lulus')))
                                : '-';
                            $maxYear = $students->max('tanggal_lulus')
                                ? date('Y', strtotime($students->max('tanggal_lulus')))
                                : '-';
                        @endphp
                        @if ($minYear !== '-' && $maxYear !== '-')
                            {{ $minYear === $maxYear ? $minYear : $minYear . ' - ' . $maxYear }}
                        @else
                            Data tidak lengkap
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <span class="summary-label">Total Data:</span>
        <span class="summary-value highlight-value">{{ count($students) }} alumni</span>
    </div>

    <div class="summary-info-item">
        <span class="summary-label">Tanggal Laporan:</span>
        <span class="summary-value">{{ date('d-m-Y') }}</span>
    </div>

    @if ($reportType == 'employment')
        @php
            $matchCount = $reportData->where('sesuai_jurusan', 'ya')->count();
            $notMatchCount = $reportData->where('sesuai_jurusan', 'tidak')->count();
            $totalCount = $matchCount + $notMatchCount;
            $matchPercentage = $totalCount > 0 ? round(($matchCount / $totalCount) * 100) : 0;

            // Calculate average salary
            $salaries = $reportData->pluck('gaji')->filter()->toArray();
            $avgSalary = count($salaries) > 0 ? array_sum($salaries) / count($salaries) : 0;
        @endphp

        <div class="summary-info-item">
            <span class="summary-label">Sesuai Jurusan:</span>
            <span class="summary-value">{{ $matchCount }} alumni ({{ $matchPercentage }}%)</span>
        </div>

        <div class="summary-info-item">
            <span class="summary-label">Tidak Sesuai Jurusan:</span>
            <span class="summary-value">{{ $notMatchCount }} alumni ({{ 100 - $matchPercentage }}%)</span>
        </div>

        <div class="summary-info-item">
            <span class="summary-label">Rata-rata Gaji:</span>
            <span class="summary-value highlight-value">Rp {{ number_format($avgSalary, 0, ',', '.') }}</span>
        </div>
    @elseif($reportType == 'education')
        @php
            $scholarshipCount = $reportData->where('status_beasiswa', 'ya')->count();
            $totalCount = count($reportData);
            $scholarshipPercentage = $totalCount > 0 ? round(($scholarshipCount / $totalCount) * 100) : 0;

            // Count by education level
            $educationLevels = $reportData->groupBy('jenjang')->map->count();
        @endphp

        <div class="summary-info-item">
            <span class="summary-label">Penerima Beasiswa:</span>
            <span class="summary-value">{{ $scholarshipCount }} alumni ({{ $scholarshipPercentage }}%)</span>
        </div>

        <div class="summary-info-item">
            <span class="summary-label">Jenjang Pendidikan:</span>
            <span class="summary-value">
                @foreach ($educationLevels as $level => $count)
                    {{ $level }}: {{ $count }} alumni{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </span>
        </div>
    @elseif($reportType == 'unemployment')
        @php
            $durations = [];
            foreach ($students as $student) {
                if ($student->tanggal_lulus) {
                    $gradDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                    $now = \Carbon\Carbon::now();
                    $durations[] = $gradDate->diffInMonths($now);
                }
            }

            $avgDuration = count($durations) > 0 ? array_sum($durations) / count($durations) : 0;
        @endphp

        <div class="summary-info-item">
            <span class="summary-label">Rata-rata Durasi Mencari Kerja:</span>
            <span class="summary-value highlight-value">{{ number_format($avgDuration, 1) }} bulan</span>
        </div>
    @else
        @php
            $workingCount = $students->where('status_setelah_lulus', 'kerja')->count();
            $studyingCount = $students->where('status_setelah_lulus', 'kuliah')->count();
            $unemployedCount = $students->where('status_setelah_lulus', 'belum_kerja')->count();
            $totalCount = count($students);
        @endphp

        <div class="summary-info-item">
            <span class="summary-label">Bekerja:</span>
            <span class="summary-value">{{ $workingCount }} alumni
                ({{ $totalCount > 0 ? round(($workingCount / $totalCount) * 100) : 0 }}%)</span>
        </div>

        <div class="summary-info-item">
            <span class="summary-label">Kuliah:</span>
            <span class="summary-value">{{ $studyingCount }} alumni
                ({{ $totalCount > 0 ? round(($studyingCount / $totalCount) * 100) : 0 }}%)</span>
        </div>

        <div class="summary-info-item">
            <span class="summary-label">Belum Bekerja:</span>
            <span class="summary-value">{{ $unemployedCount }} alumni
                ({{ $totalCount > 0 ? round(($unemployedCount / $totalCount) * 100) : 0 }}%)</span>
        </div>
    @endif
    </div>
    </div>

    <!-- Data Tables -->
    @if (count($students) > 0)
        @if ($reportType == 'employment')
            <!-- Employment Report Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="45%">Data Alumni</th>
                        <th width="50%">Informasi Pekerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($students as $student)
                        @php
                            $dataKerja = $reportData->where('student_id', $student->id)->first();
                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                        @endphp
                        @if ($dataKerja)
                            <tr>
                                <td class="text-center">{{ $i++ }}</td>
                                <td>
                                    <div class="name-cell">{{ $student->nama_lengkap }}</div>
                                    <div class="text-small text-muted">NISN: {{ $student->nisn ?? '-' }}</div>
                                    <div class="info-block">
                                        <span class="info-label">Jurusan:</span>
                                        <span class="info-value">{{ $jurusan ? $jurusan->nama : '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Lulus:</span>
                                        <span
                                            class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-block">
                                        <span class="info-label">Perusahaan:</span>
                                        <span
                                            class="info-value"><strong>{{ $dataKerja->nama_perusahaan ?? '-' }}</strong></span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Posisi:</span>
                                        <span class="info-value">{{ $dataKerja->posisi ?? '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Jenis:</span>
                                        <span class="info-value">{{ $dataKerja->jenis_pekerjaan ?? '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Mulai Kerja:</span>
                                        <span class="info-value">
                                            {{ isset($dataKerja->tanggal_mulai) ? date('d-m-Y', strtotime($dataKerja->tanggal_mulai)) : '-' }}
                                        </span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Gaji:</span>
                                        <span class="info-value">
                                            {{ isset($dataKerja->gaji) ? 'Rp ' . number_format($dataKerja->gaji, 0, ',', '.') : '-' }}
                                        </span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Kesesuaian:</span>
                                        <span class="info-value">
                                            <span
                                                class="status-badge badge-{{ ($dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'success' : 'info' }}">
                                                {{ ($dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'Sesuai Jurusan' : 'Tidak Sesuai' }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @elseif($reportType == 'education')
            <!-- Education Report Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="45%">Data Alumni</th>
                        <th width="50%">Informasi Pendidikan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($students as $student)
                        @php
                            $dataKuliah = $reportData->where('student_id', $student->id)->first();
                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                        @endphp
                        @if ($dataKuliah)
                            <tr>
                                <td class="text-center">{{ $i++ }}</td>
                                <td>
                                    <div class="name-cell">{{ $student->nama_lengkap }}</div>
                                    <div class="text-small text-muted">NISN: {{ $student->nisn ?? '-' }}</div>
                                    <div class="info-block">
                                        <span class="info-label">Jurusan:</span>
                                        <span class="info-value">{{ $jurusan ? $jurusan->nama : '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Lulus:</span>
                                        <span
                                            class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-block">
                                        <span class="info-label">Perguruan Tinggi:</span>
                                        <span
                                            class="info-value"><strong>{{ $dataKuliah->nama_pt ?? '-' }}</strong></span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Program Studi:</span>
                                        <span class="info-value">{{ $dataKuliah->jurusan ?? '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Jenjang:</span>
                                        <span class="info-value">{{ $dataKuliah->jenjang ?? '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Tahun Masuk:</span>
                                        <span class="info-value">{{ $dataKuliah->tahun_masuk ?? '-' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Status Beasiswa:</span>
                                        <span class="info-value">
                                            <span
                                                class="status-badge badge-{{ ($dataKuliah->status_beasiswa ?? '-') == 'ya' ? 'success' : 'info' }}">
                                                {{ ($dataKuliah->status_beasiswa ?? '-') == 'ya' ? 'Beasiswa' : 'Non-Beasiswa' }}
                                            </span>
                                            @if (($dataKuliah->status_beasiswa ?? '-') == 'ya' && $dataKuliah->nama_beasiswa)
                                                <div class="text-small">{{ $dataKuliah->nama_beasiswa }}</div>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @elseif($reportType == 'unemployment')
            <!-- Unemployment Report Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="45%">Data Alumni</th>
                        <th width="50%">Informasi Tambahan</th>
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
                                $graduationDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                $now = \Carbon\Carbon::now();
                                $durationMonths = $graduationDate->diffInMonths($now);

                                if ($durationMonths < 12) {
                                    $duration = $durationMonths . ' bulan';
                                } else {
                                    $years = floor($durationMonths / 12);
                                    $months = $durationMonths % 12;
                                    $duration = $years . ' tahun ' . ($months > 0 ? $months . ' bulan' : '');
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>
                                <div class="name-cell">{{ $student->nama_lengkap }}</div>
                                <div class="text-small text-muted">NISN: {{ $student->nisn ?? '-' }}</div>
                                <div class="info-block">
                                    <span class="info-label">Jurusan:</span>
                                    <span class="info-value">{{ $jurusan ? $jurusan->nama : '-' }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Lulus:</span>
                                    <span
                                        class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="info-block">
                                    <span class="info-label">Jenis Kelamin:</span>
                                    <span class="info-value">{{ ucfirst($student->jenis_kelamin ?? '-') }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Durasi:</span>
                                    <span class="info-value highlight-value">{{ $duration }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Alamat:</span>
                                    <span class="info-value">{{ $student->alamat ?? '-' }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Kontak:</span>
                                    <span class="info-value">{{ $student->no_hp ?? '-' }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Status:</span>
                                    <span class="info-value">
                                        <span class="status-badge badge-warning">Belum Bekerja</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- General Report Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="45%">Data Alumni</th>
                        <th width="50%">Informasi Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($reportData as $student)
                        @php
                            $jurusan = App\Models\Jurusan::find($student->jurusan_id);

                            // Get badge class based on status
                            $badgeClass = 'info';
                            if ($student->status_setelah_lulus == 'kerja') {
                                $badgeClass = 'success';
                            } elseif ($student->status_setelah_lulus == 'belum_kerja') {
                                $badgeClass = 'warning';
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>
                                <div class="name-cell">{{ $student->nama_lengkap }}</div>
                                <div class="text-small text-muted">NISN: {{ $student->nisn ?? '-' }}</div>
                                <div class="info-block">
                                    <span class="info-label">Jurusan:</span>
                                    <span class="info-value">{{ $jurusan ? $jurusan->nama : '-' }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Lulus:</span>
                                    <span
                                        class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="info-block">
                                    <span class="info-label">Status:</span>
                                    <span class="info-value">
                                        <span class="status-badge badge-{{ $badgeClass }}">
                                            @if ($student->status_setelah_lulus == 'kerja')
                                                Bekerja
                                            @elseif($student->status_setelah_lulus == 'kuliah')
                                                Kuliah
                                            @else
                                                Belum Bekerja
                                            @endif
                                        </span>
                                    </span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Jenis Kelamin:</span>
                                    <span class="info-value">{{ ucfirst($student->jenis_kelamin ?? '-') }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Alamat:</span>
                                    <span class="info-value">{{ $student->alamat ?? '-' }}</span>
                                </div>
                                <div class="info-block">
                                    <span class="info-label">Kontak:</span>
                                    <span class="info-value">{{ $student->no_hp ?? '-' }}</span>
                                </div>
                                @if ($student->status_setelah_lulus == 'kerja' && $student->dataKerja)
                                    <div class="info-block">
                                        <span class="info-label">Perusahaan:</span>
                                        <span
                                            class="info-value">{{ $student->dataKerja->nama_perusahaan ?? '-' }}</span>
                                    </div>
                                @elseif($student->status_setelah_lulus == 'kuliah' && $student->dataKuliah)
                                    <div class="info-block">
                                        <span class="info-label">Perguruan Tinggi:</span>
                                        <span class="info-value">{{ $student->dataKuliah->nama_pt ?? '-' }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div
            style="text-align: center; padding: 30px 0; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px;">
            <h3 style="color: #6c757d; font-size: 18px;">Tidak ada data yang sesuai dengan kriteria yang dipilih.</h3>
            <p style="color: #6c757d; margin-top: 10px;">Silakan ubah filter dan coba lagi.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="report-footer">
        <div class="footer-content">
            <strong>📋 Laporan Tracer Study Alumni</strong>
        </div>
        <div class="footer-content">
            🏫 SMK [Nama Sekolah] | 📧 admin@sekolah.sch.id | 📞 (0000) 000-0000
        </div>
        <div class="footer-content">
            💻 Digenerate oleh Sistem Tracer Study & Rekomendasi Karir Digital
        </div>
        <div class="footer-date">
            🕐 {{ $generatedAt }} | 📊 Total: {{ count($students) }} data alumni
        </div>
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #dee2e6; font-size: 8px;">
            <em>Dokumen ini digenerate secara otomatis dan telah terverifikasi oleh sistem.</em>
        </div>
    </div>
    </div>
</body>

</html>
