@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2>Edit Kuesioner</h2>
                <div id="alert-container"></div>
            </div>
        </div>

        <!-- Form Edit Kuesioner -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="editQuestionnaireForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Judul Kuesioner</label>
                        <input type="text" class="form-control" name="title" value="{{ $questionnaire->title }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3" required>{{ $questionnaire->description }}</textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1"
                            {{ $questionnaire->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Aktifkan Kuesioner
                            <small class="text-muted">(Mengaktifkan kuesioner ini akan menonaktifkan kuesioner lain)</small>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <!-- Daftar Pertanyaan -->
        <!-- Replace the existing questions list section with this: -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pertanyaan</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="fas fa-plus"></i> Tambah Pertanyaan
                </button>
            </div>
            <div class="card-body">
                <div id="questions-list">
                    @forelse($questionnaire->questions as $index => $question)
                        <div class="card mb-3 question-item shadow-sm" data-id="{{ $question->id }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="question-number">
                                            <span class="badge bg-primary rounded-circle">{{ $index + 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="mb-3 fw-bold">{{ $question->question_text }}</h6>
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <small class="text-muted">Tipe Pertanyaan:</small>
                                                <br>
                                                <span class="badge bg-info">{{ ucfirst($question->question_type) }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Bobot:</small>
                                                <br>
                                                <span class="badge bg-secondary">{{ $question->weight }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Kriteria:</small>
                                                <br>
                                                <span
                                                    class="badge {{ $question->criteria_type === 'benefit' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($question->criteria_type) }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($question->question_type === 'multiple_choice' && is_array($question->options))
                                            <div class="mt-3">
                                                <small class="text-muted">Pilihan Jawaban:</small>
                                                <div class="row mt-1">
                                                    @foreach ($question->options as $optIndex => $option)
                                                        @if (is_array($option) && isset($option['text']))
                                                            <div class="col-md-6 mb-1">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ chr(65 + $optIndex) }}.</span>
                                                                    <span>{{ $option['text'] }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-outline-danger btn-sm delete-question float-end"
                                            data-id="{{ $question->id }}" title="Hapus Pertanyaan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-state.svg') }}" alt="Empty State" class="mb-3"
                                style="width: 200px; opacity: 0.5;">
                            <p class="text-muted">Belum ada pertanyaan. Silakan tambah pertanyaan baru.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pertanyaan -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addQuestionForm">
                    @csrf
                    <div class="modal-body">
                        <div id="modal-alert"></div>
                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" name="question_text" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe Pertanyaan</label>
                            <select class="form-select" name="question_type" id="questionType" required>
                                <option value="multiple_choice">Pilihan Ganda</option>
                                <option value="scale">Skala</option>
                                <option value="text">Teks</option>
                            </select>
                        </div>
                        <div id="optionsContainer" class="mb-3" style="display: none;">
                            <label class="form-label">Pilihan Jawaban</label>
                            <div id="optionsList">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control option-input" name="options[][text]"
                                        placeholder="Pilihan 1">
                                    <button type="button" class="btn btn-outline-danger remove-option">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addOption">
                                <i class="fas fa-plus"></i> Tambah Pilihan
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bobot (0-1)</label>
                            <input type="number" class="form-control" name="weight" min="0" max="1"
                                step="0.1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe Kriteria</label>
                            <select class="form-select" name="criteria_type" required>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
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
            // Function to show alerts
            function showAlert(container, type, message) {
                const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
                $(`#${container}`).html(alertHtml);
            }

            // Toggle options container and required attribute
            $('#questionType').on('change', function() {
                const isMultipleChoice = $(this).val() === 'multiple_choice';
                const $optionsContainer = $('#optionsContainer');
                const $optionInputs = $('.option-input');

                if (isMultipleChoice) {
                    $optionsContainer.slideDown();
                    $optionInputs.prop('required', true);
                } else {
                    $optionsContainer.slideUp();
                    $optionInputs.prop('required', false);
                }
            }).trigger('change');

            // Add option field
            $('#addOption').on('click', function() {
                const optionCount = $('#optionsList .input-group').length + 1;
                const newOption = `
            <div class="input-group mb-2">
                <input type="text" class="form-control option-input" name="options[][text]" 
                       placeholder="Pilihan ${optionCount}" ${$('#questionType').val() === 'multiple_choice' ? 'required' : ''}>
                <button type="button" class="btn btn-outline-danger remove-option">×</button>
            </div>
        `;
                $('#optionsList').append(newOption);
            });

            // Remove option field
            $(document).on('click', '.remove-option', function() {
                const optionsCount = $('#optionsList .input-group').length;
                if (optionsCount > 1) {
                    $(this).closest('.input-group').remove();
                } else {
                    showAlert('modal-alert', 'warning',
                        'Minimal harus ada satu pilihan jawaban untuk tipe pilihan ganda');
                }
            });

            // Edit questionnaire form submission
            $('#editQuestionnaireForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

                const formData = new FormData(form[0]);
                formData.set('is_active', $('#is_active').is(':checked') ? '1' : '0');

                $.ajax({
                    url: "{{ route('operator.questionnaires.update', $questionnaire->id) }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showAlert('alert-container', 'success',
                            'Kuesioner berhasil diperbarui');
                        if (formData.get('is_active') === '1') {
                            showAlert('alert-container', 'info',
                                'Kuesioner ini telah diaktifkan dan kuesioner lain telah dinonaktifkan'
                            );
                        }
                    },
                    error: function(xhr) {
                        showAlert('alert-container', 'danger',
                            xhr.responseJSON?.message || 'Terjadi kesalahan');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Add question form submission
            $('#addQuestionForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const questionType = $('#questionType').val();
                const submitBtn = form.find('button[type="submit"]');

                // Validate options for multiple choice
                if (questionType === 'multiple_choice') {
                    const options = $('.option-input').map(function() {
                        return $(this).val().trim();
                    }).get();

                    if (options.length === 0 || options.some(opt => !opt)) {
                        showAlert('modal-alert', 'danger', 'Semua pilihan jawaban harus diisi');
                        return false;
                    }
                }

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
                $('#modal-alert').empty();

                $.ajax({
                    url: "{{ route('operator.questionnaires.questions.add', $questionnaire->id) }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#addQuestionModal').modal('hide');
                        form[0].reset();
                        showAlert('alert-container', 'success',
                            'Pertanyaan berhasil ditambahkan');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        showAlert('modal-alert', 'danger',
                            xhr.responseJSON?.message || 'Terjadi kesalahan');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Delete question
            $('.delete-question').on('click', function() {
                const btn = $(this);
                const questionId = btn.data('id');

                if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span>');

                    $.ajax({
                        url: `/operator/questionnaires/questions/${questionId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            $(`div.question-item[data-id="${questionId}"]`).fadeOut(400,
                                function() {
                                    $(this).remove();
                                    showAlert('alert-container', 'success',
                                        'Pertanyaan berhasil dihapus');

                                    if ($('.question-item').length === 0) {
                                        $('#questions-list').html(`
                                <div class="text-center py-3">
                                    <p class="text-muted">Belum ada pertanyaan. Silakan tambah pertanyaan baru.</p>
                                </div>
                            `);
                                    }
                                });
                        },
                        error: function(xhr) {
                            showAlert('alert-container', 'danger',
                                xhr.responseJSON?.message || 'Terjadi kesalahan');
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-trash"></i> Hapus');
                        }
                    });
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .question-item {
            transition: all 0.3s ease;
            border: none;
            border-left: 4px solid #0d6efd;
        }

        .question-item:hover {
            transform: translateX(5px);
        }

        .question-number .badge {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-question {
            opacity: 0.7;
        }

        .delete-question:hover {
            opacity: 1;
        }

        .badge {
            font-weight: 500;
        }
    </style>
@endpush
