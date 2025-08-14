@extends('layout.app')

@section('title', 'Dashboard | Siswa')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Siswa</h4>
                                <div>
                                    <button class="btn btn-success btn-icon-text mr-2" id="import-siswa">
                                        <i class="mdi mdi-file-excel btn-icon-prepend"></i>
                                        Import Data
                                    </button>
                                    <button class="btn btn-primary btn-icon-text" id="tambah-siswa">
                                        <i class="mdi mdi-plus btn-icon-prepend"></i>
                                        Tambah Siswa
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <div class="input-group">
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari siswa..." aria-label="Cari siswa">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="search-button">
                                                <i class="mdi mdi-magnify"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <select class="form-control" id="filter-jurusan">
                                        <option value="">Semua Jurusan</option>
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <select class="form-control" id="filter-tahun-lulus">
                                        <option value="">Semua Tahun Lulus</option>
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                </div>
                            </div>

                            <div class="d-md-none mb-3">
                                <!-- Mobile view toggle -->
                                <div class="btn-group btn-block">
                                    <button type="button" class="btn btn-outline-primary active" id="table-view-btn">
                                        <i class="mdi mdi-table"></i> Tabel
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="card-view-btn">
                                        <i class="mdi mdi-view-grid"></i> Kartu
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive" id="table-view">
                                <table class="table table-hover" id="siswa-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 60px;">Aksi</th>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th class="d-none d-md-table-cell">Jurusan</th>
                                            <th class="d-none d-md-table-cell">Tempat Lahir</th>
                                            <th class="d-none d-md-table-cell">Tanggal Lahir</th>
                                            <th class="d-none d-lg-table-cell">Alamat</th>
                                            <th>Tahun Lulus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="row d-none" id="card-view">
                                <!-- Mobile card view -->
                                <div class="col-12" id="card-container">
                                    <!-- Cards will be generated by JavaScript -->
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <div class="dataTables_info" id="data-count">Menampilkan 0 dari 0 data</div>
                                <ul class="pagination pagination-primary" id="pagination">
                                    <!-- Pagination akan diisi oleh JavaScript -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="tambahSiswaModal" tabindex="-1" role="dialog" aria-labelledby="tambahSiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSiswaModalLabel">Tambah Akun Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-tambah-akun-siswa">
                        @csrf
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="nisn">NISN</label>
                            <input type="text" class="form-control" id="nisn" required>
                        </div>
                        <div class="form-group">
                            <label for="jurusan_id">Jurusan</label>
                            <select class="form-control" id="jurusan_id" required>
                                <option value="">Pilih Jurusan</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_lulus">Status Kelulusan</label>
                            <select class="form-control" id="status_lulus" required>
                                <option value="">Pilih Status</option>
                                <option value="belum">Belum Lulus</option>
                                <option value="lulus">Lulus</option>
                            </select>
                        </div>
                        <div class="form-group" id="tahun-lulus-container">
                            <label for="tahun_lulus">Tahun Lulus</label>
                            <input type="number" class="form-control" id="tahun_lulus" min="2000" max="2099">
                            <small class="form-text text-muted">Diisi jika status kelulusan "Lulus"</small>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpan-akun-siswa">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Siswa -->
    <div class="modal fade" id="importSiswaModal" tabindex="-1" role="dialog" aria-labelledby="importSiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSiswaModalLabel">Import Data Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-import-siswa" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file-import">Pilih File Excel/CSV</label>
                            <input type="file" class="form-control-file" id="file-import" accept=".xlsx,.xls,.csv"
                                required>
                            <small class="form-text text-muted">Format file: .xlsx, .xls, atau .csv</small>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="mdi mdi-information-outline mr-2"></i>
                            <strong>Informasi:</strong> Pastikan format file sesuai dengan template. Data yang sudah
                            diimport tidak dapat dibatalkan.
                        </div>
                        <div class="mt-3">
                            <a href="#" id="download-template" class="text-primary">
                                <i class="mdi mdi-download"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="proses-import">Import</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Siswa -->
    <div class="modal fade" id="editSiswaModal" tabindex="-1" role="dialog" aria-labelledby="editSiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSiswaModalLabel">Edit Data Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-siswa">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-id">

                        <div class="form-group">
                            <label for="edit-nama-lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit-nama-lengkap" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-nisn">NISN</label>
                            <input type="text" class="form-control" id="edit-nisn" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-jurusan">Jurusan</label>
                            <select class="form-control" id="edit-jurusan" required>
                                <option value="">Pilih Jurusan</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit-status-lulus">Status Kelulusan</label>
                            <select class="form-control" id="edit-status-lulus" required>
                                <option value="">Pilih Status</option>
                                <option value="belum">Belum Lulus</option>
                                <option value="lulus">Lulus</option>
                            </select>
                        </div>

                        <div class="form-group" id="edit-tahun-lulus-container">
                            <label for="edit-tahun-lulus">Tahun Lulus</label>
                            <input type="number" class="form-control" id="edit-tahun-lulus" min="2000"
                                max="2099">
                            <small class="form-text text-muted">Diisi jika status kelulusan "Lulus"</small>
                        </div>

                        <div class="form-group">
                            <label for="edit-tempat-lahir">Tempat Lahir</label>
                            <input type="text" class="form-control" id="edit-tempat-lahir" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-tanggal-lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="edit-tanggal-lahir" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-alamat">Alamat</label>
                            <textarea class="form-control" id="edit-alamat" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="update-siswa">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let allData = [];
            let jurusanData = [];
            let tahunLulusOptions = [];
            const perPage = 10;
            let currentPage = 1;
            let filteredData = [];

            // Setup modal close handlers
            $('.modal .close, .modal .btn-secondary').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });

            // Toggle tahun_lulus requirement based on status_lulus selection
            $('#status_lulus').on('change', function() {
                const status = $(this).val();
                if (status === 'lulus') {
                    $('#tahun_lulus').prop('required', true);
                    $('#tahun-lulus-container').show();
                } else {
                    $('#tahun_lulus').prop('required', false);
                    $('#tahun-lulus-container').hide();
                }
            });

            // Hide tahun_lulus field on initial load
            $('#tahun-lulus-container').hide();

            // Clear form fields and reset validation when modal is opened
            $('#tambah-siswa').click(function() {
                $('#tambahSiswaModal').modal('show');
                $('#form-tambah-akun-siswa')[0].reset();
                $('#tahun-lulus-container').hide();
            });

            // Load jurusan data
            function loadJurusan() {
                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/jurusan',
                    method: 'GET',
                    success: function(data) {
                        jurusanData = data;
                        populateJurusanDropdowns();
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data jurusan: ' + xhr.responseText);
                    }
                });
            }

            // Populate jurusan dropdowns
            function populateJurusanDropdowns() {
                let options = '<option value="">Pilih Jurusan</option>';
                let filterOptions = '<option value="">Semua Jurusan</option>';

                jurusanData.forEach(function(jurusan) {
                    options += `<option value="${jurusan.id}">${jurusan.nama}</option>`;
                    filterOptions +=
                        `<option value="${jurusan.id}">${jurusan.kode} - ${jurusan.nama}</option>`;
                });

                $('#jurusan_id').html(options);
                $('#edit-jurusan').html(options);
                $('#filter-jurusan').html(filterOptions);
            }

            // Extract year from tanggal_lulus and populate tahun lulus filter
            function populateTahunLulusFilter() {
                // Extract unique tahun lulus from allData
                const uniqueYears = [...new Set(allData.map(siswa => {
                    // Extract year from tanggal_lulus
                    if (siswa.tanggal_lulus) {
                        return new Date(siswa.tanggal_lulus).getFullYear();
                    }
                    return null;
                }))].filter(year => year);

                uniqueYears.sort((a, b) => b - a); // Sort descending (newest first)

                let options = '<option value="">Semua Tahun Lulus</option>';
                uniqueYears.forEach(year => {
                    options += `<option value="${year}">${year}</option>`;
                });

                $('#filter-tahun-lulus').html(options);
                tahunLulusOptions = uniqueYears;
            }

            // Helper function to get jurusan code by id
            function getJurusanCodeById(jurusanId) {
                const jurusan = jurusanData.find(j => j.id == jurusanId);
                return jurusan ? jurusan.kode : '-';
            }

            // Load data with loading animation
            function loadData() {
                // Show loading spinner in table
                $('#siswa-table tbody').html(
                    '<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>'
                );

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/siswa',
                    method: 'GET',
                    success: function(data) {
                        // Process data to extract year from tanggal_lulus
                        data.forEach(siswa => {
                            if (siswa.tanggal_lulus) {
                                siswa.tahun_lulus = new Date(siswa.tanggal_lulus).getFullYear();
                            } else {
                                siswa.tahun_lulus = null;
                            }

                            // Add jurusan code based on jurusan_id
                            if (siswa.jurusan_id) {
                                const jurusan = jurusanData.find(j => j.id == siswa.jurusan_id);
                                siswa.jurusan_kode = jurusan ? jurusan.kode : '-';
                            } else {
                                siswa.jurusan_kode = '-';
                            }
                        });

                        allData = data;
                        filteredData = [...allData];
                        updateTable();
                        updatePagination();
                        updateDataCount();
                        populateTahunLulusFilter();
                    },
                    error: function(xhr) {
                        // Show error in table body
                        $('#siswa-table tbody').html(
                            '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>'
                        );
                        toastr.error('Gagal memuat data siswa: ' + xhr.responseText);
                    }
                });
            }

            // Update table with current page data
            function updateTable() {
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const pageData = filteredData.slice(start, end);

                let tbody = '';

                if (pageData.length === 0) {
                    tbody = '<tr><td colspan="8" class="text-center">Tidak ada data yang ditemukan</td></tr>';
                } else {
                    pageData.forEach(function(siswa, index) {
                        // Extract year from tanggal_lulus for display
                        const tahunLulus = siswa.tanggal_lulus ? new Date(siswa.tanggal_lulus)
                            .getFullYear() : '-';

                        // Get jurusan code
                        const jurusanKode = siswa.jurusan_id ? getJurusanCodeById(siswa.jurusan_id) : '-';

                        tbody += `
                <tr data-id="${siswa.id}">
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionMenu${siswa.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actionMenu${siswa.id}">
                                <a class="dropdown-item btn-edit" href="javascript:void(0);" data-id="${siswa.id}">
                                    <i class="mdi mdi-pencil text-info mr-2"></i>Edit
                                </a>
                                <a class="dropdown-item btn-hapus" href="javascript:void(0);" data-id="${siswa.id}">
                                    <i class="mdi mdi-delete text-danger mr-2"></i>Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="d-inline-block text-truncate" style="max-width: 150px;" title="${siswa.nama_lengkap || '-'}">${siswa.nama_lengkap || '-'}</span>
                    </td>
                    <td>${siswa.nisn || '-'}</td>
                    <td class="d-none d-md-table-cell">${jurusanKode}</td>
                    <td class="d-none d-md-table-cell">${siswa.tempat_lahir || '-'}</td>
                    <td class="d-none d-md-table-cell">${siswa.tanggal_lahir || '-'}</td>
                    <td class="d-none d-lg-table-cell">
                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="${siswa.alamat || '-'}">${siswa.alamat || '-'}</span>
                    </td>
                    <td>${tahunLulus}</td>
                </tr>
            `;
                    });
                }

                $('#siswa-table tbody').html(tbody);

                // Also update the card view for mobile
                let cardHtml = '';
                if (pageData.length === 0) {
                    cardHtml = '<div class="text-center p-3">Tidak ada data yang ditemukan</div>';
                } else {
                    pageData.forEach(function(siswa) {
                        const tahunLulus = siswa.tanggal_lulus ? new Date(siswa.tanggal_lulus)
                            .getFullYear() : '-';
                        const jurusanKode = siswa.jurusan_id ? getJurusanCodeById(siswa.jurusan_id) : '-';

                        cardHtml += `
                            <div class="card mb-3" data-id="${siswa.id}">
                                <div class="card-body">
                                    <h5 class="card-title">${siswa.nama_lengkap || '-'}</h5>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <strong class="text-muted">NISN:</strong>
                                            <div>${siswa.nisn || '-'}</div>
                                        </div>
                                        <div class="col-6">
                                            <strong class="text-muted">Tahun Lulus:</strong>
                                            <div>${tahunLulus}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <strong class="text-muted">Kode Jurusan:</strong>
                                            <div>${jurusanKode}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 d-flex justify-content-between">
                                        <button class="btn btn-sm btn-info btn-edit" data-id="${siswa.id}">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${siswa.id}">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }

                $('#card-container').html(cardHtml);
            }

            // Update pagination
            function updatePagination() {
                const totalPages = Math.ceil(filteredData.length / perPage);
                let paginationHtml = '';

                // Previous button
                paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
        </li>`;

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
                }

                // Next button
                paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
        </li>`;

                // On small screens, reduce the number of visible page numbers
                if (window.innerWidth < 768) {
                    // Show only current, previous, next, first and last
                    paginationHtml = '';
                    paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="1"><i class="mdi mdi-chevron-double-left"></i></a>
        </li>`;
                    paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="mdi mdi-chevron-left"></i></a>
        </li>`;
                    paginationHtml += `<li class="page-item active">
            <a class="page-link" href="#">${currentPage} / ${totalPages}</a>
        </li>`;
                    paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="mdi mdi-chevron-right"></i></a>
        </li>`;
                    paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${totalPages}"><i class="mdi mdi-chevron-double-right"></i></a>
        </li>`;
                }

                $('#pagination').html(paginationHtml);
            }

            // Update data count info
            function updateDataCount() {
                const start = filteredData.length > 0 ? (currentPage - 1) * perPage + 1 : 0;
                const end = Math.min(currentPage * perPage, filteredData.length);
                const total = filteredData.length;

                $('#data-count').text(`Menampilkan ${start} sampai ${end} dari ${total} data`);
            }

            // Search functionality with filters
            function performSearch() {
                // Show loading spinner while filtering
                $('#siswa-table tbody').html(
                    '<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>'
                );

                const searchTerm = $('#search-input').val().toLowerCase();
                const selectedJurusan = $('#filter-jurusan').val();
                const selectedTahunLulus = $('#filter-tahun-lulus').val();

                filteredData = allData.filter(siswa => {
                    // Search term filter
                    const matchesSearch = searchTerm === '' ||
                        (siswa.nama_lengkap && siswa.nama_lengkap.toLowerCase().includes(searchTerm)) ||
                        (siswa.nisn && siswa.nisn.toString().includes(searchTerm)) ||
                        (siswa.tempat_lahir && siswa.tempat_lahir.toLowerCase().includes(searchTerm)) ||
                        (siswa.jurusan_kode && siswa.jurusan_kode.toLowerCase().includes(searchTerm)) ||
                        (siswa.alamat && siswa.alamat.toLowerCase().includes(searchTerm));

                    // Jurusan filter
                    const matchesJurusan = selectedJurusan === '' ||
                        (siswa.jurusan_id && siswa.jurusan_id.toString() === selectedJurusan);

                    // Tahun lulus filter - comparing with extracted year
                    const tahunLulus = siswa.tanggal_lulus ? new Date(siswa.tanggal_lulus).getFullYear()
                        .toString() : '';
                    const matchesTahunLulus = selectedTahunLulus === '' || tahunLulus ===
                        selectedTahunLulus;

                    return matchesSearch && matchesJurusan && matchesTahunLulus;
                });

                currentPage = 1;
                updateTable();
                updatePagination();
                updateDataCount();
            }

            // Show tambah siswa modal
            $('#tambah-siswa').click(function() {
                $('#tambahSiswaModal').modal('show');
                $('#form-tambah-akun-siswa')[0].reset();
            });

            // Show import siswa modal
            $('#import-siswa').click(function() {
                $('#importSiswaModal').modal('show');
                $('#form-import-siswa')[0].reset();
            });

            // Download template
            $('#download-template').click(function(e) {
                e.preventDefault();
                window.location.href = _baseURL + 'api/profile-operator/download-template/siswa';
            });

            // Process import
            $('#proses-import').click(function() {
                const fileInput = $('#file-import')[0];

                if (fileInput.files.length === 0) {
                    toastr.warning('Harap pilih file terlebih dahulu!');
                    return;
                }

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);

                // Show loading indicator
                Swal.fire({
                    title: 'Sedang Memproses',
                    html: 'Mohon tunggu, sedang mengimport data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _baseURL + 'api/profile-operator/import/siswa',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        $('#importSiswaModal').modal('hide');
                        $('#form-import-siswa')[0].reset();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data siswa berhasil diimport',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload data after import
                            loadData();
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Gagal mengimport data siswa.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            // If there are validation errors
                            if (xhr.responseJSON.errors) {
                                let errorDetails = '<ul class="text-left">';
                                for (const [key, value] of Object.entries(xhr.responseJSON
                                        .errors)) {
                                    errorDetails += `<li>${value[0]}</li>`;
                                }
                                errorDetails += '</ul>';

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Import',
                                    html: `${errorMessage}<br>${errorDetails}`,
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }
                        }

                        toastr.error(errorMessage);
                    }
                });
            });

            // Save new siswa
            $('#simpan-akun-siswa').click(function() {
                // Collect all form data
                const statusLulus = $('#status_lulus').val();
                const tahunLulus = $('#tahun_lulus').val();

                // Create base form data
                const formData = {
                    email: $('#email').val(),
                    nama_lengkap: $('#nama_lengkap').val(),
                    password: $('#password').val(),
                    nisn: $('#nisn').val(),
                    jurusan_id: $('#jurusan_id').val(),
                    status_lulus: statusLulus
                };

                // Only add tahun_lulus and set tanggal_lulus if status is "lulus"
                if (statusLulus === 'lulus') {
                    if (!tahunLulus) {
                        toastr.warning('Harap isi tahun lulus karena status kelulusan "Lulus"!');
                        return;
                    }
                    formData.tahun_lulus = tahunLulus;
                    formData.tanggal_lulus = tahunLulus +
                        '-01-01'; // Convert year to date format YYYY-01-01
                }

                // Log the data being sent (for debugging)
                console.log('Sending data:', formData);

                // Validate form - ensure all required fields are filled
                if (!formData.email || !formData.password || !formData.nisn ||
                    !formData.jurusan_id || !formData.nama_lengkap || !formData.status_lulus) {
                    toastr.warning('Harap isi semua field yang diperlukan!');
                    return;
                }

                // Show loading indicator
                Swal.fire({
                    title: 'Sedang Memproses',
                    html: 'Mohon tunggu, sedang menyimpan data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _baseURL + 'api/profile-operator/create/siswa',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close loading indicator
                        Swal.close();

                        // Close modal and reset form
                        $('#tambahSiswaModal').modal('hide');
                        $('#form-tambah-akun-siswa')[0].reset();

                        // Show success message
                        toastr.success('Akun siswa berhasil ditambahkan!');

                        // Reload data table
                        loadData();
                    },
                    error: function(xhr, status, error) {
                        // Close loading indicator
                        Swal.close();

                        // Handle error response
                        let errorMessage = 'Gagal menambahkan akun siswa.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Log detailed error for debugging
                        console.error('Error adding student:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        toastr.error(errorMessage);
                    }
                });
            });

            // IMPORTANT CHANGE: Use event delegation for .btn-edit and .btn-hapus
            // This ensures the events are captured even for dynamically added elements
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                // Debug log to check if ID is captured correctly
                console.log('Edit button clicked, ID:', id);

                if (!id) {
                    console.error('No student ID found for edit button');
                    toastr.error('ID siswa tidak ditemukan');
                    return;
                }

                // Show loading indicator on the button
                const $editButton = $(this);
                const originalContent = $editButton.html();
                $editButton.html('<i class="spinner-border spinner-border-sm mr-1"></i> Loading...');
                $editButton.prop('disabled', true);

                // Also show loading indicator on the whole page for better user experience
                Swal.fire({
                    title: 'Memuat Data',
                    html: 'Mohon tunggu, sedang memuat data siswa...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/siswa/' + id,
                    method: 'GET',
                    success: function(data) {
                        console.log('Retrieved student data:', data);

                        // Close the loading indicator
                        Swal.close();

                        // Restore button state
                        $editButton.html(originalContent);
                        $editButton.prop('disabled', false);

                        $('#edit-id').val(id);
                        $('#edit-nama-lengkap').val(data.nama_lengkap);
                        $('#edit-nisn').val(data.nisn);
                        $('#edit-jurusan').val(data.jurusan_id);

                        // Set status lulus
                        $('#edit-status-lulus').val(data.status_lulus || 'belum');

                        // Handle tahun lulus visibility based on status
                        if (data.status_lulus === 'lulus') {
                            $('#edit-tahun-lulus-container').show();
                            $('#edit-tahun-lulus').val(data.tahun_lulus);
                        } else {
                            $('#edit-tahun-lulus-container').hide();
                            $('#edit-tahun-lulus').val('');
                        }

                        $('#edit-tempat-lahir').val(data.tempat_lahir);
                        $('#edit-tanggal-lahir').val(data.tanggal_lahir);
                        $('#edit-alamat').val(data.alamat);

                        $('#editSiswaModal').modal('show');
                    },
                    error: function(xhr) {
                        // Close the loading indicator
                        Swal.close();

                        // Restore button state
                        $editButton.html(originalContent);
                        $editButton.prop('disabled', false);

                        console.error('Error fetching student data:', xhr);
                        toastr.error('Gagal memuat data siswa: ' + xhr.responseText);
                    }
                });
            });

            // Toggle tahun_lulus requirement in edit modal based on status_lulus selection
            $('#edit-status-lulus').on('change', function() {
                const status = $(this).val();
                if (status === 'lulus') {
                    $('#edit-tahun-lulus').prop('required', true);
                    $('#edit-tahun-lulus-container').show();
                } else {
                    $('#edit-tahun-lulus').prop('required', false);
                    $('#edit-tahun-lulus-container').hide();
                }
            });

            // Hide edit-tahun-lulus field on initial load
            $('#edit-tahun-lulus-container').hide();

            // Update siswa data
            $('#update-siswa').click(function() {
                const id = $('#edit-id').val();
                const statusLulus = $('#edit-status-lulus').val();
                const tahunLulus = $('#edit-tahun-lulus').val();

                console.log('Updating student with ID:', id);

                if (!id) {
                    console.error('No student ID found for update operation');
                    toastr.error('ID siswa tidak ditemukan');
                    return;
                }

                const formData = {
                    nama_lengkap: $('#edit-nama-lengkap').val(),
                    nisn: $('#edit-nisn').val(),
                    jurusan_id: $('#edit-jurusan').val(),
                    status_lulus: statusLulus,
                    tempat_lahir: $('#edit-tempat-lahir').val(),
                    tanggal_lahir: $('#edit-tanggal-lahir').val(),
                    alamat: $('#edit-alamat').val(),
                };

                // Only add tahun_lulus if status is "lulus"
                if (statusLulus === 'lulus') {
                    if (!tahunLulus) {
                        toastr.warning('Harap isi tahun lulus karena status kelulusan "Lulus"!');
                        return;
                    }
                    formData.tahun_lulus = tahunLulus;
                    formData.tanggal_lulus = tahunLulus +
                        '-01-01'; // Convert year to date format YYYY-01-01
                }

                // Validate form
                if (!formData.nama_lengkap || !formData.nisn || !formData.jurusan_id ||
                    !formData.tempat_lahir || !formData.tanggal_lahir || !formData.alamat || !formData
                    .status_lulus
                ) {
                    toastr.warning('Harap isi semua field yang diperlukan!');
                    return;
                }

                // Show loading indicator
                const $updateButton = $(this);
                const originalUpdateText = $updateButton.text();
                $updateButton.html('<i class="spinner-border spinner-border-sm mr-1"></i> Menyimpan...');
                $updateButton.prop('disabled', true);

                // Also show loading indicator on the whole page
                Swal.fire({
                    title: 'Sedang Memproses',
                    html: 'Mohon tunggu, sedang menyimpan perubahan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Fix the method to POST since that's what the route supports
                $.ajax({
                    url: _baseURL + 'api/profile-operator/update/siswa/' + id,
                    method: 'POST', // Changed from 'POST' with _method: 'PUT' to just 'POST'
                    data: formData,
                    success: function(response) {
                        console.log('Student updated successfully:', response);

                        // Close loading indicators
                        Swal.close();
                        $updateButton.html(originalUpdateText);
                        $updateButton.prop('disabled', false);

                        $('#editSiswaModal').modal('hide');

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data siswa berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        loadData();
                    },
                    error: function(xhr) {
                        console.error('Error updating student:', xhr);
                        console.log('Error response:', xhr
                            .responseText); // Add detailed error logging

                        // Close loading indicators
                        Swal.close();
                        $updateButton.html(originalUpdateText);
                        $updateButton.prop('disabled', false);

                        let errorMessage = 'Gagal memperbarui data siswa.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // Delete siswa with confirmation dialog and loading animation
            $(document).on('click', '.btn-hapus', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                // Debug log to check if ID is captured correctly
                console.log('Delete button clicked, ID:', id);

                if (!id) {
                    console.error('No student ID found for delete button');
                    toastr.error('ID siswa tidak ditemukan');
                    return;
                }

                // Get reference to the delete button
                const $deleteButton = $(this);
                const originalDeleteText = $deleteButton.html();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data siswa akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading animation on the button
                        $deleteButton.html(
                            '<i class="spinner-border spinner-border-sm mr-1"></i> Menghapus...'
                        );
                        $deleteButton.prop('disabled', true);

                        // Also show loading indicator on the whole page
                        Swal.fire({
                            title: 'Sedang Memproses',
                            html: 'Mohon tunggu, sedang menghapus data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: _baseURL + `api/profile-operator/delete/siswa/${id}`,
                            method: 'DELETE',
                            success: function(response) {
                                console.log('Student deleted successfully:', response);

                                // Close loading indicator
                                Swal.close();

                                // Restore button (though it will be removed from DOM when the table reloads)
                                $deleteButton.html(originalDeleteText);
                                $deleteButton.prop('disabled', false);

                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data siswa berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                loadData();
                            },
                            error: function(xhr) {
                                console.error('Error deleting student:', xhr);

                                // Close loading indicator
                                Swal.close();

                                // Restore button
                                $deleteButton.html(originalDeleteText);
                                $deleteButton.prop('disabled', false);

                                let errorMessage = 'Gagal menghapus data siswa.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                toastr.error(errorMessage);
                            }
                        });
                    }
                });
            });

            // Event listeners
            $('#search-input').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            $('#search-button').on('click', performSearch);
            $('#filter-jurusan, #filter-tahun-lulus').on('change', performSearch);

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page >= 1 && page <= Math.ceil(filteredData.length / perPage)) {
                    currentPage = page;
                    updateTable();
                    updatePagination();
                    updateDataCount();
                }
            });

            // Initial load
            loadJurusan();
            loadData();

            // Add this to your $(document).ready function
            $('#card-view-btn').click(function() {
                $(this).addClass('active');
                $('#table-view-btn').removeClass('active');
                $('#table-view').addClass('d-none');
                $('#card-view').removeClass('d-none');
            });

            $('#table-view-btn').click(function() {
                $(this).addClass('active');
                $('#card-view-btn').removeClass('active');
                $('#table-view').removeClass('d-none');
                $('#card-view').addClass('d-none');
            });

            // Make the filter section collapsible on mobile
            $('.card-body').prepend(`
                <div class="d-md-none mb-3">
                    <button class="btn btn-light btn-block" type="button" data-toggle="collapse" data-target="#filterCollapse">
                        <i class="mdi mdi-filter"></i> Filter & Pencarian <i class="mdi mdi-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse d-md-block" id="filterCollapse">
                    <!-- Move your existing filter row here -->
                </div>
            `);
        });
    </script>
@endpush
