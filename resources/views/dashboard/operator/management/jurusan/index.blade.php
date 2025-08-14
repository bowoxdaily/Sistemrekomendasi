@extends('layout.app')

@section('title', 'Dashboard | Jurusan')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Jurusan</h4>
                                <div>
                                    <button class="btn btn-primary btn-icon-text" id="tambah-jurusan">
                                        <i class="mdi mdi-plus btn-icon-prepend"></i>
                                        Tambah Jurusan
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari jurusan..." aria-label="Cari jurusan">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="search-button">
                                                <i class="mdi mdi-magnify"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="jurusan-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>...</th>
                                            <th>Kode</th>
                                            <th>Nama Jurusan</th>
                                            <th>Deskripsi</th>
                                            <th>Tanggal Dibuat</th>
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

    <!-- Modal Tambah Jurusan -->
    <div class="modal fade" id="tambahJurusanModal" tabindex="-1" role="dialog" aria-labelledby="tambahJurusanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahJurusanModalLabel">Tambah Jurusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-tambah-jurusan">
                        @csrf
                        <div class="form-group">
                            <label for="kode">Kode Jurusan</label>
                            <input type="text" class="form-control" placeholder="TKJ|AKL|ATPH" id="kode" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Jurusan</label>
                            <input type="text" class="form-control" placeholder="Teknik Komputer Jaringan" id="nama"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpan-jurusan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Jurusan -->
    <div class="modal fade" id="editJurusanModal" tabindex="-1" role="dialog" aria-labelledby="editJurusanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJurusanModalLabel">Edit Data Jurusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-jurusan">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-id">
                        <div class="form-group">
                            <label for="edit-kode">Kode Jurusan</label>
                            <input type="text" class="form-control" id="edit-kode" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-nama">Nama Jurusan</label>
                            <input type="text" class="form-control" id="edit-nama" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="edit-deskripsi" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="update-jurusan">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Styling untuk description container */
        .description-container {
            position: relative;
        }

        .description-text {
            display: block;
            line-height: 1.4;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .btn-expand {
            font-size: 12px !important;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 2px 0;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .btn-expand:hover {
            text-decoration: underline;
            color: #0056b3 !important;
        }

        .btn-expand i {
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .btn-expand.expanded i {
            transform: rotate(180deg);
        }

        /* Table responsive styling */
        .table td {
            vertical-align: top;
            padding: 12px 8px;
        }

        .table .description-container {
            min-height: auto;
        }

        /* Dropdown menu positioning */
        .dropdown-menu {
            z-index: 1050;
        }

        /* Search input styling */
        .input-group .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endpush

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
                    url: _baseURL + 'api/profile-operator/get/jurusan',
                    method: 'GET',
                    success: function(data) {
                        allData = data;
                        filteredData = [...allData];
                        updateTable();
                        updatePagination();
                        updateDataCount();
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data jurusan: ' + xhr.responseText);
                    }
                });
            }

            // Update table with current page data
            function updateTable() {
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const pageData = filteredData.slice(start, end);

                let tbody = '';
                pageData.forEach(function(jurusan, index) {
                    // Format date if it exists
                    const createdAt = jurusan.created_at ? new Date(jurusan.created_at).toLocaleDateString(
                        'id-ID') : '-';

                    // Truncate description to 50 words (not characters)
                    const fullDescription = jurusan.deskripsi || '-';
                    const maxWords = 50;
                    let truncatedDescription = fullDescription;
                    let needsExpansion = false;

                    if (fullDescription !== '-') {
                        const words = fullDescription.trim().split(/\s+/);
                        if (words.length > maxWords) {
                            truncatedDescription = words.slice(0, maxWords).join(' ') + '...';
                            needsExpansion = true;
                        }
                    }

                    const descriptionHtml = needsExpansion ?
                        `<div class="description-container" data-full="${fullDescription.replace(/"/g, '&quot;')}">
                            <span class="description-text">${truncatedDescription}</span>
                            <br>
                            <a href="javascript:void(0)" class="btn-expand text-primary">
                                <i class="mdi mdi-chevron-down"></i> Lihat Selengkapnya
                            </a>
                        </div>` :
                        `<span class="description-text">${fullDescription}</span>`;

                    tbody += `
                    <tr>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionMenu${jurusan.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actionMenu${jurusan.id}">
                                    <a class="dropdown-item btn-edit" href="#" data-id="${jurusan.id}">
                                        <i class="mdi mdi-pencil text-info mr-2"></i>Edit
                                    </a>
                                    <a class="dropdown-item btn-hapus" href="#" data-id="${jurusan.id}">
                                        <i class="mdi mdi-delete text-danger mr-2"></i>Hapus
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>${jurusan.kode || '-'}</td>
                        <td>${jurusan.nama || '-'}</td>
                        <td style="max-width: 300px;">${descriptionHtml}</td>
                        <td>${createdAt}</td>
                    </tr>
                `;
                });

                $('#jurusan-table tbody').html(tbody);
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
                const start = filteredData.length > 0 ? (currentPage - 1) * perPage + 1 : 0;
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
                    filteredData = allData.filter(jurusan =>
                        (jurusan.kode && jurusan.kode.toLowerCase().includes(searchTerm)) ||
                        (jurusan.nama && jurusan.nama.toLowerCase().includes(searchTerm)) ||
                        (jurusan.deskripsi && jurusan.deskripsi.toLowerCase().includes(searchTerm))
                    );
                }

                currentPage = 1;
                updateTable();
                updatePagination();
                updateDataCount();
            }

            // Show tambah jurusan modal
            $('#tambah-jurusan').click(function() {
                $('#tambahJurusanModal').modal('show');
                $('#form-tambah-jurusan')[0].reset();
            });

            // Save new jurusan
            $('#simpan-jurusan').click(function() {
                const formData = {
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    deskripsi: $('#deskripsi').val()
                };

                // Validate form
                if (!formData.kode || !formData.nama) {
                    toastr.warning('Harap isi Kode dan Nama Jurusan!');
                    return;
                }

                $.ajax({
                    url: _baseURL + 'api/profile-operator/create/jurusan',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#tambahJurusanModal').modal('hide');
                        $('#form-tambah-jurusan')[0].reset();
                        loadData();
                        toastr.success('Jurusan berhasil ditambahkan!');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menambahkan jurusan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // Edit jurusan
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/jurusan/' + id,
                    method: 'GET',
                    success: function(data) {
                        $('#edit-id').val(id);
                        $('#edit-kode').val(data.kode);
                        $('#edit-nama').val(data.nama);
                        $('#edit-deskripsi').val(data.deskripsi);

                        $('#editJurusanModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data jurusan: ' + xhr.responseText);
                    }
                });
            });

            // Update jurusan data
            $('#update-jurusan').click(function() {
                const id = $('#edit-id').val();
                const formData = {
                    kode: $('#edit-kode').val(),
                    nama: $('#edit-nama').val(),
                    deskripsi: $('#edit-deskripsi').val(),
                    _method: 'PUT'
                };

                // Validate form
                if (!formData.kode || !formData.nama) {
                    toastr.warning('Harap isi Kode dan Nama Jurusan!');
                    return;
                }

                $.ajax({
                    url: _baseURL + 'api/profile-operator/jurusan/' + id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#editJurusanModal').modal('hide');
                        loadData();
                        toastr.success('Data jurusan berhasil diperbarui!');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal memperbarui data jurusan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // Delete jurusan with confirmation dialog
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data jurusan akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: _baseURL + 'api/profile-operator/delete/jurusan/' + id,
                            method: 'DELETE',
                            success: function(response) {
                                loadData();
                                toastr.success('Data jurusan berhasil dihapus!');
                            },
                            error: function(xhr) {
                                let errorMessage = 'Gagal menghapus data jurusan.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                toastr.error(errorMessage);
                            }
                        });
                    }
                });
            });

            // Event handler for expand/collapse description
            $(document).on('click', '.btn-expand', function(e) {
                e.preventDefault();
                const container = $(this).closest('.description-container');
                const textElement = container.find('.description-text');
                const fullText = container.data('full');
                const button = $(this);
                const maxWords = 50;

                if (button.hasClass('expanded')) {
                    // Collapse
                    const words = fullText.trim().split(/\s+/);
                    const truncatedText = words.slice(0, maxWords).join(' ') + '...';
                    textElement.text(truncatedText);
                    button.html('<i class="mdi mdi-chevron-down"></i> Lihat Selengkapnya');
                    button.removeClass('expanded');
                } else {
                    // Expand
                    textElement.text(fullText);
                    button.html('<i class="mdi mdi-chevron-up"></i> Lihat Lebih Sedikit');
                    button.addClass('expanded');
                }
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
