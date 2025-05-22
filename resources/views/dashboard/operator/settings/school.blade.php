@extends('layout.app')

@section('title', 'Pengaturan Sekolah')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Informasi Sekolah</h4>
                    <p class="card-description">
                        Informasi tentang sekolah yang akan ditampilkan di website
                    </p>

                    <form id="school-settings-form">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="school_name">Nama Sekolah</label>
                            <input type="text" class="form-control" id="school_name" name="school_name" 
                                value="{{ $settings['school_name'] ?? 'SMKN 1 Terisi' }}" required>
                            <div class="text-danger mt-1 error-message" id="school_name_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="school_address">Alamat Sekolah</label>
                            <textarea class="form-control" id="school_address" name="school_address" rows="3" required>{{ $settings['school_address'] ?? 'Jl. Raya Terisi No. 1, Indramayu' }}</textarea>
                            <div class="text-danger mt-1 error-message" id="school_address_error"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_phone">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="school_phone" name="school_phone" 
                                        value="{{ $settings['school_phone'] ?? '(021) 1234567' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_email">Email Sekolah</label>
                                    <input type="email" class="form-control" id="school_email" name="school_email" 
                                        value="{{ $settings['school_email'] ?? 'info@smkn1terisi.sch.id' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_website">Website Sekolah</label>
                            <input type="url" class="form-control" id="school_website" name="school_website" 
                                value="{{ $settings['school_website'] ?? 'https://smkn1terisi.sch.id' }}">
                        </div>

                        <div class="form-group">
                            <label for="school_description">Tentang Sekolah</label>
                            <textarea class="form-control" id="school_description" name="school_description" rows="5">{{ $settings['school_description'] ?? 'SMK Negeri 1 Terisi adalah sekolah menengah kejuruan unggulan di Indramayu yang fokus pada pendidikan berkualitas dan pengembangan karir siswa.' }}</textarea>
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
        $('#school-settings-form').on('submit', function(e) {
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
                url: _baseURL + 'api/settings/school',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success message
                    toastr.success(response.message || 'Informasi sekolah berhasil diperbarui', 'Sukses');
                    
                    // Show success state
                    $btn.prop('disabled', true);
                    $icon.removeClass().addClass('mdi mdi-check');
                    $text.text('Tersimpan!');
                    
                    // Reload page after short delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
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
                            toastr.error('Terdapat kesalahan pada form', 'Error');
                        } else {
                            toastr.error(response.message || 'Gagal memperbarui informasi sekolah', 'Error');
                        }
                    } catch (e) {
                        toastr.error('Gagal memperbarui informasi sekolah. Silakan coba lagi.', 'Error');
                    }
                }
            });
        });
    });
</script>
@endpush
