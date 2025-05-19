@extends('layout.app')

@section('title', 'Dashboard | Pekerjaan')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Pekerjaan</h4>
                                <button class="btn btn-primary btn-icon-text" data-bs-toggle="modal"
                                    data-bs-target="#jobModal">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    Tambah Pekerjaan
                                </button>
                            </div>

                            <!-- Search and filter section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari pekerjaan...">
                                        <button class="btn btn-primary" id="search-button" type="button">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filter-industry">
                                        <option value="">Semua Industri</option>
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
                                <table class="table table-hover" id="jobs-table">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>Nama Pekerjaan</th>
                                            <th>Tipe Industri</th>
                                            <th>Rata-rata Gaji</th>
                                            <th>Persyaratan</th>
                                            <th>Keahlian</th>
                                            <th>Kriteria</th>
                                        </tr>
                                    </thead>
                                    <tbody id="jobs-table-body">
                                        <!-- Data will be loaded here via AJAX -->
                                        <tr id="empty-placeholder">
                                            <td colspan="7" class="text-center py-3">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-database-remove display-4"></i>
                                                    <p class="mt-2">Memuat data pekerjaan...</p>
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

    <!-- Modal Pekerjaan -->
    <div class="modal fade" id="jobModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Tambah Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="jobForm">
                    @csrf
                    <input type="hidden" id="job_id">
                    <div class="modal-body">
                        <div id="modal-alert"></div>

                        <div class="form-group">
                            <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>Tipe Industri <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="industry_type" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="description" rows="2" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Rata-rata Gaji <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="average_salary" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Persyaratan <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 add-requirement">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </label>
                            <div id="requirements-container">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" class="form-control" name="requirements[]" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-field">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Keahlian <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 add-skill">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </label>
                            <div id="skills-container">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" class="form-control" name="skills_needed[]" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-field">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Kriteria & Nilai (1-5) <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 add-criteria">
                                    <i class="mdi mdi-plus"></i> Tambah Kriteria
                                </button>
                            </label>
                            <small class="text-muted d-block mb-2">
                                Tambahkan kriteria dan nilai yang dibutuhkan untuk pekerjaan ini
                            </small>
                            <div id="criteria-container">
                                <!-- Kriteria akan ditambahkan di sini -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="saveBtn">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Pekerjaan -->
    <div class="modal fade" id="editJobModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editJobForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_job_id" name="id">
                    <div class="modal-body">
                        <div id="edit-modal-alert"></div>

                        <div class="form-group">
                            <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" id="edit_name"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Tipe Industri <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="industry_type"
                                id="edit_industry_type" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="description" id="edit_description" rows="2" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Rata-rata Gaji <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="average_salary"
                                    id="edit_average_salary" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Persyaratan <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 add-edit-requirement">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </label>
                            <div id="edit-requirements-container">
                                <!-- Requirements will be loaded here -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Keahlian <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 add-edit-skill">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </label>
                            <div id="edit-skills-container">
                                <!-- Skills will be loaded here -->
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label>Nilai Kriteria (1-5) <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-4">
                                    <label class="small">Pendidikan</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="criteria_values[education]" id="edit_criteria_education" min="1"
                                        max="5" required>
                                </div>
                                <div class="col-4">
                                    <label class="small">Pengalaman</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="criteria_values[experience]" id="edit_criteria_experience" min="1"
                                        max="5" required>
                                </div>
                                <div class="col-4">
                                    <label class="small">Keahlian</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="criteria_values[technical]" id="edit_criteria_technical" min="1"
                                        max="5" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="updateBtn">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .modal-body {
            max-height: 65vh;
            overflow-y: auto;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .badge {
            font-weight: 500;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .input-group-sm>.form-control,
        .input-group-sm>.input-group-text {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap modal
            const jobModal = new bootstrap.Modal(document.getElementById('jobModal'));

            // Pagination state
            let currentPage = 1;
            let entriesPerPage = 10;
            let totalEntries = 0;
            let totalPages = 0;

            // Filter state
            let searchQuery = '';
            let industryFilter = '';

            // Initialize and load data
            loadIndustryOptions();
            loadJobsData();

            // Search and filter functionality
            $('#search-button').click(function() {
                searchQuery = $('#search-input').val();
                currentPage = 1;
                loadJobsData();
            });

            $('#search-input').keypress(function(e) {
                if (e.which === 13) {
                    searchQuery = $(this).val();
                    currentPage = 1;
                    loadJobsData();
                }
            });

            $('#filter-industry').change(function() {
                industryFilter = $(this).val();
                currentPage = 1;
                loadJobsData();
            });

            $('#entries-select').change(function() {
                entriesPerPage = parseInt($(this).val());
                currentPage = 1;
                loadJobsData();
            });

            // Load industry options for filter dropdown
            function loadIndustryOptions() {
                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/job',
                    method: 'GET',
                    success: function(response) {
                        const select = $('#filter-industry');
                        select.find('option:not(:first)').remove();

                        if (response.industries && response.industries.length > 0) {
                            response.industries.forEach(industry => {
                                select.append(
                                    `<option value="${industry}">${industry}</option>`);
                            });
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat opsi industri.');
                    }
                });
            }

            // Load jobs data with AJAX
            function loadJobsData() {
                $('#loading-indicator').show();
                $('#jobs-table-body').hide();

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/job',
                    method: 'GET',
                    data: {
                        page: currentPage,
                        per_page: entriesPerPage,
                        search: searchQuery,
                        industry: industryFilter
                    },
                    success: function(response) {
                        renderJobsTable(response);
                        renderPagination(response);
                        updatePaginationInfo(response);

                        // Hide loading indicator
                        $('#loading-indicator').hide();
                        $('#jobs-table-body').show();
                    },
                    error: function(xhr) {
                        $('#loading-indicator').hide();
                        $('#jobs-table-body').html(`
                            <tr>
                                <td colspan="7" class="text-center py-3">
                                    <div class="text-danger">
                                        <i class="mdi mdi-alert-circle display-4"></i>
                                        <p class="mt-2">Gagal memuat data. Silakan coba lagi.</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                        $('#jobs-table-body').show();
                        toastr.error('Gagal memuat data pekerjaan.');
                    }
                });
            }

            // Render jobs table with data
            function renderJobsTable(response) {
                const tableBody = $('#jobs-table-body');
                tableBody.empty();

                if (!response.data || response.data.length === 0) {
                    tableBody.html(`
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <div class="text-muted">
                                    <i class="mdi mdi-database-remove display-4"></i>
                                    <p class="mt-2">Belum ada data pekerjaan</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    return;
                }

                response.data.forEach(job => {
                    const criteriaHtml = Object.entries(job.criteria_values).map(([criteria, value]) => {
                        const weight = job.criteria_weights?.[criteria] || 1;
                        return `<div class="mb-1">
                            <span class="badge bg-info">
                                ${capitalizeFirstLetter(criteria)}: ${value} 
                                <small>(Bobot: ${weight})</small>
                            </span>
                        </div>`;
                    }).join('');

                    const row = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item btn-edit" href="javascript:void(0);"
                                            data-id="${job.id}">
                                            <i class="mdi mdi-pencil text-info me-2"></i>Edit
                                        </a>
                                        <a class="dropdown-item btn-hapus" href="javascript:void(0);"
                                            data-id="${job.id}">
                                            <i class="mdi mdi-delete text-danger me-2"></i>Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>${job.name}</td>
                            <td>${job.industry_type}</td>
                            <td>Rp ${formatNumber(job.average_salary)}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    ${job.requirements.map(req => `<li><small>• ${req}</small></li>`).join('')}
                                </ul>
                            </td>
                            <td>
                                ${job.skills_needed.map(skill => 
                                    `<span class="badge bg-secondary">${skill}</span>`
                                ).join(' ')}
                            </td>
                            <td>
                                ${criteriaHtml}
                            </td>
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
                        loadJobsData();
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
            function formatNumber(number) {
                return Number(number).toLocaleString('id-ID');
            }

            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Dynamic form fields management
            function addDynamicField(container, name) {
                const newField = `
                <div class="input-group input-group-sm mb-2">
                    <input type="text" class="form-control" name="${name}[]" required>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-field">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                `;
                container.append(newField);
            }

            $('.add-requirement').click(() => addDynamicField($('#requirements-container'), 'requirements'));
            $('.add-skill').click(() => addDynamicField($('#skills-container'), 'skills_needed'));

            $(document).on('click', '.remove-field', function() {
                const container = $(this).closest('.form-group').find('.input-group');
                if (container.length > 1) {
                    $(this).closest('.input-group').remove();
                }
            });

            // Form submission with AJAX
            $('#jobForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = $('#saveBtn');
                const isEdit = $('#job_id').val() !== '';

                // Kumpulkan data form
                let formData = {
                    name: form.find('[name="name"]').val(),
                    description: form.find('[name="description"]').val(),
                    industry_type: form.find('[name="industry_type"]').val(),
                    average_salary: form.find('[name="average_salary"]').val(),
                    requirements: [],
                    skills_needed: [],
                    criteria_values: {},
                    criteria_weights: {}
                };

                // Kumpulkan requirements
                form.find('input[name="requirements[]"]').each(function() {
                    if ($(this).val()) {
                        formData.requirements.push($(this).val());
                    }
                });

                // Kumpulkan skills
                form.find('input[name="skills_needed[]"]').each(function() {
                    if ($(this).val()) {
                        formData.skills_needed.push($(this).val());
                    }
                });

                // Kumpulkan criteria dan bobot
                form.find('.criteria-item').each(function() {
                    const name = $(this).find('[name="criteria_names[]"]').val().toLowerCase();
                    const value = parseInt($(this).find('[name="criteria_values[]"]').val());
                    const weight = parseInt($(this).find('[name="criteria_weights[]"]').val());

                    formData.criteria_values[name] = value;
                    formData.criteria_weights[name] = weight;
                });

                submitBtn.prop('disabled', true)
                    .html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                $.ajax({
                    url: isEdit ?
                        _baseURL + 'api/profile-operator/update/job/' + $('#job_id').val() :
                        _baseURL + 'api/profile-operator/create/job',
                    method: isEdit ? 'PUT' : 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            jobModal.hide();
                            resetForm();
                            loadJobsData();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                            Object.values(xhr.responseJSON.errors).forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                            errorHtml += '</ul></div>';
                            $('#modal-alert').html(errorHtml);
                        } else {
                            toastr.error(message);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false)
                            .html('<i class="mdi mdi-content-save"></i> Simpan');
                    }
                });
            });

            // Edit job
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $('#modal-alert').html('');

                $.ajax({
                    url: _baseURL + 'api/profile-operator/get/job/' + id,
                    method: 'GET',
                    success: function(response) {
                        const data = response.data[0];
                        $('#jobModalLabel').text('Edit Pekerjaan');
                        $('#job_id').val(id);

                        $('#jobForm [name="name"]').val(data.name);
                        $('#jobForm [name="industry_type"]').val(data.industry_type);
                        $('#jobForm [name="description"]').val(data.description);
                        $('#jobForm [name="average_salary"]').val(data.average_salary);

                        $('#requirements-container').empty();
                        data.requirements.forEach(req => {
                            addDynamicField($('#requirements-container'),
                                'requirements');
                            $('#requirements-container input:last').val(req);
                        });

                        $('#skills-container').empty();
                        data.skills_needed.forEach(skill => {
                            addDynamicField($('#skills-container'), 'skills_needed');
                            $('#skills-container input:last').val(skill);
                        });

                        $('#criteria-container').empty();
                        if (data.criteria_values) {
                            Object.entries(data.criteria_values).forEach(([name, value]) => {
                                const weight = data.criteria_weights?.[name] || 1;
                                addCriteriaField($('#criteria-container'), name, value,
                                    weight);
                            });
                        }

                        jobModal.show();
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data pekerjaan');
                    }
                });
            });

            // Delete job
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus Pekerjaan?',
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
                            url: _baseURL + 'api/profile-operator/delete/job/' + id,
                            type: 'DELETE', // Changed from method to type
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    loadJobsData(); // Reload the table
                                } else {
                                    toastr.error(response.message ||
                                        'Gagal menghapus data');
                                }
                            },
                            error: function(xhr) {
                                console.error('Delete error:', xhr);
                                toastr.error(xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data');
                            }
                        });
                    }
                });
            });

            // Reset form when modal is closed or new job button is clicked
            $('#jobModal').on('hidden.bs.modal', resetForm);

            $('[data-bs-target="#jobModal"]').click(function() {
                resetForm();
                $('#jobModalLabel').text('Tambah Pekerjaan');
            });

            function resetForm() {
                $('#jobForm')[0].reset();
                $('#job_id').val('');
                $('#modal-alert').html('');

                // Reset dynamic fields to just one empty field
                $('#requirements-container').html(`
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control" name="requirements[]" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-field">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                `);

                $('#skills-container').html(`
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control" name="skills_needed[]" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-field">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                `);

                $('#criteria-container').html('');
            }

            // Fungsi untuk menambah kriteria
            function addCriteriaField(container, name = '', value = '', weight = 1) {
                const criteriaField = `
                    <div class="row mb-2 criteria-item">
                        <div class="col-5">
                            <input type="text" class="form-control form-control-sm" 
                                   name="criteria_names[]" 
                                   placeholder="Nama Kriteria"
                                   value="${name}" required>
                        </div>
                        <div class="col-3">
                            <input type="number" class="form-control form-control-sm" 
                                   name="criteria_values[]" 
                                   placeholder="Nilai (1-5)"
                                   min="1" max="5"
                                   value="${value}" required>
                        </div>
                        <div class="col-3">
                            <input type="number" class="form-control form-control-sm" 
                                   name="criteria_weights[]" 
                                   placeholder="Bobot (1-5)"
                                   min="1" max="5"
                                   value="${weight}" required>
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-criteria">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.append(criteriaField);
            }

            // Tambah kriteria baru
            $('.add-criteria').click(function() {
                addCriteriaField($('#criteria-container'));
            });

            // Hapus kriteria
            $(document).on('click', '.remove-criteria', function() {
                $(this).closest('.criteria-item').remove();
            });
        });
    </script>
@endpush
