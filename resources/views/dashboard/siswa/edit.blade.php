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
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('nama_lengkap', $student->nama_lengkap ?? '') }}">
                            <div class="invalid-feedback" id="nama_lengkap_error"></div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $student->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $student->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            <div class="invalid-feedback" id="jenis_kelamin_error"></div>
                        </div>

                        <!-- NISN -->
                        <div class="form-group">
                            <label for="nisn">NISN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nisn" name="nisn"
                                placeholder="Masukkan NISN" readonly value="{{ old('nisn', $student->nisn ?? '') }}">
                            <div class="invalid-feedback" id="nisn_error"></div>
                        </div>

                        <!-- Tempat & Tanggal Lahir -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                        placeholder="Masukkan tempat lahir"
                                        value="{{ old('tempat_lahir', $student->tempat_lahir ?? '') }}">
                                    <div class="invalid-feedback" id="tempat_lahir_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', $student->tanggal_lahir ?? '') }}">
                                    <div class="invalid-feedback" id="tanggal_lahir_error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="form-group">
                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="4" placeholder="Masukkan alamat lengkap">{{ old('alamat', $student->alamat ?? '') }}</textarea>
                            <div class="invalid-feedback" id="alamat_error"></div>
                        </div>

                        <!-- No Telepon -->
                        <div class="form-group">
                            <label for="no_telp">No Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp"
                                placeholder="+62899XXXX" value="{{ old('no_telp', $user->no_telp ?? '') }}">
                            <div class="invalid-feedback" id="no_telp_error"></div>
                        </div>

                        <!-- Status Kelulusan -->
                        <div class="form-group">
                            <label>Status Kelulusan</label>
                            <select class="form-control" name="status_lulus" id="status_lulus">
                                <option value="belum"
                                    {{ old('status_lulus', $student->status_lulus ?? '') == 'belum' ? 'selected' : '' }}>
                                    Belum Lulus</option>
                                <option value="lulus"
                                    {{ old('status_lulus', $student->status_lulus ?? '') == 'lulus' ? 'selected' : '' }}>
                                    Lulus</option>
                            </select>
                            <div class="invalid-feedback" id="status_lulus_error"></div>
                        </div>

                        <!-- Tanggal Lulus (conditionally shown) -->
                        <div class="form-group" id="tanggal_lulus_container"
                            style="{{ old('status_lulus', $student->status_lulus ?? '') == 'lulus' ? '' : 'display: none;' }}">
                            <label for="tanggal_lulus">Tanggal Lulus</label>
                            <input type="date" class="form-control" id="tanggal_lulus" name="tanggal_lulus"
                                value="{{ old('tanggal_lulus', $student->tanggal_lulus ?? '') }}">
                            <div class="invalid-feedback" id="tanggal_lulus_error"></div>
                        </div>

                        <!-- Status Setelah Lulus (conditionally shown) -->
                        <div class="form-group" id="status_setelah_lulus_container"
                            style="{{ old('status_lulus', $student->status_lulus ?? '') == 'lulus' ? '' : 'display: none;' }}">
                            <label>Status Setelah Lulus</label>
                            <select class="form-control" name="status_setelah_lulus" id="status_setelah_lulus">
                                <option value="">-- Pilih Status --</option>
                                <option value="belum_kerja"
                                    {{ old('status_setelah_lulus', $student->status_setelah_lulus ?? '') == 'belum_kerja' ? 'selected' : '' }}>
                                    Belum Bekerja</option>
                                <option value="kuliah"
                                    {{ old('status_setelah_lulus', $student->status_setelah_lulus ?? '') == 'kuliah' ? 'selected' : '' }}>
                                    Kuliah</option>
                                <option value="kerja"
                                    {{ old('status_setelah_lulus', $student->status_setelah_lulus ?? '') == 'kerja' ? 'selected' : '' }}>
                                    Bekerja</option>
                            </select>
                            <div class="invalid-feedback" id="status_setelah_lulus_error"></div>
                        </div>

                        <!-- Foto -->
                        <div class="form-group">
                            <label>Foto Profil</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <img id="preview-foto"
                                        src="{{ asset('storage/user_photos/' . ($student->foto ?? 'default.jpg')) }}"
                                        class="img-thumbnail mb-2"
                                        style="width: 150px; height: 150px; object-fit: cover;">
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

            // Handle conditional fields visibility based on status_lulus
            $('#status_lulus').on('change', function() {
                if ($(this).val() === 'lulus') {
                    $('#tanggal_lulus_container').show();
                    $('#status_setelah_lulus_container').show();
                } else {
                    $('#tanggal_lulus_container').hide();
                    $('#status_setelah_lulus_container').hide();
                    $('#tanggal_lulus').val('');
                    $('#status_setelah_lulus').val('');
                }
            });

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

                            // Set user no_telp
                            $('#no_telp').val(response.data.no_telp);

                            if (data.status_lulus) {
                                $('#status_lulus').val(data.status_lulus);

                                if (data.status_lulus === 'lulus') {
                                    $('#tanggal_lulus_container').show();
                                    $('#status_setelah_lulus_container').show();
                                    $('#tanggal_lulus').val(data.tanggal_lulus);
                                    $('#status_setelah_lulus').val(data.status_setelah_lulus);
                                }
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

                // Add status_terakhir_diupdate field with current timestamp
                if ($('#status_lulus').val() === 'lulus') {
                    formData.append('status_terakhir_diupdate', new Date().toISOString().split('T')[0]);
                }

                // Set is_profile_complete based on required fields
                const requiredFields = ['nama_lengkap', 'jenis_kelamin', 'nisn', 'tempat_lahir',
                    'tanggal_lahir', 'alamat'
                ];
                let isComplete = true;

                for (const field of requiredFields) {
                    if (!formData.get(field)) {
                        isComplete = false;
                        break;
                    }
                }

                formData.append('is_profile_complete', isComplete ? '1' : '0');

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
                        if (response.status === 'success') {
                            toastr.success('Profil berhasil diperbarui');
                            // Redirect to dashboard after successful profile update
                            setTimeout(function() {
                                    window.location.href =
                                        '{{ route('dashboard') }}';
                                },
                                1500
                            ); // Delay for 1.5 seconds so user can see the success message
                        } else {
                            toastr.error('Terjadi kesalahan, silakan coba lagi');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '_error').text(errors[field][0]);
                        }
                        toastr.error('Periksa kembali form Anda');
                    },
                    complete: function() {
                        $('#submitBtn').html('Simpan');
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
