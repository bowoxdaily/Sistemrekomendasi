@extends('layout.app')

@section('title', 'Visualisasi Data Tracer Study')

@push('styles')
    <style>
        .chart-container {
            position: relative;
            margin: auto;
        }

        .card-description {
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 0.9rem;
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

        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Enhanced footer positioning */
        .main-panel {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-panel .content-wrapper {
            padding-top: 20px;
            padding-bottom: 60px;
            flex: 1;
        }

        .main-panel footer {
            margin-top: auto;
        }

        .card-body {
            position: relative;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Card loading state */
        .card-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 5;
            visibility: hidden;
        }

        .chart-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title .badge {
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 250px;
            }

            .summary-stats {
                flex-direction: column;
                text-align: center;
            }

            .summary-stats>div {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="font-weight-bold mb-0">Visualisasi Data Tracer Study</h4>
                    <p class="text-muted">Visualisasi dan analisis data tracer study alumni</p>
                </div>
                <div>
                    <div class="btn-group">
                        <select id="export-type" class="form-control mr-2" style="width:auto;display:inline-block;">
                            <option value="general">General</option>
                            <option value="employment">Data Kerja</option>
                            <option value="education">Data Kuliah</option>
                            <option value="unemployment">Belum Kerja</option>
                        </select>
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-export"></i> Export Data
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" id="export-pdf">
                                <i class="mdi mdi-file-pdf text-danger mr-2"></i> Export sebagai PDF
                            </a>
                            <a class="dropdown-item" href="#" id="export-excel">
                                <i class="mdi mdi-file-excel text-success mr-2"></i> Export sebagai Excel
                            </a>
                        </div>
                    </div>
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
                                <select class="form-control" id="year-filter" name="year">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($years ?? [] as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jurusan</label>
                                <select class="form-control" id="department-filter" name="department">
                                    <option value="">Semua Jurusan</option>
                                    @foreach ($departments ?? [] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="status-filter" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="kerja">Bekerja</option>
                                    <option value="kuliah">Kuliah</option>
                                    <option value="belum_kerja">Belum Bekerja</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" id="apply-filter" class="btn btn-primary mr-2">
                                <i class="mdi mdi-filter"></i> Terapkan
                            </button>
                            <button type="button" id="reset-filter" class="btn btn-outline-secondary">
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
                    <h3 class="mb-0 mt-3 text-white" id="summary-total">
                        <div class="spinner-border spinner-border-sm text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </h3>
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
                            <h3 class="mb-0 mt-3 text-white" id="summary-working">
                                <div class="spinner-border spinner-border-sm text-light" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </h3>
                            <div class="mt-2">
                                <span class="font-weight-medium">Alumni yang bekerja</span>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <span class="h4 text-white" id="summary-working-percent">0%</span>
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
                            <h3 class="mb-0 mt-3 text-white" id="summary-studying">
                                <div class="spinner-border spinner-border-sm text-light" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </h3>
                            <div class="mt-2">
                                <span class="font-weight-medium">Alumni yang melanjutkan kuliah</span>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <span class="h4 text-white" id="summary-studying-percent">0%</span>
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
                            <h3 class="mb-0 mt-3 text-white" id="summary-unemployed">
                                <div class="spinner-border spinner-border-sm text-light" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </h3>
                            <div class="mt-2">
                                <span class="font-weight-medium">Alumni yang masih mencari pekerjaan</span>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <span class="h4 text-white" id="summary-unemployed-percent">0%</span>
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
                    <div class="chart-title">
                        <h4 class="card-title">Status Alumni per Tahun Kelulusan</h4>
                        <span class="badge badge-info" id="trend-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Tren status alumni berdasarkan tahun kelulusan</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="trend-chart"></canvas>
                        <div class="card-loading" id="trend-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Distribusi per Jurusan</h4>
                        <span class="badge badge-info" id="departments-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Distribusi status alumni berdasarkan jurusan</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="departments-chart"></canvas>
                        <div class="card-loading" id="departments-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Additional Charts -->
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Lama Waktu Tunggu Kerja</h4>
                        <span class="badge badge-info" id="waiting-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Waktu tunggu alumni sebelum mendapatkan pekerjaan pertama</p>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="waitingTime-chart"></canvas>
                        <div class="card-loading" id="waitingTime-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Rentang Gaji</h4>
                        <span class="badge badge-info" id="salary-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Distribusi rentang gaji alumni yang bekerja</p>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="salary-chart"></canvas>
                        <div class="card-loading" id="salary-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Jenjang Pendidikan</h4>
                        <span class="badge badge-info" id="education-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Distribusi jenjang pendidikan lanjutan alumni</p>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="education-chart"></canvas>
                        <div class="card-loading" id="education-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Status Chart and Data Table -->
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Status Alumni</h4>
                        <span class="badge badge-info" id="status-chart-count">0 Data</span>
                    </div>
                    <p class="card-description">Distribusi status alumni setelah lulus</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="status-chart"></canvas>
                        <div class="card-loading" id="status-chart-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="chart-title">
                        <h4 class="card-title">Detail Alumni</h4>
                        <span class="badge badge-info" id="alumni-data-count">0 Data</span>
                    </div>
                    <p class="card-description">Data alumni berdasarkan filter yang dipilih</p>
                    <div class="table-responsive" style="height: 300px; overflow-y: auto;">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Tahun Lulus</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="alumni-data-table">
                                <!-- Data will be loaded here -->
                                <tr>
                                    <td colspan="4" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="card-loading" id="alumni-table-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" style="display: none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Memuat data...</p>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global chart objects
        const charts = {};

        // Chart colors configuration
        const chartColors = {
            working: {
                bg: 'rgba(75, 192, 192, 0.8)',
                border: 'rgba(75, 192, 192, 1)'
            },
            studying: {
                bg: 'rgba(54, 162, 235, 0.8)',
                border: 'rgba(54, 162, 235, 1)'
            },
            unemployed: {
                bg: 'rgba(255, 206, 86, 0.8)',
                border: 'rgba(255, 206, 86, 1)'
            },
            other: {
                bg: 'rgba(255, 99, 132, 0.8)',
                border: 'rgba(255, 99, 132, 1)'
            }
        };
        $(document).ready(function() {

            // Filter functionality
            $('#apply-filter').click(function() {
                updateDashboard();
            });

            $('#reset-filter').click(function() {
                $('#filterForm')[0].reset();
                updateDashboard();
            });

            function updateDashboard() {
                const year = $('#year-filter').val();
                const department = $('#department-filter').val();
                const status = $('#status-filter').val();

                // Show loading indicators for summary cards
                $('#summary-total, #summary-working, #summary-studying, #summary-unemployed').html(
                    '<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only">Loading...</span></div>'
                );

                // Show loading indicators for charts
                showAllChartLoadings();

                // Show main loading overlay
                $('#loading-overlay').show();

                // Get updated data via AJAX
                $.ajax({
                    url: '{{ route('superadmin.visualizations.data') }}',
                    method: 'GET',
                    data: {
                        type: 'summary',
                        year: year,
                        department: department,
                        status: status
                    },
                    success: function(response) {
                        if (response.status === 'success' && !response.data.error) {
                            // Update summary cards
                            $('#summary-total').text(response.data.total || 0);
                            $('#summary-working').text(response.data.working || 0);
                            $('#summary-studying').text(response.data.studying || 0);
                            $('#summary-unemployed').text(response.data.unemployed || 0);

                            // Update percentage displays
                            $('#summary-working-percent').text((response.data.workingPercentage || 0) +
                                '%');
                            $('#summary-studying-percent').text((response.data.studyingPercentage ||
                                0) + '%');
                            $('#summary-unemployed-percent').text((response.data.unemployedPercentage ||
                                0) + '%');

                            // Update status chart count
                            $('#status-chart-count').text((response.data.total || 0) + ' Data');

                            // Refresh all charts
                            refreshCharts();
                        } else {
                            console.error('API returned error:', response);
                            toastr.error(response.data.error || 'Gagal memuat data');
                            hideAllChartLoadings();
                            $('#loading-overlay').hide();
                        }
                    },
                    error: function() {
                        // Show error state
                        $('#summary-total, #summary-working, #summary-studying, #summary-unemployed')
                            .text('Error');
                        hideAllChartLoadings();
                        $('#loading-overlay').hide();
                        toastr.error('Gagal memperbarui data');
                    }
                });
            }

            // Helper functions for chart loadings
            function showAllChartLoadings() {
                $('.card-loading').css('visibility', 'visible');
            }

            function hideAllChartLoadings() {
                $('.card-loading').css('visibility', 'hidden');
            }

            // Initialize charts
            function initializeCharts() {
                // 1. Status Chart (Donut)
                const statusCtx = document.getElementById('status-chart').getContext('2d');
                charts.status = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Bekerja', 'Kuliah', 'Belum Bekerja'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: [
                                chartColors.working.bg,
                                chartColors.studying.bg,
                                chartColors.unemployed.bg,
                            ],
                            borderWidth: 1,
                            borderColor: [
                                chartColors.working.border,
                                chartColors.studying.border,
                                chartColors.unemployed.border,
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100)
                                            .toFixed(1) : 0;
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // 2. Yearly trend chart
                const trendCtx = document.getElementById('trend-chart').getContext('2d');
                charts.trend = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                                label: 'Bekerja',
                                data: [],
                                backgroundColor: chartColors.working.bg,
                                borderColor: chartColors.working.border,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 4
                            },
                            {
                                label: 'Kuliah',
                                data: [],
                                backgroundColor: chartColors.studying.bg,
                                borderColor: chartColors.studying.border,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 4
                            },
                            {
                                label: 'Belum Bekerja',
                                data: [],
                                backgroundColor: chartColors.unemployed.bg,
                                borderColor: chartColors.unemployed.border,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Alumni'
                                },
                                ticks: {
                                    stepSize: 1
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

                // 3. Departments chart
                const deptCtx = document.getElementById('departments-chart').getContext('2d');
                charts.departments = new Chart(deptCtx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Jumlah Alumni',
                            data: [],
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Alumni'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jurusan'
                                },
                                ticks: {
                                    maxRotation: 45
                                }
                            }
                        }
                    }
                });

                // 4. Waiting time chart
                const waitingTimeCtx = document.getElementById('waitingTime-chart').getContext('2d');
                charts.waitingTime = new Chart(waitingTimeCtx, {
                    type: 'pie',
                    data: {
                        labels: ['< 3 bulan', '3-6 bulan', '6-12 bulan', '> 12 bulan'],
                        datasets: [{
                            data: [0, 0, 0, 0],
                            backgroundColor: [
                                chartColors.working.bg,
                                chartColors.studying.bg,
                                chartColors.unemployed.bg,
                                chartColors.other.bg
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

                // 5. Salary ranges chart
                const salaryCtx = document.getElementById('salary-chart').getContext('2d');
                charts.salary = new Chart(salaryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['< 2 juta', '2-4 juta', '4-8 juta', '> 8 juta'],
                        datasets: [{
                            data: [0, 0, 0, 0],
                            backgroundColor: [
                                chartColors.working.bg,
                                chartColors.studying.bg,
                                chartColors.unemployed.bg,
                                chartColors.other.bg
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

                // 6. Education level chart
                const educationCtx = document.getElementById('education-chart').getContext('2d');
                charts.education = new Chart(educationCtx, {
                    type: 'polarArea',
                    data: {
                        labels: ['D3', 'S1', 'S2', 'Lainnya'],
                        datasets: [{
                            data: [0, 0, 0, 0],
                            backgroundColor: [
                                chartColors.working.bg,
                                chartColors.studying.bg,
                                chartColors.unemployed.bg,
                                chartColors.other.bg
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

            // Refresh all charts with new data
            function refreshCharts() {
                $('#loading-overlay').show();
                showAllChartLoadings();

                const year = $('#year-filter').val();
                const department = $('#department-filter').val();
                const status = $('#status-filter')
                    .val(); // Update all charts with new data in a single AJAX request
                $.ajax({
                    url: '{{ route('superadmin.visualizations.data') }}',
                    method: 'GET',
                    data: {
                        type: 'all',
                        year: year,
                        department: department,
                        status: status
                    },
                    success: function(response) {
                        console.log('API Response:', response); // Debug output
                        if (response.status === 'success' && !response.data.error) {
                            try {
                                console.log('Chart data received:', response.data); // Debug output

                                // Update status chart
                                if (charts.status && response.data.status) {
                                    console.log('Status data:', response.data.status); // Debug output
                                    charts.status.data.datasets[0].data = [
                                        response.data.status.working || 0,
                                        response.data.status.studying || 0,
                                        response.data.status.unemployed || 0
                                    ];
                                    charts.status.update();
                                } else {
                                    console.log('Status chart not updated. Chart exists:', !!charts
                                        .status, 'Data exists:', !!response.data.status);
                                }

                                // Update trend chart
                                if (charts.trend && response.data.trend) {
                                    charts.trend.data.labels = response.data.trend.labels || [];
                                    charts.trend.data.datasets[0].data = response.data.trend.working ||
                                        [];
                                    charts.trend.data.datasets[1].data = response.data.trend.studying ||
                                        [];
                                    charts.trend.data.datasets[2].data = response.data.trend
                                        .unemployed || [];
                                    charts.trend.update();

                                    // Update trend chart count
                                    let totalTrendData = 0;
                                    if (response.data.trend.working && response.data.trend.working
                                        .length > 0) {
                                        totalTrendData = response.data.trend.working.reduce((a, b) =>
                                                a + b, 0) +
                                            (response.data.trend.studying || []).reduce((a, b) => a + b,
                                                0) +
                                            (response.data.trend.unemployed || []).reduce((a, b) => a +
                                                b, 0);
                                    }
                                    $('#trend-chart-count').text(totalTrendData + ' Data');
                                }

                                // Update departments chart
                                if (charts.departments && response.data.departments) {
                                    const deptLabels = response.data.departments.map(item => item
                                        .department || '');
                                    const deptData = response.data.departments.map(item => item.total ||
                                        0);

                                    charts.departments.data.labels = deptLabels;
                                    charts.departments.data.datasets[0].data = deptData;
                                    charts.departments.update();

                                    // Update departments chart count
                                    const totalDeptData = deptData.reduce((a, b) => a + b, 0);
                                    $('#departments-chart-count').text(totalDeptData + ' Data');
                                }

                                // Update detail charts
                                updateDetailCharts(response.data);

                                // Update alumni table
                                updateAlumniTable(response.data.alumni);

                                // Update alumni count
                                if (response.data.alumni) {
                                    $('#alumni-data-count').text(response.data.alumni.length + ' Data');
                                }
                            } catch (err) {
                                console.error('Error updating charts:', err);
                                toastr.error('Terjadi kesalahan saat memperbarui visualisasi');
                            }
                        } else {
                            console.error('API returned error:', response);
                            toastr.error(response.data.error || 'Gagal memuat data visualisasi');
                        }

                        hideAllChartLoadings();
                        $('#loading-overlay').hide();
                    },
                    error: function() {
                        toastr.error('Gagal memuat data visualisasi');
                        hideAllChartLoadings();
                        $('#loading-overlay').hide();
                    }
                });
            } // Update the detail charts (salary, education, waitingTime)
            function updateDetailCharts(data) {
                // --- WAITING TIME CHART ---
                if (charts.waitingTime) {
                    // Label urut sesuai chart
                    const waitingLabels = ['< 3 bulan', '3-6 bulan', '6-12 bulan', '> 12 bulan'];
                    let waitingData = [0, 0, 0, 0];
                    if (data.waitingTime) {
                        waitingLabels.forEach((label, idx) => {
                            waitingData[idx] = data.waitingTime[label] || 0;
                        });
                    }
                    charts.waitingTime.data.labels = waitingLabels;
                    charts.waitingTime.data.datasets[0].data = waitingData;
                    charts.waitingTime.update();
                    const totalWaiting = waitingData.reduce((a, b) => a + b, 0);
                    $('#waiting-chart-count').text(totalWaiting + ' Data');
                }

                // --- SALARY CHART ---
                if (charts.salary) {
                    const salaryLabels = ['< 2 juta', '2-4 juta', '4-8 juta', '> 8 juta'];
                    let salaryData = [0, 0, 0, 0];
                    if (data.salary) {
                        salaryLabels.forEach((label, idx) => {
                            salaryData[idx] = data.salary[label] || 0;
                        });
                    }
                    charts.salary.data.labels = salaryLabels;
                    charts.salary.data.datasets[0].data = salaryData;
                    charts.salary.update();
                    const totalSalary = salaryData.reduce((a, b) => a + b, 0);
                    $('#salary-chart-count').text(totalSalary + ' Data');
                }

                // --- EDUCATION CHART ---
                if (charts.education) {
                    // Label urut sesuai chart
                    const eduLabels = ['D3', 'S1', 'S2', 'Lainnya'];
                    let eduData = [0, 0, 0, 0];
                    if (data.education) {
                        eduLabels.forEach((label, idx) => {
                            eduData[idx] = data.education[label] || 0;
                        });
                    }
                    charts.education.data.labels = eduLabels;
                    charts.education.data.datasets[0].data = eduData;
                    charts.education.update();
                    const totalEdu = eduData.reduce((a, b) => a + b, 0);
                    $('#education-chart-count').text(totalEdu + ' Data');
                }
            }

            // Update the alumni data table
            function updateAlumniTable(alumni) {
                const tableBody = document.getElementById('alumni-data-table');
                if (!tableBody || !alumni) return;

                // Clear the table
                tableBody.innerHTML = '';

                if (alumni.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML =
                        '<td colspan="4" class="text-center">Tidak ada data alumni yang sesuai dengan filter</td>';
                    tableBody.appendChild(row);
                    return;
                }

                // Add alumni data
                alumni.forEach((item, index) => {
                    if (index >= 20) return; // Limit to 20 rows

                    const row = document.createElement('tr');

                    // Get status badge class
                    let statusClass, statusText;
                    switch (item.status) {
                        case 'kerja':
                            statusClass = 'success';
                            statusText = 'Bekerja';
                            break;
                        case 'kuliah':
                            statusClass = 'info';
                            statusText = 'Kuliah';
                            break;
                        case 'belum_kerja':
                            statusClass = 'warning';
                            statusText = 'Belum Bekerja';
                            break;
                        default:
                            statusClass = 'secondary';
                            statusText = item.status || 'Tidak Diketahui';
                    }

                    row.innerHTML = `
                        <td>${item.nama_lengkap}</td>
                        <td>${item.jurusan}</td>
                        <td>${item.tahun_lulus}</td>
                        <td><span class="badge badge-${statusClass}">${statusText}</span></td>
                    `;

                    tableBody.appendChild(row);
                });

                if (alumni.length > 20) {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="4" class="text-center text-muted">
                        <small>Menampilkan 20 dari ${alumni.length} data</small>
                    </td>`;
                    tableBody.appendChild(row);
                }
            }

            // Export handlers
            $('#export-pdf').click(function(e) {
                e.preventDefault();
                const year = $('#year-filter').val();
                const department = $('#department-filter').val();
                const status = $('#status-filter').val();
                const type = $('#export-type').val();
                window.open(
                    `{{ route('superadmin.visualizations.export.pdf') }}?year=${year}&department=${department}&status=${status}&type=${type}`,
                    '_blank');
            });

            $('#export-excel').click(function(e) {
                e.preventDefault();
                const year = $('#year-filter').val();
                const department = $('#department-filter').val();
                const status = $('#status-filter').val();
                const type = $('#export-type').val();
                window.open(
                    `{{ route('superadmin.visualizations.export.excel') }}?year=${year}&department=${department}&status=${status}&type=${type}`,
                    '_blank');
            }); // Initial data load
            console.log('Initializing visualization page...');
            initializeCharts();
            console.log('Charts initialized, updating dashboard...');
            updateDashboard();
            console.log('Dashboard update initiated.');
        });
    </script>
@endpush
