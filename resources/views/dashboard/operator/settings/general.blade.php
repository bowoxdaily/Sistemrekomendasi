@extends('layout.app')

@section('title', 'Pengaturan Umum')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Umum</h4>
                    <p class="card-description">
                        Konfigurasi dasar website
                    </p>

                    <form id="general-settings-form">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="site_name">Nama Website</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                value="{{ $settings['site_name'] ?? 'Sistem Tracer Study & Rekomendasi Karir' }}" required>
                            <div class="text-danger mt-1 error-message" id="site_name_error"></div>
                            <small class="form-text text-muted">Nama website yang akan ditampilkan pada judul browser dan header.</small>
                        </div>

                        <div class="form-group">
                            <label for="site_description">Deskripsi Website</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? 'Sistem Pelacakan Alumni dan Rekomendasi Karir untuk SMK' }}</textarea>
                            <div class="text-danger mt-1 error-message" id="site_description_error"></div>
                            <small class="form-text text-muted">Deskripsi singkat tentang website ini.</small>
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                value="{{ $settings['meta_keywords'] ?? 'tracer study, alumni, karir, smk' }}">
                            <div class="text-danger mt-1 error-message" id="meta_keywords_error"></div>
                            <small class="form-text text-muted">Kata kunci untuk optimasi mesin pencari, pisahkan dengan koma.</small>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary mr-2" id="save-btn">
                                <i class="mdi mdi-content-save mr-1" id="save-icon"></i> 
                                <span id="save-text">Simpan Perubahan</span>
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
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
        $('#general-settings-form').on('submit', function(e) {
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
            
            // Send AJAX request
            $.ajax({
                url: _baseURL + 'api/settings/general',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success message with spinner for reload indication
                    $btn.prop('disabled', true);
                    $icon.removeClass().addClass('mdi mdi-check success-icon');
                    $text.html('Berhasil! <span class="ml-1"><i class="mdi mdi-refresh mdi-spin reload-spinner"></i></span>');
                    
                    // Add visual overlay with loading animation
                    const overlay = $('<div id="success-overlay"></div>').css({
                        'position': 'fixed',
                        'top': 0,
                        'left': 0,
                        'width': '100%',
                        'height': '100%',
                        'background-color': 'rgba(255,255,255,0.8)',
                        'z-index': 9999,
                        'display': 'flex',
                        'justify-content': 'center',
                        'align-items': 'center',
                        'flex-direction': 'column'
                    });
                    
                    const spinner = $('<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>');
                    const message = $('<p class="mt-3 h5 text-primary">Pengaturan berhasil disimpan!</p>');
                    const submessage = $('<p class="text-muted">Halaman akan dimuat ulang dalam beberapa saat...</p>');
                    
                    overlay.append(spinner, message, submessage);
                    $('body').append(overlay);
                    
                    // Create animation on success
                    overlay.hide().fadeIn(300);
                    
                    // Show success message
                    toastr.success(response.message || 'Pengaturan umum berhasil diperbarui', 'Sukses');
                    
                    // Wait before reload
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000); // 2 seconds delay for better user experience
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
                            toastr.error(response.message || 'Gagal memperbarui pengaturan', 'Error');
                        }
                    } catch (e) {
                        toastr.error('Gagal memperbarui pengaturan. Silakan coba lagi.', 'Error');
                    }
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Add animation for success check icon */
    .success-icon {
        animation: scaleCheck 0.5s ease-in-out;
    }
    
    /* Add animation for reload spinner */
    .reload-spinner {
        color: #28a745;
    }
    
    @keyframes scaleCheck {
        0% { transform: scale(0); }
        50% { transform: scale(1.5); }
        100% { transform: scale(1); }
    }
    
    /* Success overlay fade-in animation */
    #success-overlay {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Error message styling */
    .error-message {
        font-size: 0.875rem;
    }
</style>
@endpush
