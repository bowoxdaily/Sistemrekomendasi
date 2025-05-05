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

                    <form class="forms-sample" id="profileForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                placeholder="Masukkan nama lengkap">
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
                            <input type="text" class="form-control" id="nisn" name="nisn"
                                placeholder="Masukkan NISN">
                            <div class="invalid-feedback" id="nisn_error"></div>
                        </div>

                        <!-- Tempat & Tanggal Lahir -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                        placeholder="Masukkan tempat lahir">
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
                                    <img id="preview-foto" src="{{ asset('admin/images/faces/face1.jpg') }}"
                                        class="img-thumbnail mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <input type="file" name="foto" id="foto" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                type="button">Upload</button>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toastr Global Options (Opsional)
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 3000,
                positionClass: "toast-top-right"
            };

            // File Upload Logic
            $('.file-upload-browse').on('click', function() {
                var file = $(this).closest('.form-group').find('.file-upload-default');
                file.trigger('click');
            });

            $('.file-upload-default').on('change', function() {
                var filename = $(this).val().split('\\').pop();
                $(this).closest('.form-group').find('.file-upload-info').val(filename);
                readURL(this);
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-foto').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Load existing profile data
            function loadProfileData() {
                $.ajax({
                    url: '{{ route('api.student.profile.get') }}',
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
                            if (data.jenis_kelamin) {
                                $('#jenis_kelamin').val(data.jenis_kelamin);
                            }
                            if (data.foto) {
                                $('#preview-foto').attr('src', '{{ asset('storage/user_photos') }}/' +
                                    data.foto);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('Error loading profile:', xhr.responseText);
                    }
                });
            }

            loadProfileData();

            // Submit Form
            $('#profileForm').submit(function(e) {
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#submitBtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                );
                $('#submitBtn').prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('api.student.profile.update') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Berhasil');
                        setTimeout(function() {
                            window.location.href = '{{ route('dashboard') }}';
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('#submitBtn').html('Simpan');
                        $('#submitBtn').prop('disabled', false);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '_error').text(messages[0]);
                            });
                            toastr.error(
                                'Terdapat kesalahan pada form. Silakan periksa kembali.',
                                'Validasi Gagal');
                        } else {
                            toastr.error(
                                xhr.responseJSON ? xhr.responseJSON.message : xhr
                                .statusText,
                                'Terjadi Kesalahan'
                            );
                        }
                    },
                    complete: function() {
                        $('#submitBtn').html('Simpan');
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });

            $('#cancelBtn').click(function() {
                if (confirm('Apakah Anda yakin ingin membatalkan? Semua perubahan akan hilang.')) {
                    window.location.href = '{{ route('dashboard') }}';
                }
            });
        });
    </script>
@endpush
