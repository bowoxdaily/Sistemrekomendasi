@extends('layout.app')

@section('title', 'Pengaturan Logo')

@php
    use App\Models\Setting;
@endphp

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Logo</h4>
                    <p class="card-description">
                        Ubah logo dan favicon website
                    </p>

                    <form id="logo-form" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo Utama</label>
                                    <div class="mb-3">
                                        <img src="{{ $logo }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    <input type="file" class="form-control-file" id="logo" name="logo">
                                    <div class="text-danger mt-1 error-message" id="logo_error"></div>
                                    <small class="form-text text-muted">
                                        Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2MB.
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="logo_alt_text">Alt Text untuk Logo</label>
                                    <input type="text" class="form-control" id="logo_alt_text" name="logo_alt_text" 
                                        value="{{ Setting::get('logo_alt_text', 'Logo Sistem') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <div class="mb-3">
                                        <img src="{{ $favicon }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;" id="favicon-display">
                                    </div>
                                    <input type="file" class="form-control-file" id="favicon" name="favicon">
                                    <div class="text-danger mt-1 error-message" id="favicon_error"></div>
                                    <small class="form-text text-muted">
                                        Format yang didukung: ICO, PNG. Ukuran maksimal: 1MB. Disarankan 16x16 atau 32x32 piksel.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline"></i>
                                    Logo akan ditampilkan di header website dan pada halaman login.
                                    Favicon akan ditampilkan pada tab browser.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="mdi mdi-content-save mr-1" id="save-icon"></i>
                                <span id="save-text">Simpan Perubahan</span>
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light ml-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toastr notifications
        @if(session('success'))
            toastr.success('{{ session('success') }}', 'Sukses');
        @endif
        
        @if(session('error'))
            toastr.error('{{ session('error') }}', 'Error');
        @endif
        
        // AJAX Form Submission
        $('#logo-form').on('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.error-message').text('');
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Change button state to loading
            var $btn = $('#save-btn');
            var $icon = $('#save-icon');
            var $text = $('#save-text');
            
            // Save original state
            var originalIcon = $icon.attr('class');
            var originalText = $text.text();
            
            // Show loading state
            $btn.prop('disabled', true);
            $icon.removeClass().addClass('mdi mdi-loading mdi-spin');
            $text.text('Menyimpan...');
            
            // Send AJAX request - Optimized version
            $.ajax({
                url: _baseURL + 'api/settings/logo',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Simplified success response - reduce animations
                    toastr.success(response.message || 'Logo dan favicon berhasil diperbarui', 'Sukses');
                    
                    // Show simpler loading state without complex overlay
                    $btn.prop('disabled', true);
                    $icon.removeClass().addClass('mdi mdi-check');
                    $text.text('Menyimpan...');
                    
                    // Reload page directly after a short delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000); // Reduced delay to 1 second
                },
                
                error: function(xhr) {
                    // Restore button state
                    $btn.prop('disabled', false);
                    $icon.removeClass().addClass(originalIcon);
                    $text.text(originalText);
                    
                    // Show validation errors or general error message
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.errors) {
                            // Display field-specific errors
                            $.each(response.errors, function(field, messages) {
                                $('#' + field + '_error').text(messages[0]);
                            });
                        } else {
                            toastr.error(response.message || 'Gagal memperbarui logo dan favicon', 'Error');
                        }
                    } catch (e) {
                        toastr.error('Gagal memperbarui logo dan favicon. Silakan coba lagi.', 'Error');
                    }
                }
            });
        });
        
        // Optimize image preview by adding size limits
        $('#logo, #favicon').change(function() {
            const file = this.files[0];
            // Only process files smaller than 2MB to avoid browser slowdown
            if (file && file.size <= 2 * 1024 * 1024) {
                const reader = new FileReader();
                const inputId = $(this).attr('id');
                
                reader.onload = function(e) {
                    if (inputId === 'logo') {
                        $('img[alt="Logo"]').attr('src', e.target.result);
                    } else {
                        $('#favicon-display').attr('src', e.target.result);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Simplified animations to reduce rendering load */
    .success-icon {
        animation: none; /* Removed animation */
    }
    
    /* Remove unused animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush