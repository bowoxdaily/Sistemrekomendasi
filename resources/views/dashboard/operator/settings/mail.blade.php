@extends('layout.app')

@section('title', 'Pengaturan Email')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Email</h4>
                    <p class="card-description">
                        Konfigurasi server email untuk notifikasi sistem
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('operator.settings.mail.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="mail_driver">Mail Driver</label>
                            <select class="form-control" id="mail_driver" name="mail_driver">
                                <option value="smtp" {{ ($settings['mail_driver'] ?? '') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ ($settings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ ($settings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mail_host">SMTP Host</label>
                            <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_port">SMTP Port</label>
                                    <input type="text" class="form-control" id="mail_port" name="mail_port" 
                                        value="{{ $settings['mail_port'] ?? '587' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_encryption">Encryption</label>
                                    <select class="form-control" id="mail_encryption" name="mail_encryption">
                                        <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mail_username">SMTP Username</label>
                            <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                value="{{ $settings['mail_username'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label for="mail_password">SMTP Password</label>
                            <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                placeholder="••••••••">
                            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                        </div>

                        <div class="form-group">
                            <label for="mail_from_address">Email Pengirim</label>
                            <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                value="{{ $settings['mail_from_address'] ?? 'noreply@example.com' }}">
                        </div>

                        <div class="form-group">
                            <label for="mail_from_name">Nama Pengirim</label>
                            <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                value="{{ $settings['mail_from_name'] ?? 'Sistem Tracer Study' }}">
                        </div>

                        <div class="form-group mt-4">
                            <label>Common SMTP Presets</label>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-secondary smtp-preset mr-2 mb-2" data-host="smtp.gmail.com" data-port="587" data-encryption="tls">
                                    <i class="mdi mdi-google mr-1"></i> Gmail
                                </button>
                                <button type="button" class="btn btn-outline-secondary smtp-preset mr-2 mb-2" data-host="smtp.office365.com" data-port="587" data-encryption="tls">
                                    <i class="mdi mdi-microsoft mr-1"></i> Office 365
                                </button>
                                <button type="button" class="btn btn-outline-secondary smtp-preset mr-2 mb-2" data-host="smtp.mail.yahoo.com" data-port="587" data-encryption="tls">
                                    <i class="mdi mdi-yahoo mr-1"></i> Yahoo
                                </button>
                                <button type="button" class="btn btn-outline-secondary smtp-preset mr-2 mb-2" data-host="smtp-mail.outlook.com" data-port="587" data-encryption="tls">
                                    <i class="mdi mdi-microsoft mr-1"></i> Outlook
                                </button>
                            </div>
                            <small class="form-text text-muted">Klik pada preset untuk mengisi otomatis konfigurasi SMTP server.</small>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-info mr-2" id="testEmailBtn">
                                <i class="mdi mdi-email-send"></i> Kirim Email Tes
                            </button>
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Email Tes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="test_email">Alamat Email Tujuan</label>
                        <input type="email" class="form-control" id="test_email" placeholder="Masukkan email tujuan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">Kirim</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Test email button handler
        $('#testEmailBtn').click(function() {
            $('#testEmailModal').modal('show');
        });
        
        // Send test email handler
        $('#sendTestEmailBtn').click(function() {
            const email = $('#test_email').val();
            if (!email) {
                alert('Silakan masukkan alamat email tujuan');
                return;
            }
            
            // Show loading
            $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span>Mengirim...')
                .prop('disabled', true);
                
            // Collect form data
            const formData = {
                mail_driver: $('#mail_driver').val(),
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_username: $('#mail_username').val(),
                mail_password: $('#mail_password').val(),
                mail_from_address: $('#mail_from_address').val(),
                mail_from_name: $('#mail_from_name').val(),
                test_email: email,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Send test email
            $.ajax({
                url: '{{ route('operator.settings.mail.test') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#testEmailModal').modal('hide');
                    
                    if (response.success) {
                        // Show success notification
                        toastr.success('Email tes berhasil dikirim!');
                    } else {
                        // Show error notification
                        toastr.error('Gagal mengirim email: ' + response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan saat mengirim email');
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Reset button state
                    $('#sendTestEmailBtn').html('Kirim').prop('disabled', false);
                }
            });
        });
        
        // Toggle SMTP settings based on driver selection
        $('#mail_driver').change(function() {
            const driver = $(this).val();
            const smtpFields = $('#mail_host, #mail_port, #mail_encryption, #mail_username, #mail_password');
            
            if (driver === 'smtp' || driver === 'mailgun') {
                smtpFields.closest('.form-group, .col-md-6').show();
            } else {
                smtpFields.closest('.form-group, .col-md-6').hide();
            }
        }).trigger('change');
        
        // SMTP preset button handler
        $('.smtp-preset').on('click', function() {
            const host = $(this).data('host');
            const port = $(this).data('port');
            const encryption = $(this).data('encryption');
            
            $('#mail_host').val(host);
            $('#mail_port').val(port);
            $('#mail_encryption').val(encryption);
            
            // Highlight the fields with animation
            $('#mail_host, #mail_port, #mail_encryption').css('background-color', '#f0f8ff')
                .animate({ backgroundColor: '#ffffff' }, 1500);
                
            toastr.info(`SMTP settings loaded for ${host}`);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        border-radius: 0.3rem;
    }
    
    .smtp-preset {
        transition: all 0.2s;
    }
    
    .smtp-preset:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
