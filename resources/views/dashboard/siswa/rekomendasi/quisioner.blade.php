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
                            <div class="alert alert-info mb-4" id="questionnaire-description">
                                <i class="mdi mdi-information me-2"></i>
                                {{ $questionnaire->description }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-4">
                                <i class="mdi mdi-alert-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success mb-4">
                                <i class="mdi mdi-check-circle me-2"></i>
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
                            <div id="questionsContainer">
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
                                                                <div class="options-grid">
                                                                    @foreach ($question->options as $optionIndex => $option)
                                                                        @php
                                                                            $optionText = '';
                                                                            if (
                                                                                is_array($option) &&
                                                                                isset($option['text'])
                                                                            ) {
                                                                                $optionText = $option['text'];
                                                                            } elseif (is_string($option)) {
                                                                                $optionText = $option;
                                                                            }
                                                                        @endphp

                                                                        @if (!empty($optionText))
                                                                            <div class="option-item">
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
                                                                                    <div class="option-text">{{ $optionText }}
                                                                                    </div>
                                                                                    <div class="option-check">
                                                                                        <i class="mdi mdi-check-circle"></i>
                                                                                    </div>
                                                                                </label>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="alert alert-warning">
                                                                    <i class="mdi mdi-alert-triangle me-2"></i>
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
                                                                            value="{{ $i }}" required
                                                                            data-question-id="{{ $question->id }}">
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

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>
                                        <i class="mdi mdi-chevron-left me-2"></i>Sebelumnya
                                    </button>

                                    <div class="navigation-info">
                                        <span class="text-muted">
                                            Pertanyaan <span id="navCurrentQuestion">1</span> dari
                                            {{ $questionnaire->questions->count() }}
                                        </span>
                                    </div>

                                    <button type="button" class="btn btn-primary" id="nextBtn">
                                        Selanjutnya<i class="mdi mdi-chevron-right ms-2"></i>
                                    </button>

                                    <button type="submit" class="btn btn-success" id="submitBtn"
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

    <!-- Completion Modal -->
    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="completionModalLabel">
                        <i class="mdi mdi-check-circle me-2"></i>Kuesioner Selesai!
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="completion-icon mb-3">
                            <i class="mdi mdi-trophy text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-success mb-3">Selamat!</h4>
                        <p class="lead">Anda telah menyelesaikan semua pertanyaan dalam kuesioner ini.</p>
                        <p class="text-muted">Silakan tinjau jawaban Anda sebelum mengirim, atau langsung kirim jika sudah
                            yakin.</p>
                    </div>

                    <div class="completion-summary mt-4">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="summary-item">
                                    <h5 class="text-primary">{{ $questionnaire->questions->count() }}</h5>
                                    <small class="text-muted">Total Pertanyaan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-item">
                                    <h5 class="text-success" id="answeredCount">0</h5>
                                    <small class="text-muted">Telah Dijawab</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-item">
                                    <h5 class="text-info">100%</h5>
                                    <small class="text-muted">Progres</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="reviewAnswersBtn">
                        <i class="mdi mdi-eye me-2"></i>Tinjau Jawaban
                    </button>
                    <button type="button" class="btn btn-success" id="submitFromCompletion">
                        <i class="mdi mdi-send me-2"></i>Kirim Jawaban
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/questionnaire-single.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/questionnaire-single.js') }}"></script>
@endpush
