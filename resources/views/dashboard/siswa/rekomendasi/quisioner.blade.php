@extends('layout.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 5px;">
                    <div id="questionProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ $questionnaire->title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i>
                            {{ $questionnaire->description }}
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.questionnaire.submit') }}" id="questionnaireForm">
                            @csrf
                            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

                            @foreach ($questionnaire->questions as $question)
                                <div class="question-card mb-4 p-4 border rounded">
                                    <h5 class="border-bottom pb-2">
                                        <span class="badge bg-primary me-2">{{ $loop->iteration }}</span>
                                        {{ $question->question_text }}
                                    </h5>

                                    <div class="mt-3">
                                        @switch($question->question_type)
                                            @case('multiple_choice')
                                                <div class="row g-3">
                                                    @foreach ($question->options ?? [] as $option)
                                                        <div class="col-md-6">
                                                            <div class="form-check custom-radio">
                                                                <input class="form-check-input question-input" type="radio"
                                                                    name="answers[{{ $question->id }}]"
                                                                    id="question_{{ $question->id }}_option_{{ $loop->index }}"
                                                                    value="{{ is_array($option) ? $option['text'] ?? ($option['value'] ?? '') : $option }}"
                                                                    required>
                                                                <label class="form-check-label w-100"
                                                                    for="question_{{ $question->id }}_option_{{ $loop->index }}">
                                                                    <div class="p-3 rounded border">
                                                                        {{ is_array($option) ? $option['text'] ?? ($option['value'] ?? '') : $option }}
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @break

                                            @case('scale')
                                                <div class="scale-container text-center">
                                                    <div class="btn-group" role="group">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <input type="radio" class="btn-check question-input"
                                                                name="answers[{{ $question->id }}]"
                                                                id="question_{{ $question->id }}_scale_{{ $i }}"
                                                                value="{{ $i }}" required>
                                                            <label class="btn btn-outline-primary px-4 py-2"
                                                                for="question_{{ $question->id }}_scale_{{ $i }}">
                                                                {{ $i }}
                                                            </label>
                                                        @endfor
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2 text-muted">
                                                        <small>Sangat Rendah</small>
                                                        <small>Sangat Tinggi</small>
                                                    </div>
                                                </div>
                                            @break

                                            @case('text')
                                                <div class="form-group">
                                                    <textarea class="form-control question-input" name="answers[{{ $question->id }}]" rows="3" required></textarea>
                                                </div>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4 d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Kirim Jawaban
                                </button>
                            </div>
                        </form>

                        @if (session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .question-card {
            transition: all 0.3s ease;
        }

        .question-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .custom-radio .form-check-label {
            cursor: pointer;
        }

        .custom-radio .form-check-input:checked+.form-check-label div {
            background-color: #e9ecef;
            border-color: #0d6efd;
        }

        .scale-container .btn-group {
            width: 100%;
            max-width: 400px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Progress bar update
            function updateProgress() {
                const totalQuestions = {{ $questionnaire->questions->count() }};
                let answeredQuestions = 0;

                $('.question-card').each(function() {
                    const $card = $(this);
                    const $radio = $card.find('input[type="radio"]:checked');
                    const $textarea = $card.find('textarea');

                    if ($radio.length > 0 || ($textarea.length > 0 && $textarea.val().trim() !== '')) {
                        answeredQuestions++;
                    }
                });

                const progress = (answeredQuestions / totalQuestions) * 100;
                $('#questionProgress').css('width', progress + '%');
            }

            // Monitor input changes
            $('.question-input').on('change keyup', updateProgress);

            // Form validation and submission
            $('#questionnaireForm').on('submit', function(e) {
                const $form = $(this);
                const $submitBtn = $('#submitBtn');
                let hasEmptyFields = false;

                // Check each question card
                $('.question-card').each(function() {
                    const $card = $(this);
                    const $radio = $card.find('input[type="radio"]:checked');
                    const $textarea = $card.find('textarea');

                    if (($radio.length === 0 && !$textarea.length) ||
                        ($textarea.length > 0 && $textarea.val().trim() === '')) {
                        hasEmptyFields = true;
                        return false; // break the loop
                    }
                });

                if (hasEmptyFields) {
                    e.preventDefault();
                    alert('Mohon jawab semua pertanyaan sebelum mengirim.');
                    return false;
                }

                // Disable button and show loading state
                $submitBtn
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');

                // Allow form submission
                return true;
            });
        });
    </script>
@endpush
