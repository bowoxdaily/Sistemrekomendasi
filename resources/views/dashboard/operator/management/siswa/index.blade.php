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
                                <div class="col-md-6">
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
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="siswa-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>...</th>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Tempat Lahir</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Alamat</th>
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
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="nisn">NISN</label>
                            <input type="text" class="form-control" id="nisn" required>
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
            const perPage = 10;
            let currentPage = 1;
            let filteredData = [];

            // Setup modal close handlers
            $('.modal .close, .modal .btn-secondary').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });

            // Load data
            function loadData() {
                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/siswa',
                    method: 'GET',
                    success: function(data) {
                        allData = data;
                        filteredData = [...allData];
                        updateTable();
                        updatePagination();
                        updateDataCount();
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
                            <td>${siswa.tempat_lahir || '-'}</td>
                            <td>${siswa.tanggal_lahir || '-'}</td>
                            <td>${siswa.alamat || '-'}</td>
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

            // Search functionality
            function performSearch() {
                const searchTerm = $('#search-input').val().toLowerCase();

                if (searchTerm === '') {
                    filteredData = [...allData];
                } else {
                    filteredData = allData.filter(siswa =>
                        (siswa.nama_lengkap && siswa.nama_lengkap.toLowerCase().includes(searchTerm)) ||
                        (siswa.nisn && siswa.nisn.toString().includes(searchTerm)) ||
                        (siswa.tempat_lahir && siswa.tempat_lahir.toLowerCase().includes(searchTerm)) ||
                        (siswa.alamat && siswa.alamat.toLowerCase().includes(searchTerm))
                    );
                }

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
                const formData = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    nisn: $('#nisn').val()
                };

                // Validate form
                if (!formData.email || !formData.password || !formData.nisn) {
                    toastr.warning('Harap isi semua field yang diperlukan!');
                    return;
                }

                $.ajax({
                    url: _baseURL + 'api/profile-operator/create/siswa',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#tambahSiswaModal').modal('hide');
                        $('#form-tambah-akun-siswa')[0].reset();
                        loadData();
                        toastr.success('Akun siswa berhasil ditambahkan!');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menambahkan akun siswa.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
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
                    tempat_lahir: $('#edit-tempat-lahir').val(),
                    tanggal_lahir: $('#edit-tanggal-lahir').val(),
                    alamat: $('#edit-alamat').val(),
                    _method: 'PUT'
                };

                // Validate form
                if (!formData.nama_lengkap || !formData.nisn || !formData.tempat_lahir ||
                    !formData.tanggal_lahir || !formData.alamat) {
                    toastr.warning('Harap isi semua field yang diperlukan!');
                    return;
                }

                $.ajax({
                    url: _baseURL + 'api/profile-operator/siswa/' + id,
                    method: 'POST', // Using POST with _method: 'PUT'
                    data: formData,
                    success: function(response) {
                        $('#editSiswaModal').modal('hide');
                        loadData();
                        toastr.success('Data siswa berhasil diperbarui!');
                    },
                    error: function(xhr) {
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
            loadData();
        });
    </script>
@endpush

@push('styles')
    <style>
        .table th {
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .pagination {
            margin-bottom: 0;
        }

        .dataTables_info {
            padding-top: 0.85rem;
        }

        .btn-icon-text .btn-icon-prepend {
            margin-right: 8px;
        }

        .btn-edit,
        .btn-hapus {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Toastr notification position */
        #toast-container {
            min-width: 350px;
            top: 70px;
            right: 20px;
        }
    </style>
@endpush
