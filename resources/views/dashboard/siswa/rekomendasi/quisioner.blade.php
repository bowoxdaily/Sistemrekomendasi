@extends('layout.app')

@section('title', 'Kuis - ' . $questionnaire->title)
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-15">
                <!-- Progress Bar -->
                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Progres Jawaban</span>
                            <span id="progressText" class="badge bg-primary">0 /
                                {{ $questionnaire->questions->count() }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div id="questionProgress" class="progress-bar progress-bar-striped" role="progressbar"
                                style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $questionnaire->title }}</h4>
                            <span class="badge bg-light text-primary">
                                {{ $questionnaire->questions->count() }} Pertanyaan
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($questionnaire->description)
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ $questionnaire->description }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.questionnaire.submit') }}" id="questionnaireForm">
                            @csrf
                            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

                            @foreach ($questionnaire->questions as $index => $question)
                                <div class="question-card mb-4 p-4 border rounded"
                                    data-question-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="border-bottom pb-2 flex-grow-1">
                                            <span class="badge bg-primary me-2">{{ $loop->iteration }}</span>
                                            {{ $question->question_text }}
                                            @if (isset($question->is_required) && $question->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </h5>
                                    </div>

                                    <div class="mt-3">
                                        @switch($question->question_type)
                                            @case('multiple_choice')
                                                <div class="row g-3">
                                                    @if (is_array($question->options) && count($question->options) > 0)
                                                        @foreach ($question->options as $optionIndex => $option)
                                                            @php
                                                                // Handle different option formats
                                                                if (is_array($option)) {
                                                                    $optionText =
                                                                        $option['text'] ?? ($option['label'] ?? '');
                                                                    $optionValue =
                                                                        $option['text'] ??
                                                                        ($option['label'] ??
                                                                            ($option['value'] ?? $optionText));
                                                                } else {
                                                                    $optionText = $option;
                                                                    $optionValue = $option;
                                                                }

                                                                // Skip empty options
                                                                if (empty($optionText)) {
                                                                    continue;
                                                                }
                                                            @endphp
                                                            <div class="col-md-6">
                                                                <div class="form-check custom-radio">
                                                                    <input class="form-check-input question-input" type="radio"
                                                                        name="answers[{{ $question->id }}]"
                                                                        id="question_{{ $question->id }}_option_{{ $optionIndex }}"
                                                                        value="{{ $optionValue }}"
                                                                        {{ $question->is_required ?? true ? 'required' : '' }}
                                                                        data-question-id="{{ $question->id }}">
                                                                    <label class="form-check-label w-100"
                                                                        for="question_{{ $question->id }}_option_{{ $optionIndex }}">
                                                                        <div
                                                                            class="option-box p-3 rounded h-100 d-flex align-items-center">
                                                                            <span>{{ $optionText }}</span>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="col-12">
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                Opsi pertanyaan tidak tersedia.
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @break

                                            @case('scale')
                                                <div class="scale-container">
                                                    <div class="d-flex justify-content-center mb-2">
                                                        <div class="btn-group btn-group-scale" role="group">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <input type="radio" class="btn-check question-input"
                                                                    name="answers[{{ $question->id }}]"
                                                                    id="question_{{ $question->id }}_scale_{{ $i }}"
                                                                    value="{{ $i }}" required>
                                                                <label class="btn btn-outline-primary scale-btn"
                                                                    for="question_{{ $question->id }}_scale_{{ $i }}">
                                                                    {{ $i }}
                                                                </label>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between scale-labels">
                                                        <small>Rendah</small>
                                                        <small class="text-muted">Tinggi</small>
                                                    </div>
                                                </div>
                                            @break

                                            @case('text')
                                                <div class="form-group">
                                                    <textarea class="form-control question-input" name="answers[{{ $question->id }}]" rows="4"
                                                        placeholder="Tulis jawaban Anda di sini..."
                                                        {{ isset($question->is_required) && $question->is_required ? 'required' : '' }}
                                                        data-question-id="{{ $question->id }}"></textarea>
                                                    <div class="form-text">
                                                        <small class="text-muted">
                                                            <span id="charCount_{{ $question->id }}">0</span> karakter
                                                        </small>
                                                    </div>
                                                </div>
                                            @break

                                            @default
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Tipe pertanyaan tidak didukung: {{ $question->question_type }}
                                                </div>
                                        @endswitch
                                    </div>

                                    <!-- Validation Error Display -->
                                    <div class="invalid-feedback d-none" id="error_{{ $question->id }}">
                                        Pertanyaan ini wajib dijawab.
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-5 d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Kirim Jawaban
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="reviewBtn">
                                    <i class="fas fa-eye me-2"></i>
                                    Tinjau Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Tinjauan Jawaban
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reviewContent">
                    <!-- Review content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="submitFromReview">
                        <i class="fas fa-paper-plane me-2"></i>
                        Kirim Jawaban
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .question-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef !important;
        }

        .question-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-color: #dee2e6 !important;
        }

        .question-card.answered {
            border-color: #198754 !important;
            background-color: #f8fff9;
        }

        .custom-radio .form-check-label {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-radio .option-box {
            transition: all 0.2s ease;
            border: 2px solid transparent;
            background-color: #f8f9fa;
        }

        .custom-radio .form-check-input:checked+.form-check-label .option-box {
            background-color: #e7f3ff;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
        }

        .custom-radio .option-box:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .scale-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 10px;
        }

        .btn-group-scale {
            width: auto;
            max-width: 300px;
        }

        .scale-btn {
            padding: 8px 15px;
            min-width: 45px;
            font-size: 0.95rem;
        }

        .btn-check:checked+.scale-btn {
            transform: scale(1.05);
        }

        .scale-labels {
            width: 300px;
            margin: 5px auto 0;
            padding: 0 10px;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .question-input:invalid {
            border-color: #dc3545;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        #submitBtn:disabled {
            opacity: 0.65;
        }

        .modal-body .review-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }

        .modal-body .review-item:last-child {
            border-bottom: none;
        }

        .review-question {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .review-answer {
            color: #0d6efd;
            font-weight: 500;
        }

        .review-answer.empty {
            color: #dc3545;
            font-style: italic;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const totalQuestions = {{ $questionnaire->questions->count() }};

            // Progress bar update
            function updateProgress() {
                let answeredQuestions = 0;

                $('.question-card').each(function() {
                    const $card = $(this);
                    const questionId = $card.find('.question-input').first().data('question-id');
                    const $radio = $card.find('input[type="radio"]:checked');
                    const $textarea = $card.find('textarea');

                    let isAnswered = false;

                    if ($radio.length > 0) {
                        isAnswered = true;
                    } else if ($textarea.length > 0 && $textarea.val().trim() !== '') {
                        isAnswered = true;
                    }

                    if (isAnswered) {
                        answeredQuestions++;
                        $card.addClass('answered');
                        $('#error_' + questionId).addClass('d-none');
                    } else {
                        $card.removeClass('answered');
                    }
                });

                const progress = totalQuestions > 0 ? (answeredQuestions / totalQuestions) * 100 : 0;
                $('#questionProgress').css('width', progress + '%').attr('aria-valuenow', progress);
                $('#progressText').text(answeredQuestions + ' / ' + totalQuestions);
            }

            // Character count for text areas
            $('textarea.question-input').on('input', function() {
                const questionId = $(this).data('question-id');
                const charCount = $(this).val().length;
                $('#charCount_' + questionId).text(charCount);
            });

            // Monitor input changes
            $('.question-input').on('change input keyup', function() {
                updateProgress();

                // Remove any existing validation errors
                const questionId = $(this).data('question-id');
                if (questionId) {
                    $('#error_' + questionId).addClass('d-none');
                    $(this).removeClass('is-invalid');
                }
            });

            // Review functionality
            $('#reviewBtn').on('click', function() {
                let reviewContent = '';
                let unansweredCount = 0;

                $('.question-card').each(function(index) {
                    const questionText = $(this).find('h5').clone().children().remove().end().text()
                        .trim();
                    const $radio = $(this).find('input[type="radio"]:checked');
                    const $textarea = $(this).find('textarea');

                    let answer = '';
                    let isEmpty = false;

                    if ($radio.length > 0) {
                        answer = $radio.val();
                    } else if ($textarea.length > 0) {
                        answer = $textarea.val().trim();
                        if (answer === '') {
                            isEmpty = true;
                            answer = 'Belum dijawab';
                        }
                    } else {
                        isEmpty = true;
                        answer = 'Belum dijawab';
                    }

                    if (isEmpty) unansweredCount++;

                    reviewContent += `
                        <div class="review-item">
                            <div class="review-question">${index + 1}. ${questionText}</div>
                            <div class="review-answer ${isEmpty ? 'empty' : ''}">${answer}</div>
                        </div>
                    `;
                });

                if (unansweredCount > 0) {
                    reviewContent = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Masih ada ${unansweredCount} pertanyaan yang belum dijawab.
                        </div>
                    ` + reviewContent;
                }

                $('#reviewContent').html(reviewContent);
                $('#reviewModal').modal('show');
            });

            // Submit from review modal with SweetAlert
            $('#submitFromReview').on('click', function() {
                $('#reviewModal').modal('hide');
                Swal.fire({
                    title: 'Konfirmasi Pengiriman',
                    text: 'Apakah Anda yakin ingin mengirim jawaban?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#questionnaireForm').submit();
                    } else {
                        $('#reviewModal').modal('show');
                    }
                });
            });

            // Form validation and submission
            $('#questionnaireForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submitBtn = $('#submitBtn');
                let hasEmptyRequired = false;
                let firstErrorQuestion = null;

                // Reset all validation states
                $('.question-input').removeClass('is-invalid');
                $('.invalid-feedback').addClass('d-none');

                // Check each required question
                $('.question-card').each(function() {
                    const $card = $(this);
                    const $inputs = $card.find('.question-input');
                    const questionId = $inputs.first().data('question-id');
                    const isRequired = $inputs.first().prop('required');

                    if (!isRequired) return true; // Skip non-required questions

                    const $radio = $card.find('input[type="radio"]:checked');
                    const $textarea = $card.find('textarea');

                    let isAnswered = false;

                    if ($radio.length > 0) {
                        isAnswered = true;
                    } else if ($textarea.length > 0 && $textarea.val().trim() !== '') {
                        isAnswered = true;
                    }

                    if (!isAnswered) {
                        hasEmptyRequired = true;
                        $inputs.addClass('is-invalid');
                        $('#error_' + questionId).removeClass('d-none');

                        if (!firstErrorQuestion) {
                            firstErrorQuestion = $card;
                        }
                    }
                });

                if (hasEmptyRequired) {
                    // Scroll to first error
                    if (firstErrorQuestion) {
                        $('html, body').animate({
                            scrollTop: firstErrorQuestion.offset().top - 100
                        }, 500);
                    }

                    // Show error message
                    const errorAlert = `
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Mohon jawab semua pertanyaan yang wajib diisi sebelum mengirim.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                    // Remove existing error alerts and add new one
                    $('.alert-danger').remove();
                    $form.before(errorAlert);

                    return false;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi Pengiriman',
                    text: 'Apakah Anda yakin ingin mengirim jawaban? Jawaban tidak dapat diubah setelah dikirim.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $submitBtn.prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim jawaban...'
                            );

                        // Show loading state
                        Swal.fire({
                            title: 'Mengirim jawaban...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        $form[0].submit();
                    }
                });
            });

            // Initial progress update
            updateProgress();

            // Auto-save functionality (optional)
            let autoSaveTimer;
            $('.question-input').on('change input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    console.log('Auto-saving...'); // Replace with actual auto-save logic
                }, 2000);
            });
        });
    </script>
@endpush
