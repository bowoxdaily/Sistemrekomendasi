@extends('layout.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Rekomendasi Pekerjaan</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
                <i class="fas fa-plus"></i> Tambah Pekerjaan
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div id="alert-container"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pekerjaan</th>
                                <th>Tipe Industri</th>
                                <th>Rata-rata Gaji</th>
                                <th>Persyaratan</th>
                                <th>Keahlian</th>
                                <th>Kriteria</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td>{{ $job->name }}</td>
                                    <td>{{ $job->industry_type }}</td>
                                    <td>Rp {{ number_format($job->average_salary, 0, ',', '.') }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($job->requirements as $req)
                                                <li><small>• {{ $req }}</small></li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @foreach ($job->skills_needed as $skill)
                                            <span class="badge bg-secondary mb-1">{{ $skill }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($job->criteria_values as $criteria => $value)
                                            <div class="mb-1">
                                                <span class="badge bg-info">
                                                    {{ ucfirst($criteria) }}: {{ $value }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info edit-job" data-id="{{ $job->id }}"
                                                data-job="{{ json_encode($job) }}" data-bs-toggle="modal"
                                                data-bs-target="#editJobModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-job" data-id="{{ $job->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Belum ada data pekerjaan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pekerjaan -->
    <div class="modal fade" id="addJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pekerjaan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addJobForm">
                    @csrf
                    <!-- filepath: d:\Project\Sistemrekomendasi\resources\views\dashboard\operator\jobs\index.blade.php -->
                    <!-- Inside the modal form -->
                    <div class="modal-body">
                        <div id="modal-alert"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pekerjaan</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Industri</label>
                                <input type="text" class="form-control" name="industry_type" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rata-rata Gaji</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="average_salary" required>
                                </div>
                            </div>

                            <!-- Requirements (Dynamic Fields) -->
                            <div class="col-12">
                                <label class="form-label">Persyaratan</label>
                                <div id="requirements-container">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="requirements[]" required>
                                        <button type="button" class="btn btn-outline-secondary add-requirement">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Needed (Dynamic Fields) -->
                            <div class="col-12">
                                <label class="form-label">Keahlian yang Dibutuhkan</label>
                                <div id="skills-container">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="skills_needed[]" required>
                                        <button type="button" class="btn btn-outline-secondary add-skill">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Criteria Values -->
                            <div class="col-12">
                                <h6 class="mb-3">Nilai Kriteria (1-5)</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Pendidikan</label>
                                        <input type="number" class="form-control" name="criteria_values[education]"
                                            min="1" max="5" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Pengalaman</label>
                                        <input type="number" class="form-control" name="criteria_values[experience]"
                                            min="1" max="5" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Keahlian Teknis</label>
                                        <input type="number" class="form-control" name="criteria_values[technical]"
                                            min="1" max="5" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
        }

        .list-unstyled li {
            margin-bottom: 0.25rem;
        }

        .modal-lg {
            max-width: 800px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            function showAlert(container, type, message) {
                const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
                $(`#${container}`).html(alertHtml);
            }

            // Add dynamic field
            function addDynamicField(container, name) {
                const newField = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="${name}[]" required>
                <button type="button" class="btn btn-outline-danger remove-field">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
                container.append(newField);
            }

            // Add requirement field
            $('.add-requirement').on('click', function() {
                addDynamicField($('#requirements-container'), 'requirements');
            });

            // Add skill field
            $('.add-skill').on('click', function() {
                addDynamicField($('#skills-container'), 'skills_needed');
            });

            // Remove field
            $(document).on('click', '.remove-field', function() {
                const container = $(this).closest('.col-12');
                const inputGroups = container.find('.input-group');

                if (inputGroups.length > 1) {
                    $(this).closest('.input-group').remove();
                } else {
                    showAlert('modal-alert', 'warning', 'Minimal harus ada satu input');
                }
            });

            // Form submission
            $('#addJobForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('#modal-alert').empty();

                submitBtn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: "{{ route('operator.jobs.store') }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        showAlert('alert-container', 'success', response.message);
                        $('#addJobModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                const input = form.find(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback">${errors[key][0]}</div>`
                                );
                            });
                            showAlert('modal-alert', 'danger',
                                'Mohon periksa kembali input Anda');
                        } else {
                            showAlert('modal-alert', 'danger',
                                xhr.responseJSON?.message || 'Terjadi kesalahan');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false)
                            .html('<i class="fas fa-save me-1"></i> Simpan');
                    }
                });
            });

            // Delete job
            $('.delete-job').on('click', function() {
                const btn = $(this);
                const jobId = btn.data('id');

                if (confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?')) {
                    btn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span>');

                    $.ajax({
                        url: `/operator/jobs/${jobId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            showAlert('alert-container', 'success', response.message);
                            btn.closest('tr').fadeOut(400, function() {
                                $(this).remove();
                                if ($('tbody tr').length === 0) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            showAlert('alert-container', 'danger',
                                xhr.responseJSON?.message || 'Terjadi kesalahan');
                            btn.prop('disabled', false)
                                .html('<i class="fas fa-trash"></i>');
                        }
                    });
                }
            });
        });
    </script>
@endpush
