@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2>Daftar Kuesioner</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionnaireModal">
                    Tambah Kuesioner
                </button>
            </div>
        </div>

        <div id="alert-container"></div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="questionnaires-table">
                    @foreach ($questionnaires as $questionnaire)
                        <tr>
                            <td>{{ $questionnaire->title }}</td>
                            <td>{{ Str::limit($questionnaire->description, 100) }}</td>
                            <td>
                                <span class="badge {{ $questionnaire->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $questionnaire->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('operator.questionnaires.edit', $questionnaire) }}"
                                    class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $questionnaire->id }}"
                                    data-title="{{ $questionnaire->title }}">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                        <div class="mb-3">
                            <label class="form-label">Judul Kuesioner</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active">
                            <label class="form-check-label" for="is_active">Aktifkan Kuesioner</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#addQuestionnaireForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('operator.questionnaires.store') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        $('#addQuestionnaireModal').modal('hide');
                        form[0].reset();

                        // Show success message
                        $('#alert-container').html(
                            `<div class="alert alert-success alert-dismissible fade show">
                        Kuesioner berhasil ditambahkan
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`
                        );

                        // Redirect to edit page
                        window.location.href = response.redirect_url;
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#alert-container').html(
                            `<div class="alert alert-danger alert-dismissible fade show">
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`
                        );
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Handle delete button
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');

                if (confirm(`Apakah Anda yakin ingin menghapus kuesioner "${title}"?`)) {
                    $.ajax({
                        url: `/operator/questionnaires/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#alert-container').html(
                                `<div class="alert alert-success alert-dismissible fade show">
                            Kuesioner berhasil dihapus
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`
                            );
                            location.reload();
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            $('#alert-container').html(
                                `<div class="alert alert-danger alert-dismissible fade show">
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush
