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
                                <div class="col-md-4">
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
                                <div class="col-md-3">
                                    <select class="form-control" id="filter-jurusan">
                                        <option value="">Semua Jurusan</option>
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filter-tahun-lulus">
                                        <option value="">Semua Tahun Lulus</option>
                                        <!-- Options will be loaded dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="siswa-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>...</th>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Jurusan</th>
                                            <th>Tempat Lahir</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Alamat</th>
                                            <th>Tahun Lulus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
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
                            <label for="email">Nama Lengkap</label>
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
                            <label for="tahun_lulus">Tahun Lulus</label>
                            <input type="number" class="form-control" id="tahun_lulus" min="2000" max="2099"
                                required>
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
                            <label for="edit-tahun-lulus">Tahun Lulus</label>
                            <input type="number" class="form-control" id="edit-tahun-lulus" min="2000"
                                max="2099" required>
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
                    filterOptions += `<option value="${jurusan.id}">${jurusan.nama}</option>`;
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

            // Helper function to get jurusan name by id
            function getJurusanNameById(jurusanId) {
                const jurusan = jurusanData.find(j => j.id == jurusanId);
                return jurusan ? jurusan.nama : '-';
            }

            // Load data
            function loadData() {
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

                            // Add jurusan name based on jurusan_id
                            if (siswa.jurusan_id) {
                                const jurusan = jurusanData.find(j => j.id == siswa.jurusan_id);
                                siswa.jurusan_nama = jurusan ? jurusan.nama : '-';
                            } else {
                                siswa.jurusan_nama = '-';
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
                pageData.forEach(function(siswa, index) {
                    // Extract year from tanggal_lulus for display
                    const tahunLulus = siswa.tanggal_lulus ? new Date(siswa.tanggal_lulus).getFullYear() :
                        '-';

                    // Get jurusan name
                    const jurusanNama = siswa.jurusan_id ? getJurusanNameById(siswa.jurusan_id) : '-';

                    tbody += `
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionMenu${siswa.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actionMenu${siswa.id}">
                                <a class="dropdown-item btn-edit" href="#" data-id="${siswa.id}">
                                    <i class="mdi mdi-pencil text-info mr-2"></i>Edit
                                </a>
                                <a class="dropdown-item btn-hapus" href="#" data-id="${siswa.id}">
                                    <i class="mdi mdi-delete text-danger mr-2"></i>Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>${siswa.nama_lengkap || '-'}</td>
                    <td>${siswa.nisn || '-'}</td>
                    <td>${jurusanNama}</td>
                    <td>${siswa.tempat_lahir || '-'}</td>
                    <td>${siswa.tanggal_lahir || '-'}</td>
                    <td>${siswa.alamat || '-'}</td>
                    <td>${tahunLulus}</td>
                </tr>
            `;
                });

                $('#siswa-table tbody').html(tbody);
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

                $('#pagination').html(paginationHtml);
            }

            // Update data count info
            function updateDataCount() {
                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(currentPage * perPage, filteredData.length);
                const total = filteredData.length;

                $('#data-count').text(`Menampilkan ${start} sampai ${end} dari ${total} data`);
            }

            // Search functionality with filters
            function performSearch() {
                const searchTerm = $('#search-input').val().toLowerCase();
                const selectedJurusan = $('#filter-jurusan').val();
                const selectedTahunLulus = $('#filter-tahun-lulus').val();

                filteredData = allData.filter(siswa => {
                    // Search term filter
                    const matchesSearch = searchTerm === '' ||
                        (siswa.nama_lengkap && siswa.nama_lengkap.toLowerCase().includes(searchTerm)) ||
                        (siswa.nisn && siswa.nisn.toString().includes(searchTerm)) ||
                        (siswa.tempat_lahir && siswa.tempat_lahir.toLowerCase().includes(searchTerm)) ||
                        (siswa.jurusan_nama && siswa.jurusan_nama.toLowerCase().includes(searchTerm)) ||
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

            // Save new siswa - REVISED FUNCTION
            $('#simpan-akun-siswa').click(function() {
                // Collect all form data
                const formData = {
                    email: $('#email').val(),
                    nama_lengkap: $('#nama_lengkap').val(),
                    password: $('#password').val(),
                    nisn: $('#nisn').val(),
                    jurusan_id: $('#jurusan_id').val(),
                    tanggal_lulus: $('#tanggal_lulus').val()
                };

                // Log the data being sent (for debugging)
                console.log('Sending data:', formData);

                // Validate form - ensure all required fields are filled
                if (!formData.email || !formData.password || !formData.nisn ||
                    !formData.jurusan_id || !formData.tanggal_lulus || !formData.nama_lengkap) {
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

            // Edit siswa
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/siswa/' + id,
                    method: 'GET',
                    success: function(data) {
                        $('#edit-id').val(id);
                        $('#edit-nama-lengkap').val(data.nama_lengkap);
                        $('#edit-nisn').val(data.nisn);
                        $('#edit-jurusan').val(data.jurusan_id);
                        $('#edit-tanggal-lulus').val(data.tanggal_lulus);
                        $('#edit-tempat-lahir').val(data.tempat_lahir);
                        $('#edit-tanggal-lahir').val(data.tanggal_lahir);
                        $('#edit-alamat').val(data.alamat);

                        $('#editSiswaModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data siswa: ' + xhr.responseText);
                    }
                });
            });

            // Update siswa data
            $('#update-siswa').click(function() {
                const id = $('#edit-id').val();
                const formData = {
                    nama_lengkap: $('#edit-nama-lengkap').val(),
                    nisn: $('#edit-nisn').val(),
                    jurusan_id: $('#edit-jurusan').val(),
                    tanggal_lulus: $('#edit-tanggal-lulus').val(),
                    tempat_lahir: $('#edit-tempat-lahir').val(),
                    tanggal_lahir: $('#edit-tanggal-lahir').val(),
                    alamat: $('#edit-alamat').val(),
                    _method: 'PUT'
                };

                // Validate form
                if (!formData.nama_lengkap || !formData.nisn || !formData.jurusan_id || !formData
                    .tanggal_lulus || !formData.tempat_lahir || !formData.tanggal_lahir || !formData.alamat
                ) {
                    toastr.warning('Harap isi semua field yang diperlukan!');
                    return;
                }

                // Show loading indicator
                Swal.fire({
                    title: 'Sedang Memproses',
                    html: 'Mohon tunggu, sedang menyimpan perubahan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _baseURL + 'api/profile-operator/siswa/' + id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        $('#editSiswaModal').modal('hide');
                        loadData();
                        toastr.success('Data siswa berhasil diperbarui!');
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Gagal memperbarui data siswa.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // Delete siswa with confirmation dialog
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');

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
                        $.ajax({
                            url: _baseURL + '/api/profile-operator/siswa/' + id,
                            method: 'DELETE',
                            success: function(response) {
                                loadData();
                                toastr.success('Data siswa berhasil dihapus!');
                            },
                            error: function(xhr) {
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
        });
    </script>
@endpush
