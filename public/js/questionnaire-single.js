$(document).ready(function () {
    const totalQuestions = $(".question-slide").length;
    let currentQuestion = 0;
    let answers = {};
    let completionNotificationShown = false;

    // Initialize
    updateNavigation();
    updateProgress();

    // Question navigation
    $("#nextBtn").on("click", function () {
        if (validateCurrentQuestion()) {
            if (currentQuestion < totalQuestions - 1) {
                goToQuestion(currentQuestion + 1);
            } else {
                showCompletionModal();
            }
        }
    });

    $("#prevBtn").on("click", function () {
        if (currentQuestion > 0) {
            goToQuestion(currentQuestion - 1);
        }
    });

    // Answer change handlers
    $(".question-input").on("change input", function () {
        const questionId = $(this).data("question-id");
        let value = "";

        if ($(this).is(":radio")) {
            if ($(this).is(":checked")) {
                value = $(this).val();
            }
        } else if ($(this).is("textarea")) {
            value = $(this).val().trim();
        }

        if (value) {
            answers[questionId] = value;
        } else {
            delete answers[questionId];
        }

        updateProgress();
        clearValidationError(questionId);

        // Check if this is the last question and all questions are answered
        if (
            currentQuestion === totalQuestions - 1 &&
            value &&
            !completionNotificationShown
        ) {
            // Check if all questions are now answered
            const allAnswered = checkAllQuestionsAnswered();
            if (allAnswered) {
                completionNotificationShown = true;
                // Show completion notification after a short delay
                setTimeout(() => {
                    showCompletionNotification();
                }, 500);
            }
        }

        // Auto-advance for radio buttons (optional) - but not on last question
        if (
            $(this).is(":radio") &&
            $(this).is(":checked") &&
            currentQuestion < totalQuestions - 1
        ) {
            setTimeout(() => {
                $("#nextBtn").trigger("click");
            }, 800);
        }
    });

    // Character count for textareas
    $("textarea.question-input").on("input", function () {
        const questionId = $(this).data("question-id");
        const charCount = $(this).val().length;
        $("#charCount_" + questionId).text(charCount);
    });

    // Form submission
    $("#questionnaireForm").on("submit", function (e) {
        e.preventDefault();

        if (validateAllQuestions()) {
            showSubmissionConfirmation();
        }
    });

    // Completion modal handlers
    $("#submitFromCompletion").on("click", function () {
        $("#completionModal").modal("hide");
        showSubmissionConfirmation();
    });

    $("#reviewAnswersBtn").on("click", function () {
        $("#completionModal").modal("hide");
        showReviewModal();
    });

    // Functions
    function goToQuestion(questionIndex) {
        if (questionIndex < 0 || questionIndex >= totalQuestions) return;

        const $currentSlide = $(".question-slide").eq(currentQuestion);
        const $nextSlide = $(".question-slide").eq(questionIndex);

        // Add animation classes
        $currentSlide.addClass("slide-out");

        setTimeout(() => {
            $currentSlide.hide().removeClass("slide-out");
            $nextSlide.show().addClass("slide-in");

            setTimeout(() => {
                $nextSlide.removeClass("slide-in");
            }, 300);
        }, 150);

        currentQuestion = questionIndex;
        updateNavigation();
        updateProgress();
    }

    function updateNavigation() {
        // Update button states
        $("#prevBtn").prop("disabled", currentQuestion === 0);

        if (currentQuestion === totalQuestions - 1) {
            $("#nextBtn").hide();
            $("#submitBtn").show();
        } else {
            $("#nextBtn").show();
            $("#submitBtn").hide();
        }

        // Update question numbers
        $("#currentQuestionNumber").text(currentQuestion + 1);
        $("#navCurrentQuestion").text(currentQuestion + 1);
    }

    function updateProgress() {
        const answeredCount = Object.keys(answers).length;
        const progressPercentage =
            totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;

        $("#questionProgress")
            .css("width", progressPercentage + "%")
            .attr("aria-valuenow", progressPercentage);

        $("#progressText").text(answeredCount + " / " + totalQuestions);
        $("#answeredCount").text(answeredCount);

        // Update progress text based on current question position
        const currentProgressPercentage =
            totalQuestions > 0
                ? ((currentQuestion + 1) / totalQuestions) * 100
                : 0;
        $("#progressText").text(currentQuestion + 1 + " / " + totalQuestions);
    }

    function validateCurrentQuestion() {
        const $currentSlide = $(".question-slide").eq(currentQuestion);
        const $input = $currentSlide.find(".question-input").first();
        const questionId = $input.data("question-id");
        const isRequired = $input.prop("required");

        if (!isRequired) return true;

        let isAnswered = false;

        if ($input.is(":radio")) {
            isAnswered =
                $currentSlide.find('input[type="radio"]:checked').length > 0;
        } else if ($input.is("textarea")) {
            isAnswered = $input.val().trim() !== "";
        }

        if (!isAnswered) {
            showValidationError(questionId, "Pertanyaan ini wajib dijawab.");
            return false;
        }

        return true;
    }

    function validateAllQuestions() {
        let isValid = true;
        let firstInvalidQuestion = null;

        $(".question-slide").each(function (index) {
            const $slide = $(this);
            const $input = $slide.find(".question-input").first();
            const questionId = $input.data("question-id");
            const isRequired = $input.prop("required");

            if (!isRequired) return true;

            let isAnswered = false;

            if ($input.is(":radio")) {
                isAnswered =
                    $slide.find('input[type="radio"]:checked').length > 0;
            } else if ($input.is("textarea")) {
                isAnswered = $input.val().trim() !== "";
            }

            if (!isAnswered) {
                isValid = false;
                if (firstInvalidQuestion === null) {
                    firstInvalidQuestion = index;
                }
            }
        });

        if (!isValid && firstInvalidQuestion !== null) {
            goToQuestion(firstInvalidQuestion);
            setTimeout(() => {
                const questionId = $(".question-slide")
                    .eq(firstInvalidQuestion)
                    .find(".question-input")
                    .first()
                    .data("question-id");
                showValidationError(
                    questionId,
                    "Pertanyaan ini wajib dijawab."
                );
            }, 300);
        }

        return isValid;
    }

    function checkAllQuestionsAnswered() {
        let allAnswered = true;

        $(".question-slide").each(function () {
            const $slide = $(this);
            const $input = $slide.find(".question-input").first();
            const isRequired = $input.prop("required");

            if (!isRequired) return true; // Skip non-required questions

            let isAnswered = false;

            if ($input.is(":radio")) {
                isAnswered =
                    $slide.find('input[type="radio"]:checked').length > 0;
            } else if ($input.is("textarea")) {
                isAnswered = $input.val().trim() !== "";
            }

            if (!isAnswered) {
                allAnswered = false;
                return false; // Break the loop
            }
        });

        return allAnswered;
    }

    function showValidationError(questionId, message) {
        const $input = $(`[data-question-id="${questionId}"]`);
        const $error = $(`#error_${questionId}`);

        $input.addClass("is-invalid");
        $error.removeClass("d-none").text(message);

        // Simple shake effect without external animation library
        const $card = $input.closest(".question-card");
        $card.css("animation", "shake 0.5s ease-in-out");
        setTimeout(() => {
            $card.css("animation", "");
        }, 500);
    }

    function clearValidationError(questionId) {
        const $input = $(`[data-question-id="${questionId}"]`);
        const $error = $(`#error_${questionId}`);

        $input.removeClass("is-invalid");
        $error.addClass("d-none");
    }

    function showCompletionNotification() {
        // Show SweetAlert notification
        Swal.fire({
            icon: "success",
            title: "Selamat!",
            html: `
                <div class="text-center">
                    <i class="mdi mdi-trophy text-warning mb-3" style="font-size: 3rem;"></i>
                    <p class="lead mb-2">Anda telah menyelesaikan semua pertanyaan!</p>
                    <p class="text-muted">Klik tombol "Kirim Jawaban" untuk menyelesaikan kuesioner ini.</p>
                </div>
            `,
            confirmButtonText:
                '<i class="mdi mdi-check me-2"></i>Baik, Mengerti!',
            confirmButtonColor: "#28a745",
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: "completion-notification-popup",
                confirmButton: "btn-lg",
            },
            showClass: {
                popup: "animate__animated animate__bounceIn",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOut",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Add visual feedback to submit button
                updateSubmitButton();
            }
        });
    }

    function updateSubmitButton() {
        const $submitBtn = $("#submitBtn");
        $submitBtn
            .removeClass("btn-success")
            .addClass("btn-success")
            .html(
                '<i class="mdi mdi-check-circle me-2"></i>Kirim Jawaban - Semua Selesai!'
            )
            .addClass("btn-pulse");

        // Remove pulse animation after 3 seconds
        setTimeout(() => {
            $submitBtn.removeClass("btn-pulse");
        }, 3000);

        // Scroll to submit button smoothly
        $("html, body").animate(
            {
                scrollTop: $submitBtn.offset().top - 100,
            },
            500
        );
    }

    function showCompletionModal() {
        const answeredCount = Object.keys(answers).length;
        $("#answeredCount").text(answeredCount);
        $("#completionModal").modal("show");
    }

    function showSubmissionConfirmation() {
        Swal.fire({
            title: "Konfirmasi Pengiriman",
            text: "Apakah Anda yakin ingin mengirim jawaban? Jawaban tidak dapat diubah setelah dikirim.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: "Batal",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    }

    function submitForm() {
        const $submitBtn = $("#submitBtn, #submitFromCompletion");

        $submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim jawaban...'
            );

        Swal.fire({
            title: "Mengirim jawaban...",
            text: "Mohon tunggu sebentar",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Submit the form
        document.getElementById("questionnaireForm").submit();
    }

    function showReviewModal() {
        let reviewContent = "";
        let totalAnswered = 0;

        $(".question-slide").each(function (index) {
            const $slide = $(this);
            const questionText = $slide
                .find(".question-title")
                .clone()
                .children()
                .remove()
                .end()
                .text()
                .trim();
            const $radio = $slide.find('input[type="radio"]:checked');
            const $textarea = $slide.find("textarea");

            let answer = "";
            let isEmpty = false;

            if ($radio.length > 0) {
                answer = $radio.val();
                totalAnswered++;
            } else if ($textarea.length > 0) {
                answer = $textarea.val().trim();
                if (answer === "") {
                    isEmpty = true;
                    answer = '<span class="text-muted">Belum dijawab</span>';
                } else {
                    totalAnswered++;
                }
            } else {
                isEmpty = true;
                answer = '<span class="text-muted">Belum dijawab</span>';
            }

            reviewContent += `
                <div class="review-item mb-3 p-3 border rounded ${
                    isEmpty ? "bg-light" : ""
                }">
                    <div class="review-question fw-bold mb-2">${
                        index + 1
                    }. ${questionText}</div>
                    <div class="review-answer">${answer}</div>
                </div>
            `;
        });

        const summaryContent = `
            <div class="alert alert-info mb-4">
                <h6><i class="mdi mdi-information me-2"></i>Ringkasan Jawaban</h6>
                <p class="mb-0">Total dijawab: <strong>${totalAnswered}</strong> dari <strong>${totalQuestions}</strong> pertanyaan</p>
            </div>
        `;

        Swal.fire({
            title: "Tinjauan Jawaban",
            html: summaryContent + reviewContent,
            width: "80%",
            showCancelButton: true,
            confirmButtonText: '<i class="mdi mdi-send me-2"></i>Kirim Jawaban',
            cancelButtonText: "Tutup",
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            customClass: {
                htmlContainer: "text-start",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    }

    // Keyboard navigation
    $(document).on("keydown", function (e) {
        // Only allow keyboard navigation if no modal is open and no input is focused
        if (
            !$(".modal").hasClass("show") &&
            !$("input, textarea").is(":focus")
        ) {
            if (
                e.key === "ArrowRight" &&
                currentQuestion < totalQuestions - 1
            ) {
                $("#nextBtn").trigger("click");
            } else if (e.key === "ArrowLeft" && currentQuestion > 0) {
                $("#prevBtn").trigger("click");
            }
        }
    });

    // Initial setup
    hideQuestionnaireDescription();

    function hideQuestionnaireDescription() {
        // Hide description after 5 seconds
        setTimeout(() => {
            $("#questionnaire-description").fadeOut();
        }, 5000);
    }
});
