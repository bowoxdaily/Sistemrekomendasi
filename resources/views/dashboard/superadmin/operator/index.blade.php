@extends('layout.app')

@section('title', 'Dashboard | SuperAdmin')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Operator Sekolah</h4>
                                <div>
                                    <button class="btn btn-primary btn-icon-text" id="tambah-operator">
                                        <i class="mdi mdi-plus btn-icon-prepend"></i>
                                        Tambah Operator
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" id="search-operator-input" class="form-control"
                                            placeholder="Cari operator..." aria-label="Cari operator">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="search-operator-button">
                                                <i class="mdi mdi-magnify"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="operator-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>...</th>
                                            <th>Nama Lengkap</th>
                                            <th>Email</th>
                                            <th>NIP</th>
                                            <th>Jabatan</th>
                                            <th>Jenis Kelamin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <div class="dataTables_info" id="operator-data-count">Menampilkan 0 dari 0 data</div>
                                <ul class="pagination pagination-primary" id="operator-pagination">
                                    <!-- Pagination akan diisi oleh JavaScript -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Operator -->
        <div class="modal fade" id="tambahOperatorModal" tabindex="-1" role="dialog"
            aria-labelledby="tambahOperatorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahOperatorModalLabel">Tambah Akun Operator</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-tambah-operator">
                            @csrf
                            <div class="form-group">
                                <label for="operator_nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control" id="operator_nama_lengkap" required>
                            </div>
                            <div class="form-group">
                                <label for="operator_email">Email</label>
                                <input type="email" class="form-control" id="operator_email" required>
                            </div>
                            <div class="form-group">
                                <label for="operator_nip">NIP</label>
                                <input type="text" class="form-control" id="operator_nip">
                            </div>
                            <div class="form-group">
                                <label for="operator_jabatan">Jabatan</label>
                                <select class="form-control" id="operator_jabatan" required>
                                    <option value="operator">Operator</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="operator_jenis_kelamin">Jenis Kelamin</label>
                                <select class="form-control" id="operator_jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="operator_no_hp">No. HP</label>
                                <input type="number" class="form-control" id="operator_no_hp">
                            </div>
                            <div class="form-group">
                                <label for="operator_password">Password</label>
                                <input type="password" class="form-control" id="operator_password" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="simpan-operator">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Operator -->
        <div class="modal fade" id="editOperatorModal" tabindex="-1" role="dialog"
            aria-labelledby="editOperatorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOperatorModalLabel">Edit Data Operator</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-edit-operator">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="edit-operator-id">
                            <div class="form-group">
                                <label for="edit-operator-nama-lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control" id="edit-operator-nama-lengkap" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-operator-email">Email</label>
                                <input type="email" class="form-control" id="edit-operator-email" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-operator-nip">NIP</label>
                                <input type="text" class="form-control" id="edit-operator-nip">
                            </div>
                            <div class="form-group">
                                <label for="edit-operator-jabatan">Jabatan</label>
                                <input type="text" class="form-control" id="edit-operator-jabatan">
                            </div>
                            <div class="form-group">
                                <label for="edit-operator-jenis-kelamin">Jenis Kelamin</label>
                                <select class="form-control" id="edit-operator-jenis-kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-operator-no-hp">No. HP</label>
                                <input type="text" class="form-control" id="edit-operator-no-hp">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="update-operator">Simpan</button>
                    </div>
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

            // Show tambah operator modal
            $('#tambah-operator').click(function() {
                $('#tambahOperatorModal').modal('show');
                $('#form-tambah-operator')[0].reset();
            });

            // Load data with loading animation
            function loadData() {
                // Show loading spinner in table
                $('#operator-table tbody').html(
                    '<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>'
                );

                $.ajax({
                    url: _baseURL + 'api/superadmin/operator',
                    method: 'GET',
                    success: function(data) {
                        allData = data;
                        filteredData = [...allData];
                        updateTable();
                        updatePagination();
                        updateDataCount();
                    },
                    error: function(xhr) {
                        // Show error in table body
                        $('#operator-table tbody').html(
                            '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>'
                        );
                        toastr.error('Gagal memuat data operator: ' + xhr.responseText);
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
                    tbody = '<tr><td colspan="6" class="text-center">Tidak ada data yang ditemukan</td></tr>';
                } else {
                    pageData.forEach(function(operator) {
                        tbody += `
                <tr data-id="${operator.id}">
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionMenu${operator.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actionMenu${operator.id}">
                                <a class="dropdown-item btn-edit" href="javascript:void(0);" data-id="${operator.id}">
                                    <i class="mdi mdi-pencil text-info mr-2"></i>Edit
                                </a>
                                <a class="dropdown-item btn-hapus" href="javascript:void(0);" data-id="${operator.id}">
                                    <i class="mdi mdi-delete text-danger mr-2"></i>Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>${operator.nama_lengkap || '-'}</td>
                    <td>${operator.user ? operator.user.email : '-'}</td>
                    <td>${operator.nip || '-'}</td>
                    <td>${operator.jabatan || '-'}</td>
                    <td>${operator.jenis_kelamin || '-'}</td>
                </tr>
                `;
                    });
                }

                $('#operator-table tbody').html(tbody);
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
                paginationHtml += `<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
        </li>`;

                $('#operator-pagination').html(paginationHtml);
            }

            // Update data count info
            function updateDataCount() {
                const start = filteredData.length > 0 ? (currentPage - 1) * perPage + 1 : 0;
                const end = Math.min(currentPage * perPage, filteredData.length);
                const total = filteredData.length;

                $('#operator-data-count').text(`Menampilkan ${start} sampai ${end} dari ${total} data`);
            }

            // Search functionality
            function performSearch() {
                // Show loading spinner while filtering
                $('#operator-table tbody').html(
                    '<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>'
                );

                const searchTerm = $('#search-operator-input').val().toLowerCase();

                filteredData = allData.filter(operator => {
                    // Search term filter
                    return searchTerm === '' ||
                        (operator.nama_lengkap && operator.nama_lengkap.toLowerCase().includes(
                            searchTerm)) ||
                        (operator.nip && operator.nip.toString().includes(searchTerm)) ||
                        (operator.jabatan && operator.jabatan.toLowerCase().includes(searchTerm)) ||
                        (operator.user && operator.user.email && operator.user.email.toLowerCase().includes(
                            searchTerm));
                });

                currentPage = 1;
                updateTable();
                updatePagination();
                updateDataCount();
            }

            // Save new operator
            $('#simpan-operator').click(function() {
                const formData = {
                    email: $('#operator_email').val(),
                    nama_lengkap: $('#operator_nama_lengkap').val(),
                    password: $('#operator_password').val(),
                    no_hp: $('#operator_no_hp').val(),
                    nip: $('#operator_nip').val(),
                    jabatan: $('#operator_jabatan').val(),
                    jenis_kelamin: $('#operator_jenis_kelamin').val()
                };

                // Validate form
                if (!formData.email || !formData.password || !formData.nama_lengkap) {
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
                    url: _baseURL + 'api/superadmin/operator',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close loading indicator
                        Swal.close();

                        // Close modal and reset form
                        $('#tambahOperatorModal').modal('hide');
                        $('#form-tambah-operator')[0].reset();

                        // Show success message
                        toastr.success('Akun operator berhasil ditambahkan!');

                        // Reload data table
                        loadData();
                    },
                    error: function(xhr) {
                        // Close loading indicator
                        Swal.close();

                        // Handle error response
                        let errorMessage = 'Gagal menambahkan akun operator.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                    }
                });
            });

            // Load operator data for editing
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                if (!id) {
                    toastr.error('ID operator tidak ditemukan');
                    return;
                }

                // Show loading indicator
                Swal.fire({
                    title: 'Memuat Data',
                    html: 'Mohon tunggu, sedang memuat data operator...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _baseURL + 'api/superadmin/operator/' + id,
                    method: 'GET',
                    success: function(data) {
                        // Close loading indicator
                        Swal.close();

                        $('#edit-operator-id').val(id);
                        $('#edit-operator-nama-lengkap').val(data.nama_lengkap);
                        $('#edit-operator-email').val(data.user ? data.user.email : '');
                        $('#edit-operator-no-hp').val(data.no_hp || '');
                        $('#edit-operator-nip').val(data.nip || '');
                        $('#edit-operator-jabatan').val(data.jabatan || '');
                        $('#edit-operator-jenis-kelamin').val(data.jenis_kelamin || '');

                        $('#editOperatorModal').modal('show');
                    },
                    error: function(xhr) {
                        // Close loading indicator
                        Swal.close();
                        toastr.error('Gagal memuat data operator: ' + xhr.responseText);
                    }
                });
            });

            // Update operator data
            $('#update-operator').click(function() {
                const id = $('#edit-operator-id').val();

                if (!id) {
                    toastr.error('ID operator tidak ditemukan');
                    return;
                }

                const formData = {
                    nama_lengkap: $('#edit-operator-nama-lengkap').val(),
                    email: $('#edit-operator-email').val(),
                    no_hp: $('#edit-operator-no-hp').val(),
                    nip: $('#edit-operator-nip').val(),
                    jabatan: $('#edit-operator-jabatan').val(),
                    jenis_kelamin: $('#edit-operator-jenis-kelamin').val(),
                    _method: 'PUT'
                };

                // Validate form
                if (!formData.nama_lengkap || !formData.email) {
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
                    url: _baseURL + 'api/superadmin/operator/' + id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close loading indicator
                        Swal.close();

                        // Close modal
                        $('#editOperatorModal').modal('hide');

                        // Show success message
                        toastr.success('Data operator berhasil diperbarui!');

                        // Reload data
                        loadData();
                    },
                    error: function(xhr) {
                        // Close loading indicator
                        Swal.close();

                        let errorMessage = 'Gagal memperbarui data operator.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // Delete operator with confirmation
            $(document).on('click', '.btn-hapus', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                if (!id) {
                    toastr.error('ID operator tidak ditemukan');
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data operator akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading indicator
                        Swal.fire({
                            title: 'Sedang Memproses',
                            html: 'Mohon tunggu, sedang menghapus data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: _baseURL + `api/superadmin/operator/${id}`,
                            method: 'DELETE',
                            success: function(response) {
                                // Close loading indicator
                                Swal.close();

                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data operator berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                loadData();
                            },
                            error: function(xhr) {
                                // Close loading indicator
                                Swal.close();

                                let errorMessage = 'Gagal menghapus data operator.';
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
            $('#search-operator-input').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            $('#search-operator-button').on('click', performSearch);

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
