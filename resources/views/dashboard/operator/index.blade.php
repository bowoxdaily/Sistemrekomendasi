<!-- views/dashboard.blade.php -->
@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->operator->nama_lengkap ?? Auth::user()->name }}</h3>
                    <h6 class="font-weight-normal mb-0">
                        <span class="text-primary">Administrator Panel</span> - Sistem Tracer Study & Rekomendasi Karir
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
                                <a class="dropdown-item" href="#">January - March</a>
                                <a class="dropdown-item" href="#">March - June</a>
                                <a class="dropdown-item" href="#">June - August</a>
                                <a class="dropdown-item" href="#">August - November</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Pengaturan Cepat</h4>
                    <div class="row">
                        <div class="col-md-6 text-center mb-3">
                            <a href="{{ route('operator.settings.logo') }}" class="btn btn-outline-primary btn-icon-text p-3 w-100 h-100">
                                <i class="mdi mdi-image-area-close btn-icon-prepend"></i>
                                <span class="d-block mt-2">Ganti Logo</span>
                            </a>
                        </div>
                        <div class="col-md-6 text-center mb-3">
                            <a href="{{ route('operator.settings.school') }}" class="btn btn-outline-info btn-icon-text p-3 w-100 h-100">
                                <i class="mdi mdi-school btn-icon-prepend"></i>
                                <span class="d-block mt-2">Info Sekolah</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card tale-bg">
                <div class="card-people mt-auto">
                    <img src="{{ asset('admin/images/dashboard/people.svg') }}" alt="people">
                    <div class="weather-info">
                        <div class="d-flex">
                            <div>
                                <h2 class="mb-0 font-weight-normal" id="weather-temp">
                                    <i class="icon-sun mr-2"></i><span id="temperature">--</span><sup>C</sup>
                                    <span class="spinner-border spinner-border-sm text-primary" role="status" id="weather-loading">
                                        <span class="sr-only">Loading...</span>
                                    </span>
                                </h2>
                                <div class="weather-details mt-2">
                                    <span id="weather-description" class="badge badge-info mr-1"></span>
                                    <span id="weather-humidity" class="badge badge-secondary" title="Humidity"><i class="mdi mdi-water"></i> --</span>
                                </div>
                            </div>
                            <div class="ml-2">
                                <h4 class="location font-weight-normal" id="weather-location">Indramayu</h4>
                                <h6 class="font-weight-normal">Terisi</h6>
                                <p class="text-muted small" id="weather-updated">Memuat data cuaca...</p>
                            </div>
                        </div>
                        <button id="refresh-weather" class="btn btn-sm btn-outline-info mt-2">
                            <i class="mdi mdi-refresh mr-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin transparent">
            <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Siswa</p>
                            <p class="fs-30 mb-2" id="student-count">Loading...</p>
                            <p id="percentage-change">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4">Total Bookings</p>
                            <p class="fs-30 mb-2">61344</p>
                            <p>22.00% (30 days)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <p class="mb-4">Number of Meetings</p>
                            <p class="fs-30 mb-2">34040</p>
                            <p>2.00% (30 days)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4">Number of Clients</p>
                            <p class="fs-30 mb-2">47033</p>
                            <p>0.22% (30 days)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - Adjusted sizing -->
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <p class="card-title mb-2">Tracer Study: Status Setelah Lulus</p>
                    <p class="font-weight-500 mb-2">Distribusi alumni berdasarkan status setelah lulus dalam 3 tahun terakhir.</p>
                    <div class="d-flex flex-wrap mb-3">
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Total Alumni</p>
                            <h3 class="text-primary fs-30 font-weight-medium" id="total-alumni-count">0</h3>
                        </div>
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Bekerja</p>
                            <h3 class="text-success fs-30 font-weight-medium" id="working-count">0</h3>
                        </div>
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Kuliah</p>
                            <h3 class="text-info fs-30 font-weight-medium" id="study-count">0</h3>
                        </div>
                        <div class="mt-2">
                            <p class="text-muted mb-1">Belum Bekerja</p>
                            <h3 class="text-warning fs-30 font-weight-medium" id="unemployed-count">0</h3>
                        </div>
                    </div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="tracer-status-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="card-title mb-0">Tracer Study: Detail</p>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle py-1 px-2" type="button" id="chartFilterDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Tipe Data
                            </button>
                            <div class="dropdown-menu" aria-labelledby="chartFilterDropdown">
                                <a class="dropdown-item active" href="#" data-chart-type="jobType">Jenis Pekerjaan</a>
                                <a class="dropdown-item" href="#" data-chart-type="eduMajor">Program Studi</a>
                                <a class="dropdown-item" href="#" data-chart-type="salary">Rentang Gaji</a>
                                <a class="dropdown-item" href="#" data-chart-type="jobMatch">Kesesuaian Pekerjaan</a>
                            </div>
                        </div>
                    </div>
                    <p class="font-weight-500 mb-2">Visualisasi data detail dari hasil tracer study alumni.</p>
                    <div id="detail-chart-legend" class="chartjs-legend mt-2 mb-2"></div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="tracer-detail-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracer Study Trends - Adjusted sizing -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <p class="card-title mb-1">Tren Status Alumni (5 Tahun Terakhir)</p>
                    <p class="font-weight-500 mb-2">Perubahan status alumni dari tahun ke tahun.</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="tracer-trend-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Original charts section - Optimized whitespace -->
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <p class="card-title mb-2">Order Details</p>
                    <p class="font-weight-500 mb-3">The total number of sessions within the date range.</p>
                    <div class="d-flex flex-wrap mb-3">
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Order value</p>
                            <h3 class="text-primary fs-30 font-weight-medium">12.3k</h3>
                        </div>
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Orders</p>
                            <h3 class="text-primary fs-30 font-weight-medium">14k</h3>
                        </div>
                        <div class="mr-5 mt-2">
                            <p class="text-muted mb-1">Users</p>
                            <h3 class="text-primary fs-30 font-weight-medium">71.56%</h3>
                        </div>
                        <div class="mt-2">
                            <p class="text-muted mb-1">Downloads</p>
                            <h3 class="text-primary fs-30 font-weight-medium">34040</h3>
                        </div>
                    </div>
                    <div class="chart-container" style="height: 220px;">
                        <canvas id="order-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between mb-2">
                        <p class="card-title mb-0">Sales Report</p>
                        <a href="#" class="text-info">View all</a>
                    </div>
                    <p class="font-weight-500 mb-2">The total number of sessions within the date range.</p>
                    <div id="sales-legend" class="chartjs-legend mt-2 mb-2"></div>
                    <div class="chart-container" style="height: 220px;">
                        <canvas id="sales-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include Chart.js if not already included -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Check if student profile is complete (only for students)
            @if (Auth::check() && Auth::user()->role === 'siswa')
                checkStudentProfile();
            @endif

            // Function to check if student profile is complete
            function checkStudentProfile() {
                $.ajax({
                    url: '{{ route('api.student.profile.check') }}',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (!response.complete) {
                            // Show modal if profile is incomplete
                            $('#profileModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking profile:', xhr);
                    }
                });
            }

            // Handle "Lengkapi Profil" button click
            $('#goToProfileBtn').click(function() {
                window.location.href = '{{ route('student.profile.edit') }}';
            });

            // Load student count statistics via AJAX
            function loadStudentStats() {
                $.ajax({
                    url: '{{ route('api.stats.students') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#student-count').text(response.data.count);

                            // Format percentage with proper color
                            const percentageChange = response.data.percentage_change;
                            const percentageText = (percentageChange >= 0 ? '+' : '') +
                                percentageChange + '% (30 days)';
                            const textClass = percentageChange >= 0 ? 'text' : 'text-danger';

                            $('#percentage-change').html(
                                `<span class="${textClass}">${percentageText}</span>`);
                        }
                    },
                    error: function(xhr) {
                        $('#student-count').text('N/A');
                        $('#percentage-change').text('Data tidak tersedia');
                    }
                });
            }

            // Load student statistics
            loadStudentStats();

            // Fetch tracer study data and initialize charts
            function loadTracerData() {
                console.log('Loading tracer data...');
                
                // Show loading state
                $('#total-alumni-count').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $('#working-count').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $('#study-count').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                $('#unemployed-count').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                
                $.ajax({
                    url: '{{ route("api.stats.tracer") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Tracer data received:', response);
                        
                        if (response.status === 'success') {
                            initTracerCharts(response.data);
                        } else {
                            console.error('Error in tracer data response:', response.message);
                            showErrorState();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading tracer data:", error);
                        console.error("Response:", xhr.responseText);
                        showErrorState();
                    }
                });
            }
            
            function showErrorState() {
                $('#total-alumni-count').text('Error');
                $('#working-count').text('Error');
                $('#study-count').text('Error');
                $('#unemployed-count').text('Error');
                
                // Show error message on chart areas
                $('#tracer-status-chart').parent().append(
                    '<div class="alert alert-danger mt-3">' +
                    '<i class="fas fa-exclamation-circle mr-2"></i> ' +
                    'Failed to load tracer study data. Please check the console for details.' +
                    '</div>'
                );
            }
            
            // Initialize tracer study charts
            function initTracerCharts(data) {
                console.log('Initializing charts with data:', data);
                
                // Update summary counts
                $('#total-alumni-count').text(data.summary.total || 0);
                $('#working-count').text(data.summary.working || 0);
                $('#study-count').text(data.summary.study || 0);
                $('#unemployed-count').text(data.summary.unemployed || 0);

                // Status chart (pie chart)
                const statusCtx = document.getElementById('tracer-status-chart');
                if (statusCtx) {
                    if (window.statusChart) window.statusChart.destroy();

                    window.statusChart = new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Bekerja', 'Kuliah', 'Belum Bekerja'],
                            datasets: [{
                                data: [
                                    data.summary.working || 0,
                                    data.summary.study || 0,
                                    data.summary.unemployed || 0
                                ],
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 206, 86, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Changed to false to fill container
                            layout: {
                                padding: {
                                    top: 10,
                                    right: 30,
                                    bottom: 10, 
                                    left: 10
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 15,
                                        padding: 10,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Detail chart (bar chart) - Default to job types
                updateDetailChart('jobType', data.details);

                // Trend chart (line chart)
                const trendCtx = document.getElementById('tracer-trend-chart');
                if (trendCtx && data.trends) {
                    if (window.trendChart) window.trendChart.destroy();

                    window.trendChart = new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: data.trends.years || [],
                            datasets: [
                                {
                                    label: 'Bekerja',
                                    data: data.trends.working || [],
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    tension: 0.4
                                },
                                {
                                    label: 'Kuliah',
                                    data: data.trends.study || [],
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    tension: 0.4
                                },
                                {
                                    label: 'Belum Bekerja',
                                    data: data.trends.unemployed || [],
                                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                    borderColor: 'rgba(255, 206, 86, 1)',
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Changed to false to fill container
                            layout: {
                                padding: {
                                    top: 10,
                                    right: 20,
                                    bottom: 10,
                                    left: 10
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        padding: 5
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Alumni'
                                    }
                                },
                                x: {
                                    ticks: {
                                        padding: 5
                                    },
                                    title: {
                                        display: true,
                                        text: 'Tahun'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        }
                    });
                }
            }

            // Update detail chart based on selection
            function updateDetailChart(chartType, details) {
                const detailCtx = document.getElementById('tracer-detail-chart');
                if (!detailCtx || !details) return;

                if (window.detailChart) window.detailChart.destroy();

                let chartData = {
                    labels: [],
                    datasets: []
                };

                switch (chartType) {
                    case 'jobType':
                        chartData.labels = details.jobTypes ? Object.keys(details.jobTypes) : [];
                        chartData.datasets.push({
                            label: 'Jumlah',
                            data: details.jobTypes ? Object.values(details.jobTypes) : [],
                            backgroundColor: 'rgba(75, 192, 192, 0.8)'
                        });
                        break;
                    case 'eduMajor':
                        chartData.labels = details.eduMajors ? Object.keys(details.eduMajors) : [];
                        chartData.datasets.push({
                            label: 'Jumlah',
                            data: details.eduMajors ? Object.values(details.eduMajors) : [],
                            backgroundColor: 'rgba(54, 162, 235, 0.8)'
                        });
                        break;
                    case 'salary':
                        chartData.labels = details.salaryRanges ? Object.keys(details.salaryRanges) : [];
                        chartData.datasets.push({
                            label: 'Jumlah',
                            data: details.salaryRanges ? Object.values(details.salaryRanges) : [],
                            backgroundColor: 'rgba(153, 102, 255, 0.8)'
                        });
                        break;
                    case 'jobMatch':
                        chartData.labels = ['Sesuai Jurusan', 'Tidak Sesuai'];
                        chartData.datasets.push({
                            label: 'Jumlah',
                            data: [
                                details.jobMatch ? details.jobMatch.match : 0,
                                details.jobMatch ? details.jobMatch.notMatch : 0
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ]
                        });
                        break;
                }

                window.detailChart = new Chart(detailCtx, {
                    type: chartType === 'jobMatch' ? 'pie' : 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Changed to false to fill container
                        layout: {
                            padding: {
                                top: 10,
                                right: chartType === 'jobMatch' ? 30 : 10,
                                bottom: 10,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: chartType === 'jobMatch',
                                position: 'right'
                            }
                        },
                        scales: chartType !== 'jobMatch' ? {
                            y: {
                                beginAtZero: true
                            }
                        } : {}
                    }
                });

                // Update the dropdown button text
                const selectedText = $(`[data-chart-type="${chartType}"]`).text();
                $('#chartFilterDropdown').text(selectedText);
            }

            // Chart type dropdown handler
            $(document).on('click', '[data-chart-type]', function(e) {
                e.preventDefault();
                $('.dropdown-item').removeClass('active');
                $(this).addClass('active');

                const chartType = $(this).data('chart-type');

                // Get the latest data (could also cache this instead of fetching again)
                $.ajax({
                    url: '{{ route("api.stats.tracer") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            updateDetailChart(chartType, response.data.details);
                        }
                    }
                });
            });

            // Real-time weather update
            function loadWeatherData() {
                // Show loading indicator
                $('#weather-loading').show();
                $('#temperature').text('--');
                
                // OpenWeatherMap API call with correct endpoint
                $.ajax({
                    url: 'https://api.openweathermap.org/data/2.5/weather',
                    data: {
                        lat: -6.3373, 
                        lon: 108.3207,
                        units: 'metric',
                        appid: 'a4efb19d624defa1c2c0631d2cc3cf89'
                    },
                    success: function(data) {
                        console.log('Weather data:', data);
                        
                        // Update temperature and location
                        $('#temperature').text(Math.round(data.main.temp));
                        $('#weather-location').text(data.name);
                        $('#weather-loading').hide();
                        
                        // Handle the refresh button click
                        $('#refresh-weather').on('click', function() {
                            loadWeatherData();
                        });
                        
                        // Update additional weather details
                        $('#weather-description').text(data.weather[0].description.toUpperCase());
                        $('#weather-humidity').html(`<i class="mdi mdi-water"></i> ${data.main.humidity}%`);
                        $('#weather-updated').text(`Updated: ${new Date().toLocaleTimeString()}`);
                        
                        // Update weather icon based on condition
                        const iconClass = getWeatherIconClass(data.weather[0].icon);
                        $('#weather-temp i').attr('class', iconClass + ' mr-2');
                    },
                    error: function(xhr, status, error) {
                        console.error("Weather API error:", error);
                        console.error("Response:", xhr.responseText);
                        $('#temperature').text('31'); // Fallback to default
                        $('#weather-location').text('Indramayu');
                        $('#weather-updated').text('Unable to update weather data');
                        $('#weather-loading').hide();
                    }
                });
            }
            
            // Helper function to map OpenWeatherMap icons to our icon classes
            function getWeatherIconClass(iconCode) {
                const iconMap = {
                    '01d': 'icon-sun', // clear sky day
                    '01n': 'icon-moon', // clear sky night
                    '02d': 'icon-cloud-sun', // few clouds day
                    '02n': 'icon-cloud-moon', // few clouds night
                    '03d': 'icon-cloud', // scattered clouds
                    '03n': 'icon-cloud', 
                    '04d': 'icon-clouds', // broken clouds
                    '04n': 'icon-clouds',
                    '09d': 'icon-cloud-rain', // shower rain
                    '09n': 'icon-cloud-rain',
                    '10d': 'icon-cloud-sun-rain', // rain day
                    '10n': 'icon-cloud-moon-rain', // rain night
                    '11d': 'icon-cloud-lightning', // thunderstorm
                    '11n': 'icon-cloud-lightning',
                    '13d': 'icon-snowflake', // snow
                    '13n': 'icon-snowflake',
                    '50d': 'icon-fog', // mist/fog
                    '50n': 'icon-fog'
                };
                
                return iconMap[iconCode] || 'icon-sun'; // Default to sun icon
            }
            
            // Initialize WebSocket for real-time updates
            function initRealTimeUpdates() {
                // Check if WebSocket is supported
                if ('WebSocket' in window) {
                    try {
                        // Replace with your WebSocket server URL
                        const socket = new WebSocket('ws://your-websocket-server-url');
                        
                        socket.onopen = function() {
                            console.log('WebSocket connection established');
                        };
                        
                        socket.onmessage = function(event) {
                            const data = JSON.parse(event.data);
                            
                            // Handle different types of real-time updates
                            if (data.type === 'student_count') {
                                $('#student-count').text(data.value);
                            } 
                            else if (data.type === 'tracer_data') {
                                // Update tracer study charts with new data
                                initTracerCharts(data.value);
                            }
                        };
                        
                        socket.onerror = function(error) {
                            console.error('WebSocket error:', error);
                        };
                        
                        socket.onclose = function() {
                            console.log('WebSocket connection closed');
                            // Try to reconnect after 5 seconds
                            setTimeout(initRealTimeUpdates, 5000);
                        };
                    } catch (e) {
                        console.error('Failed to connect to WebSocket server:', e);
                    }
                } else {
                    console.log('WebSocket not supported. Falling back to polling.');
                    // Continue with regular polling as fallback
                }
            }
            
            // Load tracer study data                        initRealTimeUpdates();            // Initialize real-time updates                        setInterval(loadWeatherData, 1800000); // 30 minutes            loadWeatherData();            // Load weather data immediately and refresh every 30 minutes            loadTracerData();
            
            // Make sure we load all data properly and in the right order
            $(function() {
                // Load student statistics
                loadStudentStats();
                
                // Load weather data immediately 
                loadWeatherData();
                
                // Load tracer study data
                loadTracerData();
                
                // Initialize real-time updates
                // initRealTimeUpdates(); // Commented out until WebSocket server is ready
                
                // Set up refresh intervals
                setInterval(loadWeatherData, 1800000); // 30 minutes
                setInterval(loadTracerData, 300000);   // 5 minutes
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .weather-details {
            display: flex;
            flex-wrap: wrap;
        }
        
        .text-highlight {
            animation: highlight 1s ease;
        }
        
        @keyframes highlight {
            0% { color: inherit; }
            50% { color: #4d83ff; }
            100% { color: inherit; }
        }
        
        #weather-description {
            text-transform: capitalize;
        }
        
        .weather-info {
            padding: 10px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.4); /* More transparent */
            backdrop-filter: blur(5px); /* Add blur effect */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            position: relative;
            z-index: 2;
        }
        
        /* Make sure the weather-info container blends better with the background image */
        .card-people {
            position: relative;
        }
        
        .card-people img {
            position: relative;
            z-index: 1;
        }
        
        /* Remove any button styling that might add white background */
        #refresh-weather {
            background: transparent;
            border-color: rgba(75, 192, 192, 0.5);
            color: #4b8a8a;
            font-size: 0.7rem;
            padding: 3px 8px;
        }
        
        #refresh-weather:hover {
            background-color: rgba(75, 192, 192, 0.2);
        }
        
        /* Make badges more translucent */
        .weather-details .badge {
            background-color: rgba(0, 123, 255, 0.7);
            backdrop-filter: blur(3px);
        }
        
        /* Ensure text is readable */
        .weather-info h2, .weather-info h4, .weather-info h6 {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
        }
        
        /* Fix dashboard width and overflow issues */
        .chart-container {
            width: 100%;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 300px; /* Set minimum height */
        }
        
        /* Ensure canvas fills container */
        canvas {
            max-width: 100%;
            height: 100% !important; /* Important to ensure full height */
        }
        
        /* Fix card overflow issues */
        .card {
            overflow: hidden;
        }
        
        .card-body {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        
        /* Reduce margin between cards */
        .grid-margin {
            margin-bottom: 1rem;
        }
        
        /* Reduce text margins */
        .card-title {
            margin-bottom: 0.5rem;
        }
        
        p.font-weight-500 {
            margin-bottom: 0.5rem;
        }
        
        /* Make metrics more compact */
        .d-flex.flex-wrap {
            margin-bottom: 0.5rem;
        }
        
        .d-flex.flex-wrap .mr-5 {
            margin-right: 1rem !important;
        }
        
        /* Adjust responsive styling */
        @media (max-width: 768px) {
            .chart-container {
                height: 350px !important; /* Taller on mobile */
            }
        }
    </style>
@endpush
