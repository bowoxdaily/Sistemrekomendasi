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
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-export"></i> Export Data Mentah
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('operator.reports.export', ['format' => 'excel']) }}">
                                    <i class="mdi mdi-file-excel text-success mr-2"></i> Export Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('operator.reports.export', ['format' => 'pdf']) }}">
                                    <i class="mdi mdi-file-pdf text-danger mr-2"></i> Export PDF
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary ml-2" data-toggle="modal" data-target="#generateReportModal">
                            <i class="mdi mdi-file-chart"></i> Buat Laporan Khusus
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
                                            for($i = $currentYear; $i >= $currentYear - 10; $i--) {
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
                                        @foreach($departments as $dept)
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
                                <h3 class="mb-0 mt-3 text-white" id="summaryUnemployed">{{ $summary['unemployed'] }}</h3>
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
        @if(isset($students) && is_countable($students) && count($students) > 0)
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Visualisasi Data</h5>
                        <div class="chart-container" style="height: 300px; max-height: 300px; overflow: hidden;">
                            @if(isset($reportType))
                                @if($reportType == 'employment')
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
                                    @foreach($students as $index => $student)
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Laporan Khusus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('operator.reports.generate') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Laporan</label>
                        <select class="form-control" name="report_type" required>
                            <option value="general">Laporan Umum</option>
                            <option value="employment">Data Pekerjaan</option>
                            <option value="education">Data Pendidikan</option>
                            <option value="unemployment">Data Belum Bekerja</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tahun Kelulusan</label>
                        <select class="form-control" name="year">
                            <option value="">Semua Tahun</option>
                            @php
                                $currentYear = date('Y');
                                for($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                    echo "<option value='$i'>$i</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select class="form-control" name="department">
                            <option value="">Semua Jurusan</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->jurusan }}">{{ $dept->jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">Semua Status</option>
                            <option value="kerja">Bekerja</option>
                            <option value="kuliah">Kuliah</option>
                            <option value="belum_kerja">Belum Bekerja</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Format Output</label>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="format" value="web" checked>
                                Tampilkan di Browser
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="format" value="pdf">
                                Download PDF
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="format" value="excel">
                                Download Excel
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Laporan</button>
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
                    $('#summaryTotal, #summaryWorking, #summaryStudying, #summaryUnemployed').text('Error');
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
                    datasets: [
                        {
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
</style>
@endpush
