@extends('layout.app')

@section('title', 'Dashboard | Blog')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Blog</h4>
                                <a href="{{ route('operator.blog.create') }}" class="btn btn-primary btn-icon-text">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    Tambah Blog
                                </a>
                            </div>

                            <!-- Search and filter section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari blog...">
                                        <button class="btn btn-primary" id="search-button" type="button">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filter-category">
                                        <option value="">Semua Kategori</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" id="entries-select">
                                        <option value="10">10 Entri</option>
                                        <option value="25">25 Entri</option>
                                        <option value="50">50 Entri</option>
                                        <option value="100">100 Entri</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Loading indicator -->
                            <div id="loading-indicator" class="text-center py-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat data...</p>
                            </div>

                            <!-- Table section -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="blogs-table">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Penulis</th>
                                            <th>Tanggal Publikasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="blogs-table-body">
                                        <!-- Data will be loaded here via AJAX -->
                                        <tr id="empty-placeholder">
                                            <td colspan="6" class="text-center py-3">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-database-remove display-4"></i>
                                                    <p class="mt-2">Memuat data blog...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="dataTables_info" id="pagination-info">
                                        Menampilkan 0 sampai 0 dari 0 entri
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <nav>
                                        <ul class="pagination justify-content-end" id="pagination-container">
                                            <!-- Pagination will be generated here -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .badge {
            font-weight: 500;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-item {
            padding: 0.4rem 1rem;
        }

        .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .table-empty {
            text-align: center;
            padding: 2rem 0;
        }

        #loading-indicator {
            position: relative;
            z-index: 1;
        }

        .published-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            margin-right: 3px;
        }
        
        .action-buttons .btn i {
            font-size: 1rem;
        }
        
        /* Dropdown menu styling */
        .dropdown-menu {
            min-width: 8rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
            padding: 0.5rem 0;
        }
        
        .dropdown-toggle::after {
            display: none;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .table td {
            vertical-align: middle !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Pagination state
            let currentPage = 1;
            let entriesPerPage = 10;
            let totalEntries = 0;
            let totalPages = 0;

            // Filter state
            let searchQuery = '';
            let categoryFilter = '';

            // Initialize and load data
            loadCategoryOptions();
            loadBlogsData();

            // Search and filter functionality
            $('#search-button').click(function() {
                searchQuery = $('#search-input').val();
                currentPage = 1;
                loadBlogsData();
            });

            $('#search-input').keypress(function(e) {
                if (e.which === 13) {
                    searchQuery = $(this).val();
                    currentPage = 1;
                    loadBlogsData();
                }
            });

            $('#filter-category').change(function() {
                categoryFilter = $(this).val();
                currentPage = 1;
                loadBlogsData();
            });

            $('#entries-select').change(function() {
                entriesPerPage = parseInt($(this).val());
                currentPage = 1;
                loadBlogsData();
            });

            // Load category options for filter dropdown
            function loadCategoryOptions() {
                $.ajax({
                    url: _baseURL + 'api/blog-categories',
                    method: 'GET',
                    success: function(response) {
                        const select = $('#filter-category');
                        
                        select.find('option:not(:first)').remove();

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(category => {
                                const optionHtml = `<option value="${category.id}">${category.name}</option>`;
                                select.append(optionHtml);
                            });
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat opsi kategori.');
                    }
                });
            }

            // Load blogs data with AJAX
            function loadBlogsData() {
                $('#loading-indicator').show();
                $('#blogs-table-body').hide();

                $.ajax({
                    url: _baseURL + 'api/blog',
                    method: 'GET',
                    data: {
                        page: currentPage,
                        per_page: entriesPerPage,
                        search: searchQuery,
                        category: categoryFilter
                    },
                    success: function(response) {
                        renderBlogsTable(response);
                        renderPagination(response);
                        updatePaginationInfo(response);

                        // Hide loading indicator
                        $('#loading-indicator').hide();
                        $('#blogs-table-body').show();
                    },
                    error: function(xhr) {
                        $('#loading-indicator').hide();
                        $('#blogs-table-body').html(`
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    <div class="text-danger">
                                        <i class="mdi mdi-alert-circle display-4"></i>
                                        <p class="mt-2">Gagal memuat data. Silakan coba lagi.</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                        $('#blogs-table-body').show();
                        toastr.error('Gagal memuat data blog.');
                    }
                });
            }

            // Render blogs table with data
            function renderBlogsTable(response) {
                const tableBody = $('#blogs-table-body');
                tableBody.empty();

                if (!response.data || response.data.length === 0) {
                    tableBody.html(`
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                <div class="text-muted">
                                    <i class="mdi mdi-database-remove display-4"></i>
                                    <p class="mt-2">Belum ada data blog</p>
                            </div>
                        </td>
                    </tr>
                `);
                    return;
                }

                response.data.forEach(blog => {
                    const statusBadge = blog.is_published 
                        ? '<span class="badge bg-success published-badge">Dipublikasikan</span>' 
                        : '<span class="badge bg-warning published-badge">Draft</span>';

                    const row = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        id="actionMenu${blog.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="actionMenu${blog.id}">
                                        <a class="dropdown-item" href="${_baseURL}blog/${blog.slug}" target="_blank">
                                            <i class="mdi mdi-eye text-primary me-2"></i>Lihat
                                        </a>
                                        <a class="dropdown-item" href="${_baseURL}operator/blog/edit/${blog.id}">
                                            <i class="mdi mdi-pencil text-info me-2"></i>Edit
                                        </a>
                                        <a class="dropdown-item btn-hapus" href="javascript:void(0);" data-id="${blog.id}">
                                            <i class="mdi mdi-delete text-danger me-2"></i>Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>${blog.title}</td>
                            <td>${blog.category || '-'}</td>
                            <td>${blog.author || (blog.user ? blog.user.name : '-')}</td>
                            <td>${formatDate(blog.created_at)}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            }
            
            // Render pagination controls
            function renderPagination(response) {
                totalEntries = response.total;
                totalPages = response.last_page;
                currentPage = response.current_page;

                const paginationContainer = $('#pagination-container');
                paginationContainer.empty();

                // Previous button
                paginationContainer.append(`
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `);

                // Page numbers
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                // Ensure we always show at least 5 page links if available
                if (endPage - startPage < 4 && totalPages > 4) {
                    if (currentPage < totalPages / 2) {
                        endPage = Math.min(startPage + 4, totalPages);
                    } else {
                        startPage = Math.max(1, endPage - 4);
                    }
                }

                // First page link if not included in range
                if (startPage > 1) {
                    paginationContainer.append(`
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="1">1</a>
                        </li>
                    `);
                    if (startPage > 2) {
                        paginationContainer.append(`
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:void(0);">...</a>
                            </li>
                        `);
                    }
                }

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationContainer.append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                // Last page link if not included in range
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationContainer.append(`
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:void(0);">...</a>
                            </li>
                        `);
                    }
                    paginationContainer.append(`
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${totalPages}">${totalPages}</a>
                        </li>
                    `);
                }

                // Next button
                paginationContainer.append(`
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `);

                // Attach click event to pagination links
                $('.page-link').click(function() {
                    if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
                        currentPage = parseInt($(this).data('page'));
                        loadBlogsData();
                    }
                });
            }

            // Update pagination info text
            function updatePaginationInfo(response) {
                const start = (response.current_page - 1) * response.per_page + 1;
                const end = Math.min(start + response.per_page - 1, response.total);

                $('#pagination-info').text(
                    `Menampilkan ${response.total > 0 ? start : 0} sampai ${end} dari ${response.total} entri`
                );
            }

            // Helper functions
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Delete blog
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Hapus Blog?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: _baseURL + 'api/blog/' + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Blog telah dihapus.',
                                        'success'
                                    );
                                    loadBlogsData();
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message || 'Gagal menghapus blog.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus blog.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush