@extends('layout.app')

@section('title', 'Laporan Tracer Study')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="font-weight-bold mb-0">Laporan Tracer Study</h4>
                            <p class="text-muted">Visualisasi dan analisis data tracer study alumni</p>
                        </div>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-gradient-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-cloud-download"></i> Export Data Mentah
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-header">
                                        <i class="mdi mdi-information text-info"></i> Export Semua Data Alumni
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                        href="{{ route('operator.reports.export', ['format' => 'excel']) }}">
                                        <i class="mdi mdi-file-excel text-success mr-2"></i>
                                        <span>Excel Spreadsheet</span>
                                        <small class="text-muted d-block">Format .xlsx dengan styling</small>
                                    </a>
                                    <a class="dropdown-item"
                                        href="{{ route('operator.reports.export', ['format' => 'pdf']) }}">
                                        <i class="mdi mdi-file-pdf text-danger mr-2"></i>
                                        <span>PDF Document</span>
                                        <small class="text-muted d-block">Format .pdf siap cetak</small>
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-gradient-info ml-2" data-toggle="modal"
                                data-target="#generateReportModal">
                                <i class="mdi mdi-chart-box"></i> Buat Laporan Khusus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Filter Data</h5>
                            <form id="filterForm" class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tahun Kelulusan</label>
                                        <select class="form-control" id="filterYear" name="year">
                                            <option value="">Semua Tahun</option>
                                            @php
                                                $currentYear = date('Y');
                                                for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                                    echo "<option value='$i'>$i</option>";
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Jurusan</label>
                                        <select class="form-control" id="filterDepartment" name="department">
                                            <option value="">Semua Jurusan</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->jurusan }}">{{ $dept->jurusan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" id="filterStatus" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="kerja">Bekerja</option>
                                            <option value="kuliah">Kuliah</option>
                                            <option value="belum_kerja">Belum Bekerja</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" id="applyFilter" class="btn btn-primary mr-2">
                                        <i class="mdi mdi-filter"></i> Terapkan
                                    </button>
                                    <button type="button" id="resetFilter" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title text-white mb-0">Total Alumni</h4>
                                <div class="icon-md bg-white-transparent">
                                    <i class="mdi mdi-account-multiple text-white"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 mt-3 text-white" id="summaryTotal">{{ $summary['total'] }}</h3>
                            <div class="mt-4">
                                <span class="font-weight-medium">Total keseluruhan alumni yang terdata</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title text-white mb-0">Bekerja</h4>
                                <div class="icon-md bg-white-transparent">
                                    <i class="mdi mdi-briefcase text-white"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <h3 class="mb-0 mt-3 text-white" id="summaryWorking">{{ $summary['working'] }}</h3>
                                    <div class="mt-2">
                                        <span class="font-weight-medium">Alumni yang bekerja</span>
                                    </div>
                                </div>
                                <div class="col-4 text-right">
                                    <span class="h4 text-white">
                                        {{ $summary['total'] > 0 ? round(($summary['working'] / $summary['total']) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card bg-gradient-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title text-white mb-0">Kuliah</h4>
                                <div class="icon-md bg-white-transparent">
                                    <i class="mdi mdi-school text-white"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <h3 class="mb-0 mt-3 text-white" id="summaryStudying">{{ $summary['studying'] }}</h3>
                                    <div class="mt-2">
                                        <span class="font-weight-medium">Alumni yang melanjutkan kuliah</span>
                                    </div>
                                </div>
                                <div class="col-4 text-right">
                                    <span class="h4 text-white">
                                        {{ $summary['total'] > 0 ? round(($summary['studying'] / $summary['total']) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title text-white mb-0">Belum Bekerja</h4>
                                <div class="icon-md bg-white-transparent">
                                    <i class="mdi mdi-account-search text-white"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <h3 class="mb-0 mt-3 text-white" id="summaryUnemployed">{{ $summary['unemployed'] }}
                                    </h3>
                                    <div class="mt-2">
                                        <span class="font-weight-medium">Alumni yang masih mencari pekerjaan</span>
                                    </div>
                                </div>
                                <div class="col-4 text-right">
                                    <span class="h4 text-white">
                                        {{ $summary['total'] > 0 ? round(($summary['unemployed'] / $summary['total']) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Charts -->
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Status Alumni per Tahun Kelulusan</h4>
                            <p class="card-description">Tren status alumni berdasarkan tahun kelulusan</p>
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="yearlyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Distribusi per Jurusan</h4>
                            <p class="card-description">Distribusi status alumni berdasarkan jurusan</p>
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Charts -->
            <div class="row">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Lama Waktu Tunggu Kerja</h4>
                            <p class="card-description">Waktu tunggu alumni sebelum mendapatkan pekerjaan pertama</p>
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="waitingTimeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Rentang Gaji</h4>
                            <p class="card-description">Distribusi rentang gaji alumni yang bekerja</p>
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="salaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Jenjang Pendidikan</h4>
                            <p class="card-description">Distribusi jenjang pendidikan lanjutan alumni</p>
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="educationLevelChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Representations Section -->
            @if (isset($students) && is_countable($students) && count($students) > 0)
                <div class="row">
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Visualisasi Data</h5>
                                <div class="chart-container" style="height: 300px; max-height: 300px; overflow: hidden;">
                                    @if (isset($reportType))
                                        @if ($reportType == 'employment')
                                            <canvas id="employmentChart" height="300"></canvas>
                                        @elseif($reportType == 'education')
                                            <canvas id="educationChart" height="300"></canvas>
                                        @elseif($reportType == 'unemployment')
                                            <canvas id="unemploymentChart" height="300"></canvas>
                                        @else
                                            <canvas id="generalChart" height="300"></canvas>
                                        @endif
                                    @else
                                        <canvas id="generalChart" height="300"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Data Tabel</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Kelulusan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $index => $student)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $student->nama }}</td>
                                                    <td>{{ $student->jurusan }}</td>
                                                    <td>{{ $student->tahun_kelulusan }}</td>
                                                    <td>{{ $student->status }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="mdi mdi-chart-box mr-2"></i>Buat Laporan Khusus
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('operator.reports.generate') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="mdi mdi-file-document text-primary mr-1"></i>Tipe Laporan
                                    </label>
                                    <select class="form-control form-control-lg" name="report_type" required>
                                        <option value="general">📊 Laporan Umum - Data ringkas semua alumni</option>
                                        <option value="employment">💼 Data Pekerjaan - Detail alumni yang bekerja</option>
                                        <option value="education">🎓 Data Pendidikan - Detail alumni yang kuliah</option>
                                        <option value="unemployment">📋 Data Belum Bekerja - Alumni yang belum kerja
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="mdi mdi-calendar text-info mr-1"></i>Tahun Kelulusan
                                    </label>
                                    <select class="form-control" name="year">
                                        <option value="">🕐 Semua Tahun</option>
                                        @php
                                            $currentYear = date('Y');
                                            for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                        @endphp
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="mdi mdi-school text-success mr-1"></i>Jurusan
                                    </label>
                                    <select class="form-control" name="department">
                                        <option value="">🎯 Semua Jurusan</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->jurusan }}">{{ $dept->jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="mdi mdi-account-check text-warning mr-1"></i>Status Alumni
                                    </label>
                                    <select class="form-control" name="status">
                                        <option value="">✅ Semua Status</option>
                                        <option value="kerja">💼 Bekerja</option>
                                        <option value="kuliah">🎓 Kuliah</option>
                                        <option value="belum_kerja">📋 Belum Bekerja</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="mdi mdi-download text-primary mr-1"></i>Format Output
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="formatWeb" name="format"
                                            value="web" checked>
                                        <label class="custom-control-label" for="formatWeb">
                                            <div class="media">
                                                <i class="mdi mdi-monitor-dashboard text-info mr-2 mt-1"
                                                    style="font-size: 20px;"></i>
                                                <div class="media-body">
                                                    <strong>Tampilkan di Browser</strong>
                                                    <small class="text-muted d-block">Preview langsung di halaman
                                                        baru</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="formatPDF" name="format"
                                            value="pdf">
                                        <label class="custom-control-label" for="formatPDF">
                                            <div class="media">
                                                <i class="mdi mdi-file-pdf text-danger mr-2 mt-1"
                                                    style="font-size: 20px;"></i>
                                                <div class="media-body">
                                                    <strong>Download PDF</strong>
                                                    <small class="text-muted d-block">Format siap cetak & share</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="formatExcel"
                                            name="format" value="excel">
                                        <label class="custom-control-label" for="formatExcel">
                                            <div class="media">
                                                <i class="mdi mdi-file-excel text-success mr-2 mt-1"
                                                    style="font-size: 20px;"></i>
                                                <div class="media-body">
                                                    <strong>Download Excel</strong>
                                                    <small class="text-muted d-block">Data untuk analisis lebih
                                                        lanjut</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="mdi mdi-information mr-2"></i>
                            <strong>Info:</strong> Laporan akan dihasilkan berdasarkan filter yang Anda pilih.
                            Untuk data lengkap, gunakan fitur "Export Data Mentah" di atas.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            <i class="mdi mdi-close mr-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="mdi mdi-download mr-1"></i>Buat Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Filter functionality
            $('#applyFilter').click(function() {
                updateDashboard();
            });

            $('#resetFilter').click(function() {
                $('#filterForm')[0].reset();
                updateDashboard();
            });

            function updateDashboard() {
                const year = $('#filterYear').val();
                const department = $('#filterDepartment').val();
                const status = $('#filterStatus').val();

                // Show loading indicators
                $('#summaryTotal, #summaryWorking, #summaryStudying, #summaryUnemployed').html(
                    '<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only">Loading...</span></div>'
                );

                // Get updated data via AJAX
                $.ajax({
                    url: '{{ route('operator.reports.data') }}',
                    method: 'GET',
                    data: {
                        type: 'summary',
                        year: year,
                        department: department,
                        status: status
                    },
                    success: function(response) {
                        // Update summary cards
                        $('#summaryTotal').text(response.total);
                        $('#summaryWorking').text(response.working);
                        $('#summaryStudying').text(response.studying);
                        $('#summaryUnemployed').text(response.unemployed);

                        // Update charts (implement as needed)
                        // For simplicity, just refresh the page in this example
                        // location.reload();
                    },
                    error: function() {
                        // Show error state
                        $('#summaryTotal, #summaryWorking, #summaryStudying, #summaryUnemployed').text(
                            'Error');
                        toastr.error('Gagal memperbarui data');
                    }
                });
            }

            // Initialize charts
            initializeCharts();

            function initializeCharts() {
                // 1. Yearly trend chart
                const yearlyTrendCtx = document.getElementById('yearlyTrendChart').getContext('2d');
                const yearlyTrendChart = new Chart(yearlyTrendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trends['labels']),
                        datasets: [{
                                label: 'Bekerja',
                                data: @json($trends['working']),
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                tension: 0.4
                            },
                            {
                                label: 'Kuliah',
                                data: @json($trends['studying']),
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.4
                            },
                            {
                                label: 'Belum Bekerja',
                                data: @json($trends['unemployed']),
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 2,
                                tension: 0.4
                            }
                        ]
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
                                    text: 'Tahun Kelulusan'
                                }
                            }
                        }
                    }
                });

                // 2. Department distribution chart
                const departmentData = @json($departments);
                const departmentLabels = departmentData.map(item => item.jurusan);
                const departmentCounts = departmentData.map(item => item.total);

                const departmentCtx = document.getElementById('departmentChart').getContext('2d');
                const departmentChart = new Chart(departmentCtx, {
                    type: 'bar',
                    data: {
                        labels: departmentLabels,
                        datasets: [{
                            label: 'Jumlah Alumni',
                            data: departmentCounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderWidth: 0
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
                                    text: 'Jurusan'
                                }
                            }
                        }
                    }
                });

                // 3. Waiting time chart
                const waitingTimeData = @json($waitingTime);
                const waitingTimeCtx = document.getElementById('waitingTimeChart').getContext('2d');
                const waitingTimeChart = new Chart(waitingTimeCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(waitingTimeData),
                        datasets: [{
                            data: Object.values(waitingTimeData),
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // 4. Salary ranges chart
                const salaryData = @json($salaryRanges);
                const salaryCtx = document.getElementById('salaryChart').getContext('2d');
                const salaryChart = new Chart(salaryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(salaryData),
                        datasets: [{
                            data: Object.values(salaryData),
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // 5. Education level chart
                const educationData = @json($educationLevels);
                const educationCtx = document.getElementById('educationLevelChart').getContext('2d');
                const educationChart = new Chart(educationCtx, {
                    type: 'polarArea',
                    data: {
                        labels: Object.keys(educationData),
                        datasets: [{
                            data: Object.values(educationData),
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(153, 102, 255, 0.8)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }
        });

        // Enhanced Export Experience
        $(document).ready(function() {
            // Add loading animation to export buttons
            $('.dropdown-item[href*="export"]').on('click', function(e) {
                const $this = $(this);
                const originalHtml = $this.html();

                // Add loading state
                $this.html('<i class="mdi mdi-loading mdi-spin mr-2"></i> Memproses...');
                $this.addClass('disabled');

                // Show notification
                if (typeof toastr !== 'undefined') {
                    if ($this.attr('href').includes('excel')) {
                        toastr.info('Memproses file Excel... Mohon tunggu!', 'Export Data');
                    } else if ($this.attr('href').includes('pdf')) {
                        toastr.info('Memproses file PDF... Mohon tunggu!', 'Export Data');
                    }
                }

                // Reset after a delay (simulating processing time)
                setTimeout(function() {
                    $this.html(originalHtml);
                    $this.removeClass('disabled');

                    if (typeof toastr !== 'undefined') {
                        toastr.success('File berhasil didownload!', 'Export Berhasil');
                    }
                }, 3000);
            });

            // Enhanced form submission for custom reports
            $('#generateReportModal form').on('submit', function(e) {
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalHtml = $submitBtn.html();

                // Add loading state
                $submitBtn.html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Memproses...');
                $submitBtn.prop('disabled', true);

                // Get selected format
                const selectedFormat = $('input[name="format"]:checked').val();
                const reportType = $('select[name="report_type"]').val();

                // Show appropriate notification
                if (typeof toastr !== 'undefined') {
                    let message = 'Sedang memproses laporan...';
                    if (selectedFormat === 'pdf') {
                        message = 'Menghasilkan laporan PDF...';
                    } else if (selectedFormat === 'excel') {
                        message = 'Menghasilkan laporan Excel...';
                    } else if (selectedFormat === 'web') {
                        message = 'Menyiapkan laporan untuk ditampilkan...';
                    }
                    toastr.info(message, 'Generate Laporan');
                }

                // Reset button after delay if it's not a download
                if (selectedFormat === 'web') {
                    setTimeout(function() {
                        $submitBtn.html(originalHtml);
                        $submitBtn.prop('disabled', false);
                        $('#generateReportModal').modal('hide');
                    }, 2000);
                } else {
                    // For downloads, reset after longer delay
                    setTimeout(function() {
                        $submitBtn.html(originalHtml);
                        $submitBtn.prop('disabled', false);
                        $('#generateReportModal').modal('hide');

                        if (typeof toastr !== 'undefined') {
                            toastr.success('Laporan berhasil didownload!', 'Laporan Siap');
                        }
                    }, 4000);
                }
            });

            // Format preview when radio button changes
            $('input[name="format"]').on('change', function() {
                const selectedFormat = $(this).val();
                const $submitBtn = $('#generateReportModal form button[type="submit"]');

                switch (selectedFormat) {
                    case 'web':
                        $submitBtn.html('<i class="mdi mdi-eye mr-1"></i>Lihat Laporan');
                        break;
                    case 'pdf':
                        $submitBtn.html('<i class="mdi mdi-file-pdf mr-1"></i>Download PDF');
                        break;
                    case 'excel':
                        $submitBtn.html('<i class="mdi mdi-file-excel mr-1"></i>Download Excel');
                        break;
                    default:
                        $submitBtn.html('<i class="mdi mdi-download mr-1"></i>Buat Laporan');
                }
            });

            // Report type change handler for better UX
            $('select[name="report_type"]').on('change', function() {
                const reportType = $(this).val();
                const $statusField = $('select[name="status"]');

                // Auto-adjust status filter based on report type
                switch (reportType) {
                    case 'employment':
                        $statusField.val('kerja');
                        break;
                    case 'education':
                        $statusField.val('kuliah');
                        break;
                    case 'unemployment':
                        $statusField.val('belum_kerja');
                        break;
                    default:
                        $statusField.val('');
                }
            });

            // Add tooltips to buttons if Bootstrap tooltip is available
            if (typeof $().tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .chart-container {
            position: relative;
            margin: auto;
        }

        .card-description {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .icon-md {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-white-transparent {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .card-body {
            position: relative;
        }

        /* Enhanced Modal Styling */
        .modal-header.bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-bottom: none;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control-lg {
            padding: 12px 16px;
            font-size: 14px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .custom-control-label {
            cursor: pointer;
            position: relative;
            padding-left: 35px;
        }

        .custom-control-label::before {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #667eea;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #667eea;
            border-color: #667eea;
        }

        .media {
            align-items: center;
        }

        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 1px solid #2196f3;
            border-radius: 8px;
            color: #0d47a1;
        }

        /* Enhanced Dropdown Styling */
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: none;
            padding: 8px 0;
        }

        .dropdown-header {
            padding: 8px 16px;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
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

        /* Button enhancements */
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

        .btn-gradient-info {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            border: none;
            box-shadow: 0 2px 4px rgba(54, 209, 220, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient-info:hover {
            background: linear-gradient(135deg, #2ec4cf 0%, #4f7bd3 100%);
            box-shadow: 0 4px 8px rgba(54, 209, 220, 0.4);
            transform: translateY(-1px);
        }

        /* Loading animation for export */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
