@extends('layout.app')

@section('title', 'Dashboard | Kuesioner')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Data Kuesioner</h4>
                                <button class="btn btn-primary btn-icon-text" data-bs-toggle="modal"
                                    data-bs-target="#addQuestionnaireModal">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    Tambah Kuesioner
                                </button>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari kuesioner...">
                                        <button class="btn btn-primary" type="button">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filter-status">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                            <div id="alert-container"></div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>Judul</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Jumlah Pertanyaan</th>
                                            <th>Terakhir Diperbarui</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questionnaires as $questionnaire)
                                            <tr>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="mdi mdi-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item"
                                                                href="{{ route('operator.questionnaires.edit', $questionnaire) }}">
                                                                <i class="mdi mdi-pencil text-info me-2"></i>Edit
                                                            </a>
                                                            <a class="dropdown-item btn-hapus" href="javascript:void(0);"
                                                                data-id="{{ $questionnaire->id }}"
                                                                data-title="{{ $questionnaire->title }}">
                                                                <i class="mdi mdi-delete text-danger me-2"></i>Hapus
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $questionnaire->title }}</td>
                                                <td>{{ Str::limit($questionnaire->description, 100) }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $questionnaire->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $questionnaire->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>{{ $questionnaire->questions_count ?? 0 }}</td>
                                                <td>{{ $questionnaire->updated_at->format('d M Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-3">
                                                    <div class="text-muted">
                                                        <i class="mdi mdi-database-remove display-4"></i>
                                                        <p class="mt-2">Belum ada data kuesioner</p>
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
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kuesioner -->
    <div class="modal fade" id="addQuestionnaireModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kuesioner Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addQuestionnaireForm">
                    @csrf
                    <div class="modal-body">
                        <div id="modal-alert"></div>

                        <div class="form-group">
                            <label>Judul Kuesioner <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="title" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="description" rows="3" required></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Aktifkan Kuesioner
                                </label>
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
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const questionnaireModal = new bootstrap.Modal(document.getElementById('addQuestionnaireModal'));

            $('#addQuestionnaireForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = $('#saveBtn');

                // Fix checkbox value handling
                const formData = new FormData();
                formData.append('title', form.find('input[name="title"]').val());
                formData.append('description', form.find('textarea[name="description"]').val());
                formData.append('is_active', form.find('#is_active').is(':checked') ? '1' : '0');
                formData.append('_token', '{{ csrf_token() }}');

                submitBtn.prop('disabled', true)
                    .html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                $.ajax({
                    url: "/api/profile-operator/kuisioner/create",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success('Kuesioner berhasil ditambahkan');
                        questionnaireModal.hide();
                        window.location.reload();
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false)
                            .html('<i class="mdi mdi-content-save"></i> Simpan');
                    }
                });
            });

            $('.btn-hapus').click(function() {
                const id = $(this).data('id');
                const title = $(this).data('title');

                Swal.fire({
                    title: 'Hapus Kuesioner?',
                    text: `Kuesioner "${title}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/profile-operator/kuisioner/delete/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success('Kuesioner berhasil dihapus');
                                location.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Terjadi kesalahan');
                            }
                        });
                    }
                });
            });

            // Search functionality
            $('#search-input').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Status filter
            $('#filter-status').on('change', function() {
                const value = $(this).val();
                $('tbody tr').show();
                if (value) {
                    $('tbody tr').filter(function() {
                        const status = $(this).find('.badge').text().toLowerCase();
                        return value === 'active' ? status !== 'aktif' : status !== 'tidak aktif';
                    }).hide();
                }
            });
        });
    </script>
@endpush
