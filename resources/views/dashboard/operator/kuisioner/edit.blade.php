@extends('layout.app')
@section('title', 'Dashboard | Edit Kuesioner')


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
                                            <div class="col-md-3">
                                                <small class="text-muted">Tipe Pertanyaan:</small>
                                                <br>
                                                <span class="badge bg-info">{{ ucfirst($question->question_type) }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Bobot:</small>
                                                <br>
                                                <span class="badge bg-secondary">{{ $question->weight }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Kriteria:</small>
                                                <br>
                                                <span
                                                    class="badge {{ $question->criteria_type === 'education' ? 'bg-success' : ($question->criteria_type === 'experience' ? 'bg-warning' : 'bg-info') }}">
                                                    {{ ucfirst($question->criteria_type) }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Range Nilai:</small>
                                                <br>
                                                <span class="badge bg-dark">1-5</span>
                                            </div>
                                        </div>

                                        @if ($question->question_type === 'multiple_choice' && is_array($question->options))
                                            <div class="mt-3">
                                                <small class="text-muted">Pilihan Jawaban:</small>
                                                <div class="row mt-1">
                                                    @foreach ($question->options as $optIndex => $option)
                                                        @if (is_array($option) && isset($option['text']))
                                                            <div class="col-md-6 mb-1">
                                                                <div class="d-flex align-items-center border rounded p-2">
                                                                    <span
                                                                        class="me-2 badge bg-secondary">{{ chr(65 + $optIndex) }}</span>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addQuestionForm">
                    @csrf
                    <div class="modal-body">
                        <div id="modal-alert"></div>

                        <!-- Info Box -->
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi Kriteria:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Education: Pertanyaan terkait latar belakang pendidikan, nilai akademik, dll.</li>
                                <li>Experience: Pertanyaan terkait pengalaman, proyek, magang, dll.</li>
                                <li>Technical: Pertanyaan terkait kemampuan teknis, keterampilan, tools, dll.</li>
                                <li>Semua jawaban dinilai dengan skala 1-5 atau pilihan dengan nilai setara</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" name="question_text" required>
                            <small class="text-muted">Pastikan pertanyaan sesuai dengan tipe kriteria yang dipilih</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Kriteria</label>
                            <select class="form-select" name="criteria_type" id="criteriaType" required>
                                <option value="">Pilih Tipe Kriteria</option>
                                @foreach ($jobs->pluck('criteria_values')->collapse()->keys()->unique() as $criteria)
                                    <option value="{{ $criteria }}">{{ ucfirst($criteria) }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kriteria ini diambil dari data pekerjaan yang tersedia</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Pertanyaan</label>
                            <select class="form-select" name="question_type" id="questionType" required>
                                <option value="multiple_choice">Pilihan Ganda (dengan nilai 1-5)</option>
                                <option value="scale">Skala 1-5</option>
                            </select>
                        </div>

                        <div id="optionsContainer" class="mb-3" style="display: none;">
                            <label class="form-label">Pilihan Jawaban</label>
                            <small class="text-muted d-block mb-2">Setiap pilihan harus memiliki nilai antara 1-5</small>
                            <div id="optionsList">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control option-input" name="options[][text]"
                                        placeholder="Pilihan 1" required>
                                    <input type="number" class="form-control option-value" name="options[][value]"
                                        placeholder="Nilai (1-5)" min="1" max="5" required
                                        style="max-width: 120px;">
                                    <button type="button" class="btn btn-outline-danger remove-option">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addOption">
                                <i class="fas fa-plus"></i> Tambah Pilihan
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bobot Pertanyaan (1-5)</label>
                            <input type="number" class="form-control" name="weight" min="1" max="5"
                                step="0.1" required>
                            <small class="text-muted">Masukkan bobot antara 1-5 (semakin tinggi semakin penting)</small>
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
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Function to show alerts using Toastr
            function showAlert(type, message) {
                switch (type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'info':
                        toastr.info(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    case 'danger':
                        toastr.error(message);
                        break;
                }
            }

            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const questionnaireId = {{ $questionnaire->id }};

            // Toggle options container and required attribute
            $('#questionType').on('change', function() {
                const isMultipleChoice = $(this).val() === 'multiple_choice';
                const $optionsContainer = $('#optionsContainer');
                const $optionInputs = $('.option-input');
                const $optionValues = $('.option-value');

                if (isMultipleChoice) {
                    $optionsContainer.slideDown();
                    $optionInputs.prop('required', true);
                    $optionValues.prop('required', true);
                } else {
                    $optionsContainer.slideUp();
                    $optionInputs.prop('required', false);
                    $optionValues.prop('required', false);
                }
            }).trigger('change');

            // Add option field
            $('#addOption').on('click', function() {
                const optionCount = $('#optionsList .input-group').length + 1;
                const newOption = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control option-input" name="options[][text]" 
                            placeholder="Pilihan ${optionCount}" required>
                        <input type="number" class="form-control option-value" name="options[][value]" 
                            placeholder="Nilai (1-5)" min="1" max="5" required style="max-width: 120px;">
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
                    showAlert('warning',
                        'Minimal harus ada satu pilihan jawaban untuk tipe pilihan ganda');
                }
            });

            // Validate option values
            $(document).on('input', '.option-value', function() {
                const value = parseInt($(this).val());
                if (value < 1 || value > 5) {
                    $(this).addClass('is-invalid');
                    showAlert('warning', 'Nilai pilihan harus antara 1-5');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Edit questionnaire form submission
            $('#editQuestionnaireForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

                const data = {
                    title: form.find('input[name="title"]').val(),
                    description: form.find('textarea[name="description"]').val(),
                    is_active: $('#is_active').prop('checked'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: `/api/profile-operator/kuisioner/update/${questionnaireId}`,
                    method: 'PUT',
                    data: data,
                    success: function(response) {
                        showAlert('success', 'Kuesioner berhasil diperbarui');
                        if (data.is_active) {
                            showAlert('info',
                                'Kuesioner ini telah diaktifkan dan kuesioner lain telah dinonaktifkan'
                            );
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Terjadi kesalahan:';
                        Object.keys(errors).forEach(key => {
                            errorMessage += `\n- ${errors[key][0]}`;
                        });
                        showAlert('danger', errorMessage);
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

                    const values = $('.option-value').map(function() {
                        return parseInt($(this).val());
                    }).get();

                    if (options.length === 0 || options.some(opt => !opt)) {
                        showAlert('danger', 'Semua pilihan jawaban harus diisi');
                        return false;
                    }

                    if (values.some(val => val < 1 || val > 5 || isNaN(val))) {
                        showAlert('danger', 'Semua nilai pilihan harus antara 1-5');
                        return false;
                    }
                    
                    // Log for debugging
                    console.log('Options to be submitted:', Array.from($('#optionsList .input-group')).map(group => {
                        return {
                            text: $(group).find('.option-input').val(),
                            value: $(group).find('.option-value').val()
                        };
                    }));
                }

                // Validate weight
                const weight = parseFloat(form.find('input[name="weight"]').val());
                if (weight < 1 || weight > 5) {
                    showAlert('danger', 'Bobot harus antara 1-5');
                    return false;
                }

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
                $('#modal-alert').empty();

                $.ajax({
                    url: `/api/profile-operator/kuisioner/${questionnaireId}/questions`,
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#addQuestionModal').modal('hide');
                        form[0].reset();
                        showAlert('success', 'Pertanyaan berhasil ditambahkan');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        showAlert('danger',
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

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pertanyaan yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm"></span>'
                        );

                        $.ajax({
                            url: `/api/profile-operator/kuisioner/questions/${questionId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Pertanyaan berhasil dihapus.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message ||
                                        'Gagal menghapus pertanyaan',
                                        'error'
                                    );
                                    btn.prop('disabled', false).html(
                                        '<i class="fas fa-trash"></i>');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus pertanyaan',
                                    'error'
                                );
                                btn.prop('disabled', false).html(
                                    '<i class="fas fa-trash"></i>');
                            }
                        });
                    }
                });
            });

            // Reset modal when closed
            $('#addQuestionModal').on('hidden.bs.modal', function() {
                $('#addQuestionForm')[0].reset();
                $('#optionsList').html(`
                    <div class="input-group mb-2">
                        <input type="text" class="form-control option-input" name="options[][text]"
                            placeholder="Pilihan 1" required>
                        <input type="number" class="form-control option-value" name="options[][value]"
                            placeholder="Nilai (1-5)" min="1" max="5" required
                            style="max-width: 120px;">
                        <button type="button" class="btn btn-outline-danger remove-option">×</button>
                    </div>
                `);
                $('#questionType').trigger('change');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Update info box saat kriteria dipilih
            $('#criteriaType').on('change', function() {
                const selectedCriteria = $(this).val();
                if (selectedCriteria) {
                    // Tampilkan contoh pertanyaan sesuai kriteria
                    let examples = 'Contoh pertanyaan untuk kriteria ' + selectedCriteria + ':<br>';
                    switch (selectedCriteria.toLowerCase()) {
                        case 'pendidikan':
                            examples += '- Apa tingkat pendidikan terakhir Anda?<br>';
                            examples += '- Berapa IPK/nilai akhir Anda?';
                            break;
                            // Tambahkan case untuk kriteria lainnya
                        default:
                            examples += '- Sesuaikan pertanyaan dengan kriteria yang dipilih';
                    }
                    $('.criteria-examples').html(examples);
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

        .modal-lg {
            max-width: 800px;
        }

        .option-value.is-invalid {
            border-color: #dc3545;
        }

        .alert-info {
            border-left: 4px solid #0dcaf0;
        }
    </style>
@endpush
