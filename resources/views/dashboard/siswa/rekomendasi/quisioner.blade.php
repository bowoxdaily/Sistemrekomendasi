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
                            
                            <!-- Add this line to indicate if this is a retake -->
                            @if(isset($isRetake) && $isRetake)
                                <input type="hidden" name="is_retake" value="1">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Anda sedang mengisi ulang kuesioner. Jawaban baru Anda akan menggantikan rekomendasi sebelumnya.
                                </div>
                            @endif

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
                                                <div class="multiple-choice-container">
                                                    @php
                                                        $hasOptions = is_array($question->options) && count($question->options) > 0;
                                                    @endphp
                                                    
                                                    @if ($hasOptions)
                                                        <div class="row g-3">
                                                            @foreach ($question->options as $optionIndex => $option)
                                                                @php
                                                                    // Determine option text and value correctly
                                                                    $optionText = '';
                                                                    $optionValue = '';
                                                                    
                                                                    if (is_array($option) && isset($option['text'])) {
                                                                        $optionText = $option['text'];
                                                                        $optionValue = $option['value'] ?? $optionText;
                                                                    } elseif (is_string($option)) {
                                                                        $optionText = $option;
                                                                        $optionValue = $option;
                                                                    }
                                                                @endphp
                                                                
                                                                @if (!empty($optionText))
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="option-card">
                                                                            <input class="option-input question-input" type="radio"
                                                                                name="answers[{{ $question->id }}]"
                                                                                id="question_{{ $question->id }}_option_{{ $optionIndex }}"
                                                                                value="{{ $optionText }}"
                                                                                {{ $question->is_required ?? true ? 'required' : '' }}
                                                                                data-question-id="{{ $question->id }}">
                                                                            <label class="option-label" 
                                                                                for="question_{{ $question->id }}_option_{{ $optionIndex }}">
                                                                                <div class="option-icon">{{ chr(65 + $optionIndex) }}</div>
                                                                                <div class="option-text">{{ $optionText }}</div>
                                                                                <div class="option-check">
                                                                                    <i class="fas fa-check-circle"></i>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            Opsi pertanyaan tidak tersedia.
                                                        </div>
                                                    @endif
                                                </div>
                                            @break

                                            @case('scale')
                                                <div class="scale-container">
                                                    <div class="scale-values">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <div class="scale-option">
                                                                <input type="radio" class="scale-input question-input"
                                                                    name="answers[{{ $question->id }}]"
                                                                    id="question_{{ $question->id }}_scale_{{ $i }}"
                                                                    value="{{ $i }}" required>
                                                                <label class="scale-label" 
                                                                    for="question_{{ $question->id }}_scale_{{ $i }}">
                                                                    <div class="scale-number">{{ $i }}</div>
                                                                    <div class="scale-icon">
                                                                        @switch($i)
                                                                            @case(1)
                                                                                <i class="fas fa-frown"></i>
                                                                                @break
                                                                            @case(2)
                                                                                <i class="fas fa-meh"></i>
                                                                                @break
                                                                            @case(3)
                                                                                <i class="fas fa-meh-blank"></i>
                                                                                @break
                                                                            @case(4)
                                                                                <i class="fas fa-smile"></i>
                                                                                @break
                                                                            @case(5)
                                                                                <i class="fas fa-grin-stars"></i>
                                                                                @break
                                                                        @endswitch
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                    <div class="scale-endpoints">
                                                        <span class="scale-start">Rendah</span>
                                                        <span class="scale-end">Tinggi</span>
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
    <link rel="stylesheet" href="{{ asset('css/questionnaire.css') }}">
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

            // Enhance multiple choice selection experience
            $('.option-input').on('change', function() {
                const questionId = $(this).data('question-id');
                
                // Add a visual feedback on selection
                if ($(this).is(':checked')) {
                    $(this).closest('.option-card').addClass('selected')
                        .find('.option-label').append('<span class="option-ripple"></span>');
                    
                    // Remove ripple effect after animation completes
                    setTimeout(function() {
                        $('.option-ripple').remove();
                    }, 800);
                    
                    // Smoothly update progress bar
                    updateProgress();
                }
            });

            // Make entire option card clickable 
            $('.option-card').on('click', function(e) {
                if (!$(e.target).is('input')) {
                    $(this).find('input').prop('checked', true).change();
                }
            });
        });
    </script>
@endpush
