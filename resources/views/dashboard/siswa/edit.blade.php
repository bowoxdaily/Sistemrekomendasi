<!-- views/student/profile/edit.blade.php -->
@extends('layout.app')

@section('title', 'Lengkapi Profil')

@section('content')
<div class="row">
    <div class="col-md-10 grid-margin">
        <div class="row">
            <div class="col-10 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Lengkapi Profil Siswa</h3>
                <h6 class="font-weight-normal mb-0">Mohon lengkapi semua informasi profil Anda sebelum melanjutkan.</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Pribadi</h4>
                <p class="card-description">
                    Lengkapi informasi pribadi Anda dengan benar
                </p>
                
                <!-- Alert container for messages -->
                <div id="alert-container"></div>
                
                <form class="forms-sample" id="profileForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap">
                        <div class="invalid-feedback" id="nama_lengkap_error"></div>
                    </div>
                    
                    <!-- Jenis Kelamin -->
                    <div class="form-group">
                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        <div class="invalid-feedback" id="jenis_kelamin_error"></div>
                    </div>
                    
                    
                    <!-- NISN -->
                    <div class="form-group">
                        <label for="nisn">NISN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nisn" name="nisn" placeholder="Masukkan NISN">
                        <div class="invalid-feedback" id="nisn_error"></div>
                    </div>
                    
                    <!-- Tempat & Tanggal Lahir -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan tempat lahir">
                                <div class="invalid-feedback" id="tempat_lahir_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                                <div class="invalid-feedback" id="tanggal_lahir_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alamat -->
                    <div class="form-group">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="4" placeholder="Masukkan alamat lengkap"></textarea>
                        <div class="invalid-feedback" id="alamat_error"></div>
                    </div>
                    
                    <!-- Foto -->
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <div class="row">
                            <div class="col-md-4">
                                <img id="preview-foto" src="{{ asset('admin/images/faces/face1.jpg') }}" class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <div class="col-md-8">
                                <input type="file" name="foto" id="foto" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="foto_error"></div>
                                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mr-2" id="submitBtn">Simpan</button>
                    <button type="button" class="btn btn-light" id="cancelBtn">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize file upload
        $('.file-upload-browse').on('click', function() {
            var file = $(this).parent().parent().parent().find('.file-upload-default');
            file.trigger('click');
        });
        
        $('.file-upload-default').on('change', function() {
            var filename = $(this).val().split('\\').pop();
            $(this).parent().find('.form-control').val(filename);
            readURL(this);
        });
        
        // File upload preview
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#preview-foto').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Load existing profile data
        function loadProfileData() {
            $.ajax({
                url: '{{ route("api.student.profile.get") }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const data = response.data;
                        $('#nama_lengkap').val(data.nama_lengkap);
                        $('#tempat_lahir').val(data.tempat_lahir);
                        $('#tanggal_lahir').val(data.tanggal_lahir);
                        $('#alamat').val(data.alamat);
                        $('#nisn').val(data.nisn);
                        
                        // Set radio button
                        if (data.jenis_kelamin) {
                            $('input[name="jenis_kelamin"][value="' + data.jenis_kelamin + '"]').prop('checked', true);
                        }
                        
                        // Display existing photo
                        if (data.foto) {
                            $('#preview-foto').attr('src', '{{ asset("storage/student_photos") }}/' + data.foto);
                        }
                    }
                },
                error: function(xhr) {
                    // It's okay if profile doesn't exist yet
                    console.log('Profile not found or error loading profile');
                }
            });
        }
        
        // Load profile data on page load
        loadProfileData();
        
        // Form submission
        $('#profileForm').submit(function(e) {
            e.preventDefault();
            
            // Reset previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // Show loading state
            $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            $('#submitBtn').prop('disabled', true);
            
            // Prepare form data for AJAX submission
            var formData = new FormData(this);
            
            $.ajax({
                url: '{{ route("api.student.profile.update") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success message
                    $('#alert-container').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        response.message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>'
                    );
                    
                    // Redirect to dashboard after delay
                    setTimeout(function() {
                        window.location.href = '{{ route("dashboard") }}';
                    }, 2000);
                },
                error: function(xhr) {
                    // Reset button state
                    $('#submitBtn').html('Simpan');
                    $('#submitBtn').prop('disabled', false);
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors;
                        
                        // Display errors
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '_error').text(messages[0]);
                        });
                        
                        // Show error alert
                        $('#alert-container').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            'Terdapat kesalahan pada form. Silakan periksa kembali.' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>'
                        );
                    } else {
                        // Server error
                        $('#alert-container').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            'Terjadi kesalahan. Silakan coba lagi nanti.' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>'
                        );
                    }
                },
                complete: function() {
                    // Reset button state
                    $('#submitBtn').html('Simpan');
                    $('#submitBtn').prop('disabled', false);
                }
            });
        });
        
        // Cancel button action
        $('#cancelBtn').click(function() {
            if (confirm('Apakah Anda yakin ingin membatalkan? Semua perubahan akan hilang.')) {
                window.location.href = '{{ route("dashboard") }}';
            }
        });
    });
</script>
@endsection