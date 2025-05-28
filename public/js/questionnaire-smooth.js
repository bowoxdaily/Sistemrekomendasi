$(document).ready(function () {
    const totalQuestions = $(".question-slide").length;
    let currentQuestion = 0;
    let answers = {};
    let completionNotificationShown = false;
    let isTransitioning = false;
    let isMobile = window.innerWidth <= 768;
    let isTouch = "ontouchstart" in window;

    // Initialize
    updateNavigation();
    updateProgress();
    initializeFirstQuestion();
    handleResponsiveFeatures();
    setupEventHandlers();

    // Set up event handlers separately to ensure they're properly bound
    function setupEventHandlers() {
        // Fix for review modal submit button
        $("#submitFromReview").on("click", function () {
            $("#reviewModal").modal("hide");

            // Show confirmation dialog
            showSubmissionConfirmation();
        });

        // Direct form submission
        $("#submitBtn").on("click", function (e) {
            e.preventDefault();
            showSubmissionConfirmation();
        });
    }

    function handleResponsiveFeatures() {
        // Adjust auto-advance timing for mobile
        const autoAdvanceDelay = isMobile ? 1500 : 1200;

        // Add touch-friendly classes
        if (isTouch) {
            $("body").addClass("touch-device");
            $(".option-label, .scale-label").addClass("touch-target");
        }

        // Handle orientation changes
        $(window).on("orientationchange resize", function () {
            setTimeout(function () {
                isMobile = window.innerWidth <= 768;
                adjustLayoutForDevice();
                updateQuestionContainerHeight();
            }, 100);
        });
    }

    function adjustLayoutForDevice() {
        const $container = $(".question-container");

        if (isMobile) {
            // Mobile optimizations
            $container.css("min-height", "300px");

            // Reduce animation complexity on mobile
            $(".option-item, .scale-option").css({
                "animation-duration": "0.3s",
                "transition-duration": "0.3s",
            });
        } else {
            // Desktop optimizations
            $container.css("min-height", "500px");

            // Full animations on desktop
            $(".option-item, .scale-option").css({
                "animation-duration": "0.4s",
                "transition-duration": "0.4s",
            });
        }
    }

    function updateQuestionContainerHeight() {
        const $activeSlide = $(".question-slide.active");
        const $container = $(".question-container");

        if ($activeSlide.length) {
            const slideHeight = $activeSlide.outerHeight();
            $container.css("min-height", slideHeight + "px");
        }
    }

    // Initialize
    updateNavigation();
    updateProgress();
    initializeFirstQuestion();

    function initializeFirstQuestion() {
        $(".question-slide").eq(0).addClass("active");
        animateOptionsIn();
    }

    function animateOptionsIn() {
        const $currentSlide = $(".question-slide.active");
        const $options = $currentSlide.find(".option-item, .scale-option");

        $options.each(function (index) {
            $(this).css({
                "animation-delay": index * 0.1 + 0.1 + "s",
                "animation-fill-mode": "forwards",
            });
        });
    }

    // Question navigation with enhanced animations
    $("#nextBtn").on("click", function () {
        if (isTransitioning) return;

        if (validateCurrentQuestion()) {
            if (currentQuestion < totalQuestions - 1) {
                goToQuestion(currentQuestion + 1, "next");
            } else {
                showCompletionModal();
            }
        }
    });

    $("#prevBtn").on("click", function () {
        if (isTransitioning) return;

        if (currentQuestion > 0) {
            goToQuestion(currentQuestion - 1, "prev");
        }
    });

    // Enhanced question transition
    function goToQuestion(questionIndex, direction = "next") {
        if (
            questionIndex < 0 ||
            questionIndex >= totalQuestions ||
            isTransitioning
        )
            return;

        isTransitioning = true;
        const $currentSlide = $(".question-slide").eq(currentQuestion);
        const $nextSlide = $(".question-slide").eq(questionIndex);

        // Disable navigation during transition
        $("#prevBtn, #nextBtn").prop("disabled", true);

        // Adjust transition timing for mobile
        const transitionDuration = isMobile ? 200 : 300;
        const entranceDelay = isMobile ? 30 : 50;

        // Determine animation direction
        const slideOutClass =
            direction === "next" ? "slide-out-left" : "slide-out-right";

        // Start exit animation
        $currentSlide.removeClass("active").addClass(slideOutClass);

        setTimeout(() => {
            // Hide current slide and prepare next slide
            $currentSlide.hide().removeClass(slideOutClass);

            // Position next slide for entrance
            $nextSlide.show().css({
                transform:
                    direction === "next"
                        ? "translateX(30px)"
                        : "translateX(-30px)",
                opacity: "0",
            });

            // Trigger entrance animation
            setTimeout(() => {
                $nextSlide.addClass("active").css({
                    transform: "translateX(0)",
                    opacity: "1",
                });

                // Update question state
                currentQuestion = questionIndex;
                updateNavigation();
                updateProgress();
                updateQuestionContainerHeight();

                // Animate options in
                setTimeout(() => {
                    animateOptionsIn();
                    isTransitioning = false;

                    // Re-enable navigation
                    updateNavigationButtons();
                }, transitionDuration);
            }, entranceDelay);
        }, transitionDuration);
    }

    function updateNavigationButtons() {
        $("#prevBtn").prop("disabled", currentQuestion === 0);
        $("#nextBtn").prop("disabled", false);
    }

    // Answer change handlers with enhanced feedback
    $(".question-input").on("change input", function () {
        const questionId = $(this).data("question-id");
        let value = "";

        if ($(this).is(":radio")) {
            if ($(this).is(":checked")) {
                value = $(this).val();

                // Add selection animation (reduced on mobile)
                if (!isMobile) {
                    $(this)
                        .closest(".option-label")
                        .addClass("selected-animation");
                    setTimeout(() => {
                        $(this)
                            .closest(".option-label")
                            .removeClass("selected-animation");
                    }, 600);
                }
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

        // Check completion for last question
        if (
            currentQuestion === totalQuestions - 1 &&
            value &&
            !completionNotificationShown
        ) {
            const allAnswered = checkAllQuestionsAnswered();
            if (allAnswered) {
                completionNotificationShown = true;
                setTimeout(
                    () => {
                        showCompletionNotification();
                    },
                    isMobile ? 600 : 800
                );
            }
        }

        // Auto-advance with responsive timing
        if (
            $(this).is(":radio") &&
            $(this).is(":checked") &&
            currentQuestion < totalQuestions - 1
        ) {
            const delay = isMobile ? 1500 : 1200;
            setTimeout(() => {
                if (!isTransitioning) {
                    $("#nextBtn").trigger("click");
                }
            }, delay);
        }
    });

    // Enhanced progress update with smooth animations
    function updateProgress() {
        const answeredCount = Object.keys(answers).length;
        const progressPercentage =
            totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;

        $("#questionProgress")
            .css("width", progressPercentage + "%")
            .attr("aria-valuenow", progressPercentage);

        $("#progressText").text(answeredCount + " / " + totalQuestions);
        $("#answeredCount").text(answeredCount);

        // Update current question indicator
        $("#currentQuestionNumber").text(currentQuestion + 1);
        $("#navCurrentQuestion").text(currentQuestion + 1);
    }

    function updateNavigation() {
        updateNavigationButtons();

        if (currentQuestion === totalQuestions - 1) {
            $("#nextBtn").fadeOut(200, function () {
                $("#submitBtn").fadeIn(200);
            });
        } else {
            $("#submitBtn").fadeOut(200, function () {
                $("#nextBtn").fadeIn(200);
            });
        }
    }

    // Enhanced completion notification
    function showCompletionNotification() {
        const iconSize = isMobile ? "2.5rem" : "3.5rem";
        const fontSize = isMobile ? "1rem" : "1.1rem";

        Swal.fire({
            icon: "success",
            title: "Selamat!",
            html: `
                <div class="text-center">
                    <i class="mdi mdi-trophy text-warning mb-3" style="font-size: ${iconSize}; animation: bounce 1s ease-in-out infinite;"></i>
                    <p class="lead mb-2" style="font-size: ${fontSize};">Anda telah menyelesaikan semua pertanyaan!</p>
                    <p class="text-muted mb-3">Silahkan pilih opsi di bawah ini:</p>
                </div>
            `,
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '<i class="mdi mdi-send me-2"></i>Kirim Jawaban',
            denyButtonText: '<i class="mdi mdi-eye me-2"></i>Tinjau Jawaban',
            cancelButtonText: "Kembali",
            confirmButtonColor: "#28a745",
            denyButtonColor: "#007bff",
            cancelButtonColor: "#6c757d",
            allowOutsideClick: false,
            reverseButtons: true,
            customClass: {
                popup: "completion-notification-popup",
                confirmButton: "btn-choice",
                denyButton: "btn-choice",
                cancelButton: "btn-choice",
            },
            width: isMobile ? "90%" : "500px",
        }).then((result) => {
            if (result.isConfirmed) {
                // User chose to submit answers
                showSubmissionConfirmation();
            } else if (result.isDenied) {
                // User chose to review answers
                showReviewModal();
            }
        });
    }

    // Updated submission confirmation with clear action
    function showSubmissionConfirmation() {
        Swal.fire({
            title: "Konfirmasi Pengiriman",
            html: `
                <div class="text-center">
                    <i class="mdi mdi-help-circle-outline text-warning mb-3" style="font-size: 3rem;"></i>
                    <p>Apakah Anda yakin ingin mengirim jawaban?</p>
                    <p class="text-muted">Jawaban tidak dapat diubah setelah dikirim.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText:
                '<i class="mdi mdi-send me-2"></i>Ya, Kirim Sekarang',
            cancelButtonText: "Batal",
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn-lg",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                actuallySubmitForm();
            }
        });
    }

    // This function actually submits the form
    function actuallySubmitForm() {
        const $submitBtn = $("#submitBtn");

        // Show loading state
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

        // Submit the form after a short delay to ensure the loading state is visible
        setTimeout(() => {
            document.getElementById("questionnaireForm").submit();
        }, 500);
    }

    function showReviewModal() {
        let reviewContent = "";
        let totalAnswered = 0;

        $(".question-slide").each(function (index) {
            const $slide = $(this);
            const $input = $slide.find(".question-input").first();
            const questionId = $input.data("question-id");
            const isAnswered = answers.hasOwnProperty(questionId);

            if (isAnswered) {
                totalAnswered++;
            }

            // Build review content for each question
            reviewContent += `
                <div class="mb-3 p-3 border rounded">
                    <h6>Pertanyaan ${index + 1}</h6>
                    <div class="fw-bold mb-2">${
                        $slide.find(".question-text").html() ||
                        "Tidak ada pertanyaan"
                    }</div>
                    <div class="mb-2">Jawaban Anda: <span class="text-success">${
                        isAnswered ? answers[questionId] : "Belum dijawab"
                    }</span></div>
                    <div class="mb-2">Status: ${
                        isAnswered
                            ? '<span class="badge bg-success">Dijawab</span>'
                            : '<span class="badge bg-danger">Belum dijawab</span>'
                    }</div>
                </div>
            `;
        });

        const summaryContent = `
            <div class="alert alert-info mb-4">
                <h6><i class="mdi mdi-information me-2"></i>Ringkasan Jawaban</h6>
                <p class="mb-0">Total dijawab: <strong>${totalAnswered}</strong> dari <strong>${totalQuestions}</strong> pertanyaan</p>
            </div>
        `;

        // Clear any previous content and show new content
        $("#reviewContent").html(summaryContent + reviewContent);
        $("#reviewModal").modal("show");
    }

    // Override the questionnaireForm submit event to use our confirmation flow
    $("#questionnaireForm").on("submit", function (e) {
        e.preventDefault();
        if (validateAllQuestions()) {
            showSubmissionConfirmation();
        }
    });

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

    function checkAllQuestionsAnswered() {
        let allAnswered = true;

        $(".question-slide").each(function () {
            const $slide = $(this);
            const $input = $slide.find(".question-input").first();
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
                allAnswered = false;
                return false;
            }
        });

        return allAnswered;
    }

    function showValidationError(questionId, message) {
        const $input = $(`[data-question-id="${questionId}"]`);
        const $error = $(`#error_${questionId}`);

        $input.addClass("is-invalid");
        $error.removeClass("d-none").text(message);
    }

    function clearValidationError(questionId) {
        const $input = $(`[data-question-id="${questionId}"]`);
        const $error = $(`#error_${questionId}`);

        $input.removeClass("is-invalid");
        $error.addClass("d-none");
    }

    // Form submission and other handlers remain the same...
    $("#questionnaireForm").on("submit", function (e) {
        e.preventDefault();
        if (validateAllQuestions()) {
            showSubmissionConfirmation();
        }
    });

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
        const $submitBtn = $("#submitBtn");

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

        document.getElementById("questionnaireForm").submit();
    }

    // Keyboard navigation
    $(document).on("keydown", function (e) {
        // Disable keyboard navigation on mobile/touch devices
        if (isTouch || isMobile) return;

        if (
            !$(".modal").hasClass("show") &&
            !$("input, textarea").is(":focus") &&
            !isTransitioning
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

    // Touch gesture support for mobile
    if (isTouch) {
        let startX = 0;
        let startY = 0;

        $(".question-container").on("touchstart", function (e) {
            if (isTransitioning) return;
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        $(".question-container").on("touchend", function (e) {
            if (isTransitioning) return;

            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            const deltaX = endX - startX;
            const deltaY = endY - startY;

            // Check if it's a horizontal swipe (not vertical scroll)
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0 && currentQuestion > 0) {
                    // Swipe right - previous question
                    $("#prevBtn").trigger("click");
                } else if (deltaX < 0 && currentQuestion < totalQuestions - 1) {
                    // Swipe left - next question
                    if (validateCurrentQuestion()) {
                        $("#nextBtn").trigger("click");
                    }
                }
            }
        });
    }

    // Character count for textareas
    $("textarea.question-input").on("input", function () {
        const questionId = $(this).data("question-id");
        const charCount = $(this).val().length;
        $("#charCount_" + questionId).text(charCount);
    });
});
