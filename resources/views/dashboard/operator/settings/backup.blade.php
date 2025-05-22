@extends('layout.app')

@section('title', 'Backup & Restore')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Backup & Restore</h4>
                    <p class="card-description">
                        Kelola backup dan restore database sistem
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-database-export mr-2"></i> Backup Database</h5>
                                </div>
                                <div class="card-body">
                                    <p>Lakukan backup database untuk menyimpan data sistem. Backup akan disimpan dalam format .zip yang terenkripsi.</p>
                                    
                                    <form id="backup-form">
                                        @csrf
                                        <div class="form-group mb-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="include_files" name="include_files" value="1">
                                                <label class="custom-control-label" for="include_files">Sertakan file upload (gambar, dokumen, dll)</label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary" id="backup-btn">
                                            <i class="mdi mdi-database-export mr-1" id="backup-icon"></i> 
                                            <span id="backup-text">Buat Backup</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-calendar mr-2"></i> Backup Otomatis</h5>
                                </div>
                                <div class="card-body">
                                    <p>Aktifkan backup otomatis untuk menjaga keamanan data.</p>
                                    
                                    <form id="schedule-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Frekuensi Backup</label>
                                            <select class="form-control" name="backup_frequency" id="backup_frequency">
                                                <option value="daily" {{ ($settings['backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Harian</option>
                                                <option value="weekly" {{ ($settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                                <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="auto_backup" name="auto_backup" {{ ($settings['auto_backup'] ?? '') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="auto_backup">Aktifkan Backup Otomatis</label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-info" id="schedule-btn">
                                            <i class="mdi mdi-content-save mr-1" id="schedule-icon"></i>
                                            <span id="schedule-text">Simpan Pengaturan</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-database-import mr-2"></i> Restore Database</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert-circle mr-2"></i>
                                        <strong>Perhatian!</strong> Restore database akan mengganti semua data yang ada saat ini. Pastikan Anda memiliki backup data terbaru.
                                    </div>
                                    
                                    <form id="restore-form" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label>Pilih File Backup</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".gz,.zip,.sql" required>
                                                <label class="custom-file-label" for="backup_file">Pilih file...</label>
                                            </div>
                                            <small class="form-text text-muted">Format yang didukung: .gz, .zip, .sql</small>
                                            <div class="text-danger mt-1 error-message" id="backup_file_error"></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="confirm_restore" name="confirm_restore" value="1" required>
                                                <label class="custom-control-label" for="confirm_restore">
                                                    Saya memahami bahwa restore akan mengganti semua data saat ini
                                                </label>
                                                <div class="text-danger mt-1 error-message" id="confirm_restore_error"></div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-warning" id="restore-btn" disabled>
                                            <i class="mdi mdi-database-import mr-1" id="restore-icon"></i> 
                                            <span id="restore-text">Restore Database</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card border-secondary mt-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-history mr-2"></i> Riwayat Backup</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="backup-history-table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Ukuran</th>
                                                    <th>Tipe</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($backupFiles ?? []) > 0)
                                                    @foreach($backupFiles as $backup)
                                                    <tr data-filename="{{ $backup['filename'] }}">
                                                        <td>{{ $backup['date'] }}</td>
                                                        <td>{{ $backup['size'] }}</td>
                                                        <td>{{ $backup['type'] }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('operator.settings.backup.download', ['filename' => $backup['filename']]) }}" 
                                                                   class="btn btn-outline-primary btn-sm" title="Download">
                                                                    <i class="mdi mdi-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-outline-danger btn-sm delete-backup-btn" 
                                                                        data-filename="{{ $backup['filename'] }}" title="Delete">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            <span class="text-muted">Belum ada backup tersimpan</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            toastr.success("{{ session('success') }}", "Sukses");
        @endif
        
        @if(session('error'))
            toastr.error("{{ session('error') }}", "Error");
        @endif
        
        // Create full page overlay for loading state
        function createOverlay(message) {
            const overlay = $('<div id="loading-overlay"></div>').css({
                'position': 'fixed',
                'top': 0,
                'left': 0,
                'width': '100%',
                'height': '100%',
                'background-color': 'rgba(0,0,0,0.7)',
                'z-index': 9999,
                'display': 'flex',
                'justify-content': 'center',
                'align-items': 'center',
                'flex-direction': 'column'
            });
            
            const spinner = $('<div class="spinner-border text-light" style="width: 5rem; height: 5rem;" role="status"></div>');
            const messageText = $('<p class="text-light mt-3 h4"></p>').text(message);
            const subMessage = $('<p class="text-light">Proses ini mungkin memerlukan waktu beberapa saat. Mohon tunggu.</p>');
            
            overlay.append(spinner, messageText, subMessage);
            $('body').append(overlay);
            
            return overlay;
        }
        
        // File input enhancement
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Enable/disable restore button based on checkbox
        $('#confirm_restore').change(function() {
            $('#restore-btn').prop('disabled', !this.checked);
        });
        
        // AJAX Form: Create backup
        $('#backup-form').on('submit', function(e) {
            e.preventDefault();
            
            // Change button state to loading
            var $btn = $('#backup-btn');
            var $icon = $('#backup-icon');
            var $text = $('#backup-text');
            
            // Save original state
            var originalIcon = $icon.attr('class');
            var originalText = $text.text();
            
            // Show loading state
            $btn.prop('disabled', true);
            $icon.removeClass().addClass('mdi mdi-loading mdi-spin');
            $text.text('Memproses...');
            
            // Create overlay
            const overlay = createOverlay('Membuat Backup...');
            
            // Send AJAX request
            $.ajax({
                url: _baseURL + 'api/settings/backup/generate',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Remove overlay
                    overlay.remove();
                    
                    // Show success message
                    toastr.success(response.message || 'Backup berhasil dibuat', 'Sukses');
                    
                    // Refresh the page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    // Remove overlay
                    overlay.remove();
                    
                    // Restore button state
                    $btn.prop('disabled', false);
                    $icon.removeClass().addClass(originalIcon);
                    $text.text(originalText);
                    
                    // Show error message
                    try {
                        const response = JSON.parse(xhr.responseText);
                        toastr.error(response.message || 'Gagal membuat backup', 'Error');
                    } catch (e) {
                        toastr.error('Gagal membuat backup. Silakan coba lagi.', 'Error');
                    }
                }
            });
        });
        
        // AJAX Form: Restore backup - Now using SweetAlert
        $('#restore-form').on('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.error-message').text('');
            
            // Prepare form data for later use
            const formData = new FormData(this);
            const $form = $(this);
            
            // Use SweetAlert for confirmation
            Swal.fire({
                title: 'Restore Database?',
                text: 'Seluruh data yang ada saat ini akan diganti dengan data dari backup. Proses ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F89406', // Warning/orange color
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Restore Database!',
                cancelButtonText: 'Batal',
                focusCancel: true // Focus on cancel for safety
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with restore
                    
                    // Change button state to loading
                    var $btn = $('#restore-btn');
                    var $icon = $('#restore-icon');
                    var $text = $('#restore-text');
                    
                    // Save original state
                    var originalIcon = $icon.attr('class');
                    var originalText = $text.text();
                    
                    // Show loading state
                    $btn.prop('disabled', true);
                    $icon.removeClass().addClass('mdi mdi-loading mdi-spin');
                    $text.text('Memproses...');
                    
                    // Create overlay with loading animation
                    const overlay = createOverlay('Memulihkan Database...');
                    
                    // Send AJAX request
                    $.ajax({
                        url: _baseURL + 'api/settings/backup/restore',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Remove overlay
                            overlay.remove();
                            
                            // Show success message with SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Database berhasil direstore',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh the page
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            // Remove overlay
                            overlay.remove();
                            
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
                                    
                                    // Show error summary
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Validasi Gagal',
                                        text: 'Silakan periksa form dan coba lagi.'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Restore',
                                        text: response.message || 'Gagal melakukan restore'
                                    });
                                }
                            } catch (e) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal melakukan restore. Silakan coba lagi.'
                                });
                            }
                        }
                    });
                }
            });
        });
        
        // AJAX Form: Update backup schedule
        $('#schedule-form').on('submit', function(e) {
            e.preventDefault();
            
            // Change button state to loading
            var $btn = $('#schedule-btn');
            var $icon = $('#schedule-icon');
            var $text = $('#schedule-text');
            
            // Save original state
            var originalIcon = $icon.attr('class');
            var originalText = $text.text();
            
            // Show loading state
            $btn.prop('disabled', true);
            $icon.removeClass().addClass('mdi mdi-loading mdi-spin');
            $text.text('Menyimpan...');
            
            // Send AJAX request
            $.ajax({
                url: _baseURL + 'api/settings/backup/schedule',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Restore button state but keep it disabled for a moment
                    $icon.removeClass().addClass('mdi mdi-check');
                    $text.text('Tersimpan!');
                    
                    // Show success message
                    toastr.success(response.message || 'Pengaturan backup berhasil diperbarui', 'Sukses');
                    
                    // Restore button state after delay
                    setTimeout(function() {
                        $btn.prop('disabled', false);
                        $icon.removeClass().addClass(originalIcon);
                        $text.text(originalText);
                    }, 1500);
                },
                error: function(xhr) {
                    // Restore button state
                    $btn.prop('disabled', false);
                    $icon.removeClass().addClass(originalIcon);
                    $text.text(originalText);
                    
                    // Show error message
                    try {
                        const response = JSON.parse(xhr.responseText);
                        toastr.error(response.message || 'Gagal memperbarui pengaturan', 'Error');
                    } catch (e) {
                        toastr.error('Gagal memperbarui pengaturan. Silakan coba lagi.', 'Error');
                    }
                }
            });
        });
        
        // AJAX: Delete backup
        $(document).on('click', '.delete-backup-btn', function() {
            const filename = $(this).data('filename');
            const $row = $(this).closest('tr');
            
            // Use SweetAlert for delete confirmation
            Swal.fire({
                title: 'Hapus Backup?',
                text: 'File backup ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request - Changed from DELETE to POST
                    $.ajax({
                        url: _baseURL + 'api/settings/backup/delete',
                        type: 'POST', // Changed from DELETE to POST
                        data: { 
                            filename: filename,
                            _token: $('meta[name="csrf-token"]').attr('content') // Explicitly add CSRF token
                        },
                        success: function(response) {
                            // Show success message with SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'File backup berhasil dihapus',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Remove row from table with animation
                            $row.fadeOut(400, function() {
                                $(this).remove();
                                
                                // If no records left, show empty message
                                if ($('#backup-history-table tbody tr').length === 0) {
                                    $('#backup-history-table tbody').html(
                                        '<tr><td colspan="4" class="text-center"><span class="text-muted">Belum ada backup tersimpan</span></td></tr>'
                                    );
                                }
                            });
                        },
                        error: function(xhr) {
                            // Show error message
                            try {
                                const response = JSON.parse(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: response.message || 'Gagal menghapus backup'
                                });
                            } catch (e) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menghapus backup. Silakan coba lagi.'
                                });
                            }
                        }
                    });
                }
            });
        });
        
        // Demo backup deletion handler with SweetAlert
        $(document).on('click', '.demo-delete-backup', function(e) {
            e.preventDefault();
            
            const $row = $(this).closest('tr');
            
            // Use SweetAlert for confirmation
            Swal.fire({
                title: 'Hapus Demo Backup?',
                text: 'File backup demo ini akan dihapus.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // For demo data, just remove the row directly
                    $row.fadeOut(400, function() {
                        $(this).remove();
                        
                        // If no records left, show empty message
                        if ($('#backup-history-table tbody tr').length === 0) {
                            $('#backup-history-table tbody').html(
                                '<tr><td colspan="4" class="text-center"><span class="text-muted">Belum ada backup tersimpan</span></td></tr>'
                            );
                        }
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File backup berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Spinner animation */
    @keyframes spinner-grow {
        0% { transform: scale(0); }
        50% { opacity: 1; transform: none; }
    }
    
    /* Loading overlay fade-in animation */
    #loading-overlay {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Pulse animation for overlay text */
    #loading-overlay p {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 0.8; }
        50% { opacity: 1; }
        100% { opacity: 0.8; }
    }
    
    /* Error message styling */
    .error-message {
        font-size: 0.875rem;
    }
    
    /* Add success indicator animation */
    .success-indicator {
        display: inline-block;
        transform: scale(0);
        animation: success-indicator-animation 0.5s ease forwards;
    }
    
    @keyframes success-indicator-animation {
        0% { transform: scale(0); }
        70% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
</style>
@endpush
