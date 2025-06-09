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
                        </h4>
                        <p class="text-muted">Detail data berdasarkan kriteria yang telah dipilih</p>
                    </div>
                    <div>
                        <div class="btn-group">
                            <a href="{{ route('operator.reports.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('operator.reports.generate', array_merge($filters, ['format' => 'pdf', 'report_type' => $reportType])) }}" class="btn btn-danger" target="_blank">
                                <i class="mdi mdi-file-pdf"></i> Ekspor PDF
                            </a>
                            <a href="{{ route('operator.reports.generate', array_merge($filters, ['format' => 'excel', 'report_type' => $reportType])) }}" class="btn btn-success">
                                <i class="mdi mdi-file-excel"></i> Ekspor Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Summary Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Kriteria Laporan</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Tipe Laporan:</strong></p>
                                <p>
                                    @switch($reportType)
                                        @case('employment')
                                            <span class="badge badge-success">Data Pekerjaan</span>
                                            @break
                                        @case('education')
                                            <span class="badge badge-info">Data Pendidikan</span>
                                            @break
                                        @case('unemployment')
                                            <span class="badge badge-warning">Belum Bekerja</span>
                                            @break
                                        @default
                                            <span class="badge badge-primary">Laporan Umum</span>
                                    @endswitch
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Tahun Kelulusan:</strong></p>
                                <p>{{ $filters['year'] ?? 'Semua Tahun' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Jurusan:</strong></p>
                                <p>{{ $filters['department'] ?? 'Semua Jurusan' }}</p>
                            </div>
                        </div>
                        
                        <!-- Statistics Summary -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p class="mb-0"><strong>Total Data:</strong> {{ count($students) }} alumni</p>
                                        </div>
                                        <div>
                                            <p class="mb-0"><strong>Digenerate pada:</strong> {{ now()->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Data -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Hasil Laporan</h5>
                        
                        @if(count($students) > 0)
                            <div class="table-responsive">
                                @if($reportType == 'employment')
                                    <!-- Employment Report Table -->
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Lulus</th>
                                                <th>Perusahaan</th>
                                                <th>Posisi</th>
                                                <th>Jenis Pekerjaan</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Sesuai Jurusan</th>
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
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $student->nama_lengkap }}</td>
                                                    <td>{{ $jurusan ? $jurusan->nama : '-' }}</td>
                                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</td>
                                                    <td>{{ $dataKerja->nama_perusahaan ?? '-' }}</td>
                                                    <td>{{ $dataKerja->posisi ?? '-' }}</td>
                                                    <td>{{ $dataKerja->jenis_pekerjaan ?? '-' }}</td>
                                                    <td>{{ $dataKerja->tanggal_mulai ? date('d-m-Y', strtotime($dataKerja->tanggal_mulai)) : '-' }}</td>
                                                    <td>{{ ($dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'Ya' : 'Tidak' }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                
                                @elseif($reportType == 'education')
                                    <!-- Education Report Table -->
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jurusan (SMK)</th>
                                                <th>Tahun Lulus</th>
                                                <th>Perguruan Tinggi</th>
                                                <th>Program Studi</th>
                                                <th>Jenjang</th>
                                                <th>Tahun Masuk</th>
                                                <th>Status Beasiswa</th>
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
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $student->nama_lengkap }}</td>
                                                    <td>{{ $jurusan ? $jurusan->nama : '-' }}</td>
                                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</td>
                                                    <td>{{ $dataKuliah->nama_pt ?? '-' }}</td>
                                                    <td>{{ $dataKuliah->jurusan ?? '-' }}</td>
                                                    <td>{{ $dataKuliah->jenjang ?? '-' }}</td>
                                                    <td>{{ $dataKuliah->tahun_masuk ?? '-' }}</td>
                                                    <td>
                                                        @if(($dataKuliah->status_beasiswa ?? '') == 'ya')
                                                            <span class="badge badge-success">Ya</span>
                                                            @if($dataKuliah->nama_beasiswa)
                                                                <small class="d-block">{{ $dataKuliah->nama_beasiswa }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">Tidak</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                
                                @elseif($reportType == 'unemployment')
                                    <!-- Unemployment Report Table -->
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Lulus</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Durasi Mencari Kerja</th>
                                                <th>Alamat</th>
                                                <th>Kontak</th>
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
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $student->nama_lengkap }}</td>
                                                    <td>{{ $jurusan ? $jurusan->nama : '-' }}</td>
                                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</td>
                                                    <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                                                    <td>{{ $duration }}</td>
                                                    <td>{{ $student->alamat ?? '-' }}</td>
                                                    <td>{{ $student->no_hp ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                
                                @else
                                    <!-- General Report Table -->
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>NISN</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Lulus</th>
                                                <th>Status</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Alamat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i = 1; @endphp
                                            @foreach($reportData as $student)
                                                @php
                                                    $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                @endphp
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $student->nama_lengkap }}</td>
                                                    <td>{{ $student->nisn }}</td>
                                                    <td>{{ $jurusan ? $jurusan->nama : '-' }}</td>
                                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}</td>
                                                    <td>
                                                        @if($student->status_setelah_lulus == 'kerja')
                                                            <span class="badge badge-success">Bekerja</span>
                                                        @elseif($student->status_setelah_lulus == 'kuliah')
                                                            <span class="badge badge-info">Kuliah</span>
                                                        @else
                                                            <span class="badge badge-warning">Belum Bekerja</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                                                    <td>{{ $student->alamat ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle-outline mr-2"></i>
                                Tidak ada data yang sesuai dengan kriteria yang dipilih.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Representations Section -->
        @if(count($students) > 0)
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Visualisasi Data</h5>
                        <div class="chart-container" style="height: 300px; max-height: 300px; overflow: hidden;">
                            @if($reportType == 'employment')
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
                            @if($reportType == 'employment')
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
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $matchPercentage }}%">
                                            {{ $matchPercentage }}% Sesuai
                                        </div>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $matchPercentage }}%">
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
                                                <ul class="list-group list-group-flush" style="max-height: 150px; overflow-y: auto;">
                                                    @php
                                                        $jobTypes = $reportData->groupBy('jenis_pekerjaan')->map->count();
                                                        $sortedJobTypes = $jobTypes->sortDesc();
                                                    @endphp
                                                    
                                                    @foreach($sortedJobTypes->take(5) as $jobType => $count)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $jobType ?: 'Tidak diketahui' }}
                                                            <span class="badge badge-primary badge-pill">{{ $count }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if($sortedJobTypes->count() > 5)
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted">Menampilkan 5 dari {{ $sortedJobTypes->count() }} jenis pekerjaan</small>
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
                                                    foreach($reportData as $data) {
                                                        $student = $students->where('id', $data->student_id)->first();
                                                        if ($student && $student->tanggal_lulus && $data->tanggal_mulai) {
                                                            $gradDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                                            $startDate = \Carbon\Carbon::parse($data->tanggal_mulai);
                                                            if ($startDate >= $gradDate) {
                                                                $waitingTimes[] = $gradDate->diffInMonths($startDate);
                                                            }
                                                        }
                                                    }
                                                    
                                                    $avgWaitingTime = count($waitingTimes) > 0 ? array_sum($waitingTimes) / count($waitingTimes) : 0;
                                                @endphp
                                                
                                                <div class="text-center my-3">
                                                    <h1 class="display-4">{{ number_format($avgWaitingTime, 1) }}</h1>
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
                                                <ul class="list-group list-group-flush" style="max-height: 150px; overflow-y: auto;">
                                                    @php
                                                        $levels = $reportData->groupBy('jenjang')->map->count();
                                                    @endphp
                                                    
                                                    @foreach($levels as $level => $count)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $level ?: 'Tidak diketahui' }}
                                                            <span class="badge badge-info badge-pill">{{ $count }}</span>
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
                                                    $scholarshipCount = $reportData->where('status_beasiswa', 'ya')->count();
                                                    $totalCount = count($reportData);
                                                    $scholarshipPercentage = $totalCount > 0 ? round(($scholarshipCount / $totalCount) * 100) : 0;
                                                @endphp
                                                
                                                <div class="text-center my-3">
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $scholarshipPercentage }}%">
                                                            {{ $scholarshipPercentage }}%
                                                        </div>
                                                    </div>
                                                    <p class="mt-2">
                                                        {{ $scholarshipCount }} dari {{ $totalCount }} alumni mendapatkan beasiswa
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
                                            
                                            @foreach($majors->take(5) as $major => $count)
                                                <li>{{ $major ?: 'Tidak diketahui' }} ({{ $count }} alumni)</li>
                                            @endforeach
                                        </ul>
                                        @if($majors->count() > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Menampilkan 5 dari {{ $majors->count() }} program studi</small>
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
                                        foreach($reportData as $student) {
                                            if ($student->tanggal_lulus) {
                                                $gradDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                                $now = \Carbon\Carbon::now();
                                                $durations[] = $gradDate->diffInMonths($now);
                                            }
                                        }
                                        
                                        $avgDuration = count($durations) > 0 ? array_sum($durations) / count($durations) : 0;
                                        
                                        // Group by duration ranges
                                        $durationRanges = [
                                            '0-6 bulan' => 0,
                                            '7-12 bulan' => 0,
                                            '1-2 tahun' => 0,
                                            '> 2 tahun' => 0
                                        ];
                                        
                                        foreach($durations as $duration) {
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
                                        @foreach($durationRanges as $range => $count)
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
                                                <h3 class="text-success">{{ $reportData->where('status_setelah_lulus', 'kerja')->count() }}</h3>
                                                <p>Bekerja</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-center">
                                        <div class="card border-info mb-3">
                                            <div class="card-body">
                                                <h3 class="text-info">{{ $reportData->where('status_setelah_lulus', 'kuliah')->count() }}</h3>
                                                <p>Kuliah</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-center">
                                        <div class="card border-warning mb-3">
                                            <div class="card-body">
                                                <h3 class="text-warning">{{ $reportData->where('status_setelah_lulus', 'belum_kerja')->count() }}</h3>
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
                                                foreach($reportData as $student) {
                                                    $jurusan = App\Models\Jurusan::find($student->jurusan_id);
                                                    $jurusanName = $jurusan ? $jurusan->nama : 'Tidak diketahui';
                                                    if (!isset($departmentStats[$jurusanName])) {
                                                        $departmentStats[$jurusanName] = 0;
                                                    }
                                                    $departmentStats[$jurusanName]++;
                                                }
                                                arsort($departmentStats);
                                            @endphp
                                            
                                            @foreach(array_slice($departmentStats, 0, 5) as $dept => $count)
                                                <li>{{ $dept }} ({{ $count }} alumni)</li>
                                            @endforeach
                                        </ul>
                                        @if(count($departmentStats) > 5)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Menampilkan 5 dari {{ count($departmentStats) }} jurusan</small>
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
        @if(count($students) > 0)
            @if($reportType == 'employment')
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
                
            @elseif($reportType == 'education')
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
                
            @elseif($reportType == 'unemployment')
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
                    $statusCounts = [
                        $reportData->where('status_setelah_lulus', 'kerja')->count(),
                        $reportData->where('status_setelah_lulus', 'kuliah')->count(),
                        $reportData->where('status_setelah_lulus', 'belum_kerja')->count()
                    ];
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
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table {
            white-space: nowrap;
        }
    }
</style>
@endpush
