@extends('layout.app')

@section('title', 'Kuis - ' . $questionnaire->title)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $questionnaire->title }}</h4>
                            <span class="badge bg-light text-primary">
                                <span id="currentQuestionNumber">1</span> / {{ $questionnaire->questions->count() }}
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

                            @if (isset($isRetake) && $isRetake)
                                <input type="hidden" name="is_retake" value="1">
                                <div class="alert alert-info mb-4">
                                    <i class="mdi mdi-refresh me-2"></i>
                                    Anda sedang mengisi ulang kuesioner. Jawaban baru Anda akan menggantikan rekomendasi
                                    sebelumnya.
                                </div>
                            @endif

                            <!-- Question Container -->
                            <div id="questionsContainer" class="question-container">
                                @foreach ($questionnaire->questions as $index => $question)
                                    <div class="question-slide" data-question-index="{{ $index }}"
                                        style="{{ $index === 0 ? 'display: block;' : 'display: none;' }}">
                                        <div class="question-card">
                                            <div class="question-header mb-4">
                                                <h5 class="question-title">
                                                    <span class="question-number">{{ $loop->iteration }}.</span>
                                                    {{ $question->question_text }}
                                                    @if (isset($question->is_required) && $question->is_required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <div class="question-content">
                                                @switch($question->question_type)
                                                    @case('multiple_choice')
                                                        <div class="multiple-choice-container">
                                                            @php
                                                                $hasOptions =
                                                                    is_array($question->options) &&
                                                                    count($question->options) > 0;
                                                            @endphp

                                                            @if ($hasOptions)
                                                                <div class="row g-3">
                                                                    @foreach ($question->options as $optionIndex => $option)
                                                                        @php
                                                                            // Determine option text and value correctly
                                                                            $optionText = '';
                                                                            $optionValue = '';

                                                                            if (
                                                                                is_array($option) &&
                                                                                isset($option['text'])
                                                                            ) {
                                                                                $optionText = $option['text'];
                                                                                $optionValue =
                                                                                    $option['value'] ?? $optionText;
                                                                            } elseif (is_string($option)) {
                                                                                $optionText = $option;
                                                                                $optionValue = $option;
                                                                            }
                                                                        @endphp

                                                                        @if (!empty($optionText))
                                                                            <div class="col-md-6 mb-3">
                                                                                <div class="option-card">
                                                                                    <input class="option-input question-input"
                                                                                        type="radio"
                                                                                        name="answers[{{ $question->id }}]"
                                                                                        id="question_{{ $question->id }}_option_{{ $optionIndex }}"
                                                                                        value="{{ $optionText }}"
                                                                                        {{ $question->is_required ?? true ? 'required' : '' }}
                                                                                        data-question-id="{{ $question->id }}">
                                                                                    <label class="option-label"
                                                                                        for="question_{{ $question->id }}_option_{{ $optionIndex }}">
                                                                                        <div class="option-icon">
                                                                                            {{ chr(65 + $optionIndex) }}</div>
                                                                                        <div class="option-text">
                                                                                            {{ $optionText }}
                                                                                        </div>
                                                                                        <div class="option-check">
                                                                                            <i class="mdi mdi-check-circle"></i>
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
                                                                                        <i class="mdi mdi-emoticon-sad"></i>
                                                                                    @break

                                                                                    @case(2)
                                                                                        <i class="mdi mdi-emoticon-neutral"></i>
                                                                                    @break

                                                                                    @case(3)
                                                                                        <i class="mdi mdi-emoticon"></i>
                                                                                    @break

                                                                                    @case(4)
                                                                                        <i class="mdi mdi-emoticon-happy"></i>
                                                                                    @break

                                                                                    @case(5)
                                                                                        <i class="mdi mdi-emoticon-excited"></i>
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
                                                            <i class="mdi mdi-alert-triangle me-2"></i>
                                                            Tipe pertanyaan tidak didukung: {{ $question->question_type }}
                                                        </div>
                                                @endswitch
                                            </div>

                                            <div class="invalid-feedback d-none" id="error_{{ $question->id }}">
                                                Pertanyaan ini wajib dijawab.
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Navigation Controls -->
                            <div class="navigation-controls mt-4">
                                <!-- Progress Bar -->
                                <div class="card mb-3">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Progres Jawaban</span>
                                            <span id="progressText" class="badge bg-primary">1 /
                                                {{ $questionnaire->questions->count() }}</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div id="questionProgress"
                                                class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                style="width: {{ 100 / $questionnaire->questions->count() }}%"
                                                aria-valuenow="{{ 100 / $questionnaire->questions->count() }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-nav" id="prevBtn"
                                        disabled>
                                        <i class="mdi mdi-chevron-left me-2"></i>Sebelumnya
                                    </button>

                                    <div class="navigation-info">
                                        <span class="text-muted">
                                            Pertanyaan <span id="navCurrentQuestion">1</span> dari
                                            {{ $questionnaire->questions->count() }}
                                        </span>
                                    </div>

                                    <button type="button" class="btn btn-primary btn-nav" id="nextBtn">
                                        Selanjutnya<i class="mdi mdi-chevron-right ms-2"></i>
                                    </button>

                                    <button type="submit" class="btn btn-success btn-nav" id="submitBtn"
                                        style="display: none;">
                                        <i class="mdi mdi-send me-2"></i>Kirim Jawaban
                                    </button>
                                </div>
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
    <link rel="stylesheet" href="{{ asset('css/questionnaire-smooth.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/questionnaire-smooth.js') }}"></script>
@endpush
