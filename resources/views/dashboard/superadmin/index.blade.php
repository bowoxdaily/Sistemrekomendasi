@extends('layout.app')

@section('title','Dashboard Superadmin')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->name }}</h3>
                    <h6 class="font-weight-normal mb-0">
                        <span class="text-primary">Superadmin Panel</span> - Sistem Tracer Study & Rekomendasi Karir
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
                            <p class="mb-4">Total Sekolah</p>
                            <p class="fs-30 mb-2" id="school-count">Loading...</p>
                            <p id="school-percentage-change">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4">Total Operator</p>
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
                            <p class="mb-4">Total Siswa</p>
                            <p class="fs-30 mb-2" id="total-student-count">Loading...</p>
                            <p id="student-percentage">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4">Total Alumni</p>
                            <p class="fs-30 mb-2" id="total-alumni">Loading...</p>
                            <p id="alumni-percentage">Loading...</p>
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
@endsection