@extends('layout.app')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin mb-3">
            <div class="row">
                <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->name }}</h3>
                    <h6 class="font-weight-normal mb-0">
                        <span class="text-primary">Dashboard Guru</span> - Sistem Tracer Study & Rekomendasi Karir
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
                                    <span class="spinner-border spinner-border-sm text-primary" role="status"
                                        id="weather-loading">
                                        <span class="sr-only">Loading...</span>
                                    </span>
                                </h2>
                                <div class="weather-details mt-2">
                                    <span id="weather-description" class="badge badge-info mr-1"></span>
                                    <span id="weather-humidity" class="badge badge-secondary" title="Humidity"><i
                                            class="mdi mdi-water"></i> --</span>
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
                            <p class="mb-4">Total Alumni Terdaftar</p>
                            <p class="fs-30 mb-2" id="school-count">Loading...</p>
                            <p id="school-percentage-change">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4">Alumni Sudah Bekerja</p>
                            <p class="fs-30 mb-2" id="operator-count">Loading...</p>
                            <p id="operator-percentage">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <p class="mb-4">Alumni Melanjutkan Kuliah</p>
                            <p class="fs-30 mb-2" id="total-student-count">Loading...</p>
                            <p id="student-percentage">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4">Alumni Belum Bekerja</p>
                            <p class="fs-30 mb-2" id="total-alumni">Loading...</p>
                            <p id="alumni-percentage">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Blog Terbaru -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Blog Terbaru</h4>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-view-list mr-1"></i> Lihat Semua
                        </a>
                    </div>

                    <!-- Loading state -->
                    <div class="text-center" id="blog-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat blog terbaru...</p>
                    </div>

                    <!-- Table container -->
                    <div class="table-responsive d-none" id="blog-table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="blog-table-body">
                                <!-- Blog rows will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty state -->
                    <div class="text-center py-4 d-none" id="blog-empty">
                        <div class="text-muted">Belum ada blog yang dipublikasikan</div>
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
                    <p class="card-title mb-2">Status Alumni Setelah Lulus</p>
                    <p class="font-weight-500 mb-2">Distribusi status alumni setelah lulus dari sekolah.</p>
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
                    <div class="chart-container d-flex justify-content-center align-items-center"
                        style="height: 260px; width: 100%; max-width: 320px; margin: 0 auto; overflow: visible;">
                        <canvas id="tracer-status-chart" width="260" height="260" style="display: block;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="card-title mb-0">Detail Tracer Study Alumni</p>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle py-1 px-2" type="button"
                                id="chartFilterDropdown" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Tipe Data
                            </button>
                            <div class="dropdown-menu" aria-labelledby="chartFilterDropdown">
                                <a class="dropdown-item active" href="#" data-chart-type="jobType">Jenis
                                    Pekerjaan</a>
                                <a class="dropdown-item" href="#" data-chart-type="eduMajor">Program Studi</a>
                                <a class="dropdown-item" href="#" data-chart-type="salary">Rentang Gaji</a>
                                <a class="dropdown-item" href="#" data-chart-type="jobMatch">Kesesuaian
                                    Pekerjaan</a>
                            </div>
                        </div>
                    </div>
                    <p class="font-weight-500 mb-2">Data detail dari hasil tracer study alumni lulusan sekolah.</p>
                    <div id="detail-chart-legend" class="chartjs-legend mt-2 mb-2"></div>
                    <div class="chart-container" style="height: 300px; width: 100%; max-width: 380px; margin: 0 auto;">
                        <canvas id="tracer-detail-chart" width="340" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Chart Tren Lulusan -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <p class="card-title mb-2">Tren Status Alumni per Tahun Kelulusan</p>
                    <p class="font-weight-500 mb-2">Visualisasi tren status alumni (bekerja, kuliah, belum bekerja)
                        berdasarkan tahun kelulusan.</p>
                    <div class="chart-container" style="height: 320px; width: 100%; max-width: 700px; margin: 0 auto;">
                        <canvas id="tracer-trend-chart" width="600" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
                    // Update temperature and location
                    $('#temperature').text(Math.round(data.main.temp));
                    $('#weather-location').text(data.name);
                    $('#weather-loading').hide();

                    // Update additional weather details
                    $('#weather-description').text(data.weather[0].description.toUpperCase());
                    $('#weather-humidity').html(
                        `<i class="mdi mdi-water"></i> ${data.main.humidity}%`);
                    $('#weather-updated').text(`Updated: ${new Date().toLocaleTimeString()}`);

                    // Update weather icon based on condition
                    const iconClass = getWeatherIconClass(data.weather[0].icon);
                    $('#weather-temp i').attr('class', iconClass + ' mr-2');
                },
                error: function(xhr, status, error) {
                    console.error("Weather API error:", error);
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

        $(document).ready(function() {
            // Load weather data
            loadWeatherData();

            // Setup refresh button
            $('#refresh-weather').on('click', function() {
                loadWeatherData();
            });

            // Auto-refresh weather every 30 minutes
            setInterval(loadWeatherData, 1800000);

            // Fetch dashboard stats untuk guru - endpoint khusus alumni
            $.ajax({
                url: _baseURL + 'api/stats/alumni-guru',
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update the dashboard cards dengan data alumni
                        $('#school-count').text(response.data.total_alumni || 0);
                        $('#school-percentage-change').text((response.data.alumni_growth || 0) +
                            '% dari bulan lalu');

                        $('#operator-count').text(response.data.working_alumni || 0);
                        $('#operator-percentage').text((response.data.working_percentage || 0) +
                            '% dari total alumni');

                        $('#total-student-count').text(response.data.studying_alumni || 0);
                        $('#student-percentage').text((response.data.studying_percentage || 0) +
                            '% dari total alumni');

                        $('#total-alumni').text(response.data.unemployed_alumni || 0);
                        $('#alumni-percentage').text((response.data.unemployed_percentage || 0) +
                            '% dari total alumni');
                    }
                },
                error: function() {
                    console.error('Failed to load alumni stats');
                    // Set default values jika gagal load
                    $('#school-count').text('0');
                    $('#school-percentage-change').text('Data tidak tersedia');
                    $('#operator-count').text('0');
                    $('#operator-percentage').text('Data tidak tersedia');
                    $('#total-student-count').text('0');
                    $('#student-percentage').text('Data tidak tersedia');
                    $('#total-alumni').text('0');
                    $('#alumni-percentage').text('Data tidak tersedia');
                }
            });

            // Fetch tracer study data
            $.ajax({
                url: _baseURL + 'api/stats/tracer',
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        // STATUS CHART
                        const summary = response.data.summary;
                        // Tampilkan angka di atas chart
                        $('#total-alumni-count').text(summary.total);
                        $('#working-count').text(summary.working);
                        $('#study-count').text(summary.study);
                        $('#unemployed-count').text(summary.unemployed);

                        const statusChart = new Chart(document.getElementById('tracer-status-chart')
                            .getContext('2d'), {
                                type: 'doughnut',
                                data: {
                                    labels: ['Bekerja', 'Kuliah', 'Belum Bekerja'],
                                    datasets: [{
                                        data: [summary.working, summary.study, summary
                                            .unemployed
                                        ],
                                        backgroundColor: [
                                            'rgba(40, 167, 69, 0.7)',
                                            'rgba(23, 162, 184, 0.7)',
                                            'rgba(255, 193, 7, 0.7)'
                                        ],
                                        borderColor: [
                                            'rgba(40, 167, 69, 1)',
                                            'rgba(23, 162, 184, 1)',
                                            'rgba(255, 193, 7, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: true
                                        }
                                    }
                                }
                            });

                        // DETAIL CHART (default: Jenis Pekerjaan)
                        let detailType = 'jobType';

                        function renderDetailChart(type) {
                            let chartData = {};
                            let label = '';
                            if (type === 'jobType') {
                                chartData = response.data.details.jobTypes;
                                label = 'Jenis Pekerjaan';
                            } else if (type === 'eduMajor') {
                                chartData = response.data.details.eduMajors;
                                label = 'Program Studi';
                            } else if (type === 'salary') {
                                chartData = response.data.details.salaryRanges;
                                label = 'Rentang Gaji';
                            } else if (type === 'jobMatch') {
                                chartData = response.data.details.jobMatch;
                                label = 'Kesesuaian Pekerjaan';
                            }
                            const labels = Object.keys(chartData);
                            const data = Object.values(chartData);
                            if (window.detailChartInstance) window.detailChartInstance.destroy();
                            window.detailChartInstance = new Chart(document.getElementById(
                                'tracer-detail-chart').getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: label,
                                        data: data,
                                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                        renderDetailChart('jobType');
                        // Dropdown event
                        $("[data-chart-type]").on('click', function(e) {
                            e.preventDefault();
                            $(this).siblings().removeClass('active');
                            $(this).addClass('active');
                            renderDetailChart($(this).data('chart-type'));
                        });

                        // CHART TREN LULUSAN
                        const trends = response.data.trends;
                        if (trends && trends.years) {
                            if (window.trendChartInstance) window.trendChartInstance.destroy();
                            window.trendChartInstance = new Chart(document.getElementById(
                                'tracer-trend-chart').getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: trends.years,
                                    datasets: [{
                                            label: 'Bekerja',
                                            data: trends.working,
                                            borderColor: 'rgba(40, 167, 69, 1)',
                                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Kuliah',
                                            data: trends.study,
                                            borderColor: 'rgba(23, 162, 184, 1)',
                                            backgroundColor: 'rgba(23, 162, 184, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Belum Bekerja',
                                            data: trends.unemployed,
                                            borderColor: 'rgba(255, 193, 7, 1)',
                                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: true
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                error: function() {
                    alert('Gagal memuat data tracer study!');
                }
            });

            // Fetch blog data
            fetchLatestBlogs();
        });

        // Function to fetch latest blogs
        function fetchLatestBlogs() {
            $.ajax({
                url: _baseURL + 'api/blogs/latest',
                method: 'GET',
                data: {
                    limit: 3 // Ambil 5 blog terbaru untuk tabel
                },
                success: function(response) {
                    $('#blog-loading').hide();

                    if (response.status === 'success' && response.data.length > 0) {
                        displayBlogsTable(response.data);
                        $('#blog-table-container').removeClass('d-none');
                    } else {
                        $('#blog-empty').removeClass('d-none');
                    }
                },
                error: function() {
                    console.error('Failed to load blog data');
                    $('#blog-loading').hide();
                    $('#blog-empty').removeClass('d-none');
                }
            });
        }

        // Function to display blogs in table format
        function displayBlogsTable(blogs) {
            const tableBody = $('#blog-table-body');
            tableBody.empty();

            // Filter hanya blog yang dipublikasikan
            const publishedBlogs = blogs.filter(blog => blog.is_published);

            if (publishedBlogs.length === 0) {
                // Show empty state jika tidak ada blog yang dipublikasikan
                $('#blog-table-container').addClass('d-none');
                $('#blog-empty').removeClass('d-none');
                return;
            }

            publishedBlogs.forEach(function(blog) {
                const tableRow = createBlogTableRow(blog);
                if (tableRow) {
                    tableBody.append(tableRow);
                }
            });
        }

        // Function to create blog table row HTML
        function createBlogTableRow(blog) {
            const publishDate = new Date(blog.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const statusBadge = blog.is_published ?
                '<span class="badge badge-success">Dipublikasikan</span>' :
                '<span class="badge badge-warning">Draft</span>';

            const categoryBadge = blog.category ?
                `<span class="badge badge-info">${blog.category}</span>` :
                '<span class="text-muted">-</span>';

            return `
                <tr>
                    <td>${blog.title.length > 50 ? blog.title.substring(0, 50) + '...' : blog.title}</td>
                    <td>${categoryBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${publishDate}</td>
                    <td>
                        <div class="btn-group">
                            <a href="${_baseURL}blog/${blog.slug}" 
                               class="btn btn-sm btn-outline-info" 
                               target="_blank"
                               title="Lihat Blog">
                                <i class="mdi mdi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Dashboard spacing fixes */
        .grid-margin {
            margin-bottom: 1.25rem;
        }

        .row {
            margin-top: 0 !important;
        }

        .content-wrapper {
            padding-top: 15px !important;
        }

        /* Card styles */
        .card {
            margin-bottom: 1.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Weather card fix */
        .card-people {
            margin-top: 0 !important;
        }

        /* Chart container fixes */
        .chart-container {
            margin-bottom: 0;
        }

        /* Table styling */
        .table-responsive {
            border-radius: 0.375rem;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .badge {
            font-size: 0.75rem;
        }

        /* Button styling */
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush
