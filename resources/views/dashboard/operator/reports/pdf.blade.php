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
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        
        /* Layout containers */
        .container {
            padding: 20px;
        }
        
        /* Header styling */
        .report-header {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 3px solid #3f51b5;
            text-align: center;
        }
        
        .report-header h1 {
            color: #3f51b5;
            font-size: 24px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .report-header p {
            color: #666;
            margin: 5px 0;
            font-size: 12px;
        }
        
        .report-header .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 60px;
            height: auto;
        }
        
        /* Summary section */
        .summary-box {
            background: #f5f7ff;
            border: 1px solid #dbe1ff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .summary-box h2 {
            color: #3f51b5;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dbe1ff;
        }
        
        .summary-info {
            margin-bottom: 15px;
        }
        
        .summary-info-item {
            margin-bottom: 8px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 180px;
        }
        
        .summary-value {
            color: #333;
        }
        
        .highlight-value {
            color: #3f51b5;
            font-weight: bold;
        }
        
        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .data-table th {
            background-color: #3f51b5;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f9f9ff;
        }
        
        .data-table tr:hover {
            background-color: #f1f3ff;
        }
        
        /* Enhanced cell styling */
        .data-table .name-cell {
            font-weight: bold;
            color: #333;
        }
        
        .data-table .info-block {
            margin-bottom: 4px;
        }
        
        .data-table .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
            color: #555;
        }
        
        .data-table .info-value {
            display: inline-block;
        }
        
        /* Footer styling */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .badge-success {
            background-color: #4CAF50;
        }
        
        .badge-info {
            background-color: #2196F3;
        }
        
        .badge-warning {
            background-color: #FFC107;
            color: #333;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
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
            <h1>
                @switch($reportType)
                    @case('employment')
                        LAPORAN DATA PEKERJAAN ALUMNI
                        @break
                    @case('education')
                        LAPORAN DATA PENDIDIKAN ALUMNI
                        @break
                    @case('unemployment')
                        LAPORAN ALUMNI BELUM BEKERJA
                        @break
                    @default
                        LAPORAN UMUM ALUMNI
                @endswitch
            </h1>
            <p>Tanggal Laporan: {{ $generatedAt }}</p>
        </div>
        
        <!-- Summary Box -->
        <div class="summary-box">
            <h2>RINGKASAN LAPORAN</h2>
            
            <div class="summary-info">
                <div class="summary-info-item">
                    <span class="summary-label">Total Data:</span>
                    <span class="summary-value highlight-value">{{ count($students) }} alumni</span>
                </div>
                
                <div class="summary-info-item">
                    <span class="summary-label">Tanggal Laporan:</span>
                    <span class="summary-value">{{ date('d-m-Y') }}</span>
                </div>
                
                @if($reportType == 'employment')
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
                            @foreach($educationLevels as $level => $count)
                                {{ $level }}: {{ $count }} alumni{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                    
                @elseif($reportType == 'unemployment')
                    @php
                        $durations = [];
                        foreach($students as $student) {
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
                        <span class="summary-value">{{ $workingCount }} alumni ({{ $totalCount > 0 ? round(($workingCount / $totalCount) * 100) : 0 }}%)</span>
                    </div>
                    
                    <div class="summary-info-item">
                        <span class="summary-label">Kuliah:</span>
                        <span class="summary-value">{{ $studyingCount }} alumni ({{ $totalCount > 0 ? round(($studyingCount / $totalCount) * 100) : 0 }}%)</span>
                    </div>
                    
                    <div class="summary-info-item">
                        <span class="summary-label">Belum Bekerja:</span>
                        <span class="summary-value">{{ $unemployedCount }} alumni ({{ $totalCount > 0 ? round(($unemployedCount / $totalCount) * 100) : 0 }}%)</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Data Tables -->
        @if(count($students) > 0)
            @if($reportType == 'employment')
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
                        @foreach($students as $student)
                            @php
                                $dataKerja = $reportData->where('student_id', $student->id)->first();
                                $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                            @endphp
                            @if($dataKerja)
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
                                        <span class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-block">
                                        <span class="info-label">Perusahaan:</span>
                                        <span class="info-value"><strong>{{ $dataKerja->nama_perusahaan ?? '-' }}</strong></span>
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
                                            <span class="status-badge badge-{{ ($dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'success' : 'info' }}">
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
                        @foreach($students as $student)
                            @php
                                $dataKuliah = $reportData->where('student_id', $student->id)->first();
                                $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                            @endphp
                            @if($dataKuliah)
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
                                        <span class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-block">
                                        <span class="info-label">Perguruan Tinggi:</span>
                                        <span class="info-value"><strong>{{ $dataKuliah->nama_pt ?? '-' }}</strong></span>
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
                                            <span class="status-badge badge-{{ ($dataKuliah->status_beasiswa ?? '-') == 'ya' ? 'success' : 'info' }}">
                                                {{ ($dataKuliah->status_beasiswa ?? '-') == 'ya' ? 'Beasiswa' : 'Non-Beasiswa' }}
                                            </span>
                                            @if(($dataKuliah->status_beasiswa ?? '-') == 'ya' && $dataKuliah->nama_beasiswa)
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
                        @foreach($reportData as $student)
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
                                        <span class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
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
                        @foreach($reportData as $student)
                            @php
                                $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                
                                // Get badge class based on status
                                $badgeClass = 'info';
                                if($student->status_setelah_lulus == 'kerja') {
                                    $badgeClass = 'success';
                                } elseif($student->status_setelah_lulus == 'belum_kerja') {
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
                                        <span class="info-value">{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-block">
                                        <span class="info-label">Status:</span>
                                        <span class="info-value">
                                            <span class="status-badge badge-{{ $badgeClass }}">
                                                @if($student->status_setelah_lulus == 'kerja')
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
                                    @if($student->status_setelah_lulus == 'kerja' && $student->dataKerja)
                                        <div class="info-block">
                                            <span class="info-label">Perusahaan:</span>
                                            <span class="info-value">{{ $student->dataKerja->nama_perusahaan ?? '-' }}</span>
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
            <div style="text-align: center; padding: 30px 0; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px;">
                <h3 style="color: #6c757d; font-size: 18px;">Tidak ada data yang sesuai dengan kriteria yang dipilih.</h3>
                <p style="color: #6c757d; margin-top: 10px;">Silakan ubah filter dan coba lagi.</p>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="report-footer">
            <p>Laporan Tracer Study - {{ $generatedAt }}</p>
            <p>Digenerate oleh Sistem Tracer Study & Rekomendasi Karir</p>
        </div>
    </div>
</body>
</html>
