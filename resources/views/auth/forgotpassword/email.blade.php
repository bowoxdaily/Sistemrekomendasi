{{-- resources/views/auth/passwords/email.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password</title>
    
    <!-- jQuery harus dimuat terlebih dahulu -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/vertical-layout-light/style.css') }}">
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{ asset('admin/images/logo.svg') }}" alt="logo">
                            </div>
                            <h4>Forgot Password</h4>
                            <h6 class="font-weight-light">Enter your email to reset your password</h6>

                            <div id="alert-container"></div>

                            <form class="pt-3" id="forgot-password-form">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email"
                                        class="form-control form-control-lg"
                                        id="email" placeholder="Email" required autofocus>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" id="reset-button"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        Send Password Reset Link
                                    </button>
                                </div>
                                <div class="text-center mt-4 font-weight-light">
                                    Remember your password? <a href="{{ route('login') }}" class="text-primary">Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>
    
    <!-- inject:js -->
    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/todolist.js') }}"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Konfigurasi Toastr
        toastr.options = {
            closeButton: true,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };

        $(document).ready(function() {
            // Set up CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle form submission with AJAX
            $('#forgot-password-form').on('submit', function(e) {
                e.preventDefault();

                // Reset previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#alert-container').html('');

                // Disable button during submission
                $('#reset-button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                $('#reset-button').prop('disabled', true);

                $.ajax({
                    url: "{{ route('password.email') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message);
                            
                            // Reset form
                            $('#forgot-password-form')[0].reset();
                            
                            // Show success message in container
                            $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            
                            // Display validation errors
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            
                            toastr.error(xhr.responseJSON.message || 'Validation failed');
                        } else {
                            toastr.error('Error: ' + xhr.responseText);
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $('#reset-button').html('Send Password Reset Link');
                        $('#reset-button').prop('disabled', false);
                    }
                });
            });
        });

        // Menampilkan pesan Toastr dari session
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if (session('status'))
            toastr.info('{{ session('status') }}');
        @endif
    </script>
</body>

</html>