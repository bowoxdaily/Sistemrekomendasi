{{-- resources/views/auth/passwords/reset.blade.php --}}
@php
    use App\Models\Setting;
    
    // Get custom logo with fallback to default
    $mainLogo = Setting::get('logo_path', asset('admin/images/logo.svg'));
    $logoAlt = Setting::get('logo_alt_text', 'Logo Sistem');
    $siteTitle = Setting::get('site_name', 'Reset Password');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteTitle }} - Reset Password</title>
    
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
    
    <link rel="shortcut icon" href="{{ Setting::get('favicon_path', asset('admin/images/favicon.png')) }}" />
    
    <style>
        .brand-logo {
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .brand-logo img {
            transition: all 0.3s ease;
            max-width: 100%;
            max-height: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        @media (max-width: 576px) {
            .brand-logo {
                min-height: 100px;
            }
            
            .brand-logo img {
                max-height: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center mb-4">
                                <img src="{{ $mainLogo }}" alt="{{ $logoAlt }}" class="img-fluid">
                            </div>
                            <h4>Reset Password</h4>
                            <h6 class="font-weight-light">Enter your new password</h6>

                            <div id="alert-container"></div>

                            <form class="pt-3" id="reset-password-form">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                
                                <div class="form-group">
                                    <input type="email" name="email"
                                        class="form-control form-control-lg"
                                        id="email" placeholder="Email" value="{{ $email ?? old('email') }}" required autofocus>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <input type="password" name="password"
                                        class="form-control form-control-lg"
                                        id="password" placeholder="New Password" required>
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <input type="password" name="password_confirmation"
                                        class="form-control form-control-lg"
                                        id="password_confirmation" placeholder="Confirm Password" required>
                                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" id="reset-button"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        Reset Password
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
            $('#reset-password-form').on('submit', function(e) {
                e.preventDefault();

                // Reset previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#alert-container').html('');

                // Disable button during submission
                $('#reset-button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                $('#reset-button').prop('disabled', true);

                $.ajax({
                    url: "{{ route('password.update') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message);
                            
                            // Display success alert
                            $('#alert-container').html('<div class="alert alert-success">' + response.message + '<br>You will be redirected to login page shortly.</div>');
                            
                            // Redirect to login page after 3 seconds
                            setTimeout(function() {
                                window.location.href = "{{ route('login') }}";
                            }, 3000);
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
                        $('#reset-button').html('Reset Password');
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