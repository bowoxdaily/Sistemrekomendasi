@extends('layout.app')

@section('title', 'Lengkapi Profil')

@section('content') <div class="row">
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
                                value="{{ old('nama_lengkap', $student->nama_lengkap ?? '') }}" readonly>
                            <small class="form-text text-muted">Jika ada kesalahan nama, tolong hubungi Wali Kelas.</small>
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
                                    <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="provinsi" name="provinsi">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                    <div class="invalid-feedback" id="provinsi_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                    <select class="form-control" id="tempat_lahir" name="tempat_lahir">
                                        <option value="">-- Pilih Kabupaten/Kota --</option>
                                    </select>
                                    <!-- Hidden input to store selected provinsi name -->
                                    <input type="hidden" id="provinsi_name" name="provinsi_name">
                                    <div class="invalid-feedback" id="tempat_lahir_error"></div>
                                    <small class="form-text text-muted">Pilih provinsi terlebih dahulu</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="row">
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
                        <div class="form-group">
                            <label for="jurusan">Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan_id"
                                placeholder="Masukkan jurusan" value="{{ old('jurusan', $student->jurusan->nama ?? '') }}"
                                readonly>
                            <small class="form-text text-muted">Jika ada kesalahan jurusan, hubungi Wali Kelas.</small>
                            <div class="invalid-feedback" id="jurusan_error"></div>
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
                            <label for="tanggal_lulus">Tanggal Lulus <span class="text-danger">*</span></label>
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
                                        src="{{ $student->foto ? asset('storage/user_photos/' . $student->foto) : asset('admin/images/faces/face1.jpg') }}"
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

@push('styles')
    <style>
        /* Custom styles for location dropdowns */
        .location-dropdown {
            position: relative;
        }

        .location-dropdown .form-control:disabled {
            background-color: #f8f9fa;
            opacity: 0.7;
        }

        .location-loading {
            position: relative;
        }

        .location-loading::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #dee2e6;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* Enhanced dropdown styling */
        .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .mdi-alert::before {
            font-size: 14px;
        }
    </style>
@endpush

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

            // Variables for API data
            let provinsiData = [];
            let kabupatenData = [];
            let currentTempatLahir = '{{ old('tempat_lahir', $student->tempat_lahir ?? '') }}';

            // Load provinsi from API
            function loadProvinsi() {
                $.ajax({
                    url: 'https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        provinsiData = data;
                        let options = '<option value="">-- Pilih Provinsi --</option>';

                        data.forEach(function(provinsi) {
                            options +=
                                `<option value="${provinsi.id}" data-name="${provinsi.name}">${provinsi.name}</option>`;
                        });

                        $('#provinsi').html(options);

                        // Auto-select provinsi if tempat_lahir exists
                        if (currentTempatLahir) {
                            autoSelectProvinsiFromTempatLahir();
                        }
                    },
                    error: function(xhr) {
                        console.log('Error loading provinsi:', xhr);
                        // Fallback to text input if API fails
                        showFallbackInput();
                    }
                });
            }

            // Load kabupaten based on selected provinsi
            function loadKabupaten(provinsiId) {
                if (!provinsiId) {
                    $('#tempat_lahir').html('<option value="">-- Pilih Kabupaten/Kota --</option>');
                    $('#tempat_lahir').prop('disabled', false);
                    return;
                }

                // Show loading in kabupaten dropdown
                $('#tempat_lahir').html('<option value="">Memuat kabupaten/kota...</option>');
                $('#tempat_lahir').prop('disabled', true);
                $('#tempat_lahir').closest('.form-group').addClass('location-loading');

                $.ajax({
                    url: `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinsiId}.json`,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 10000, // 10 second timeout
                    success: function(data) {
                        kabupatenData = data;
                        let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';

                        data.forEach(function(kabupaten) {
                            const selected = kabupaten.name === currentTempatLahir ?
                                'selected' : '';
                            options +=
                                `<option value="${kabupaten.name}" ${selected}>${kabupaten.name}</option>`;
                        });

                        $('#tempat_lahir').html(options);
                        $('#tempat_lahir').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading kabupaten:', xhr, status, error);
                        $('#tempat_lahir').html(
                            '<option value="">Gagal memuat data, coba lagi</option>');

                        // Show retry button
                        setTimeout(() => {
                            $('#tempat_lahir').html(
                                '<option value="">-- Pilih Kabupaten/Kota --</option>');
                            $('#tempat_lahir').prop('disabled', false);
                            toastr.warning(
                                'Gagal memuat data kabupaten/kota. Silakan pilih provinsi lagi.'
                                );
                        }, 2000);
                    },
                    complete: function() {
                        $('#tempat_lahir').closest('.form-group').removeClass('location-loading');
                    }
                });
            }

            // Auto-select provinsi based on existing tempat_lahir
            function autoSelectProvinsiFromTempatLahir() {
                if (!currentTempatLahir || provinsiData.length === 0) return;

                // Load all kabupaten to find matching provinsi
                $.ajax({
                    url: 'https://www.emsifa.com/api-wilayah-indonesia/api/regencies.json',
                    type: 'GET',
                    dataType: 'json',
                    success: function(allKabupaten) {
                        const matchingKabupaten = allKabupaten.find(kab =>
                            kab.name.toLowerCase() === currentTempatLahir.toLowerCase()
                        );

                        if (matchingKabupaten) {
                            const provinsiId = matchingKabupaten.province_id;
                            $('#provinsi').val(provinsiId);
                            loadKabupaten(provinsiId);
                        }
                    },
                    error: function(xhr) {
                        console.log('Error auto-selecting provinsi:', xhr);
                    }
                });
            }

            // Fallback to text input if API fails
            function showFallbackInput() {
                const fallbackHtml = `
                    <div class="form-group">
                        <label for="tempat_lahir_text">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tempat_lahir_text" name="tempat_lahir"
                            placeholder="Masukkan tempat lahir" value="${currentTempatLahir}">
                        <div class="invalid-feedback" id="tempat_lahir_error"></div>
                        <small class="form-text text-muted text-warning">
                            <i class="mdi mdi-alert"></i> Koneksi API terganggu, menggunakan input manual
                        </small>
                    </div>
                `;

                $('#tempat_lahir').closest('.form-group').replaceWith(fallbackHtml);
            }

            // Event handler for provinsi change
            $('#provinsi').on('change', function() {
                const selectedProvinsiId = $(this).val();
                const selectedProvinsiName = $(this).find('option:selected').data('name');

                // Store provinsi name in hidden input
                $('#provinsi_name').val(selectedProvinsiName || '');

                loadKabupaten(selectedProvinsiId);

                // Clear tempat lahir selection when provinsi changes
                currentTempatLahir = '';
            });

            // Add search functionality to tempat lahir dropdown
            $('#tempat_lahir').on('focus', function() {
                // Convert to searchable if has many options
                const optionsCount = $(this).find('option').length;
                if (optionsCount > 20) {
                    $(this).attr('data-live-search', 'true');
                }
            });

            // Initialize provinsi loading
            loadProvinsi();

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
                            $('#alamat').val(data.alamat);
                            $('#nisn').val(data.nisn);

                            // Update currentTempatLahir if available from API
                            if (data.tempat_lahir) {
                                currentTempatLahir = data.tempat_lahir;
                            }

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
                                $('#preview-foto').attr('src', '/storage/user_photos/' + data.foto);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('Error loading profile:', xhr.responseText);
                    }
                });
            }

            loadProfileData();

            // No additional validation needed for date input as browser handles it

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

                // Additional validation for provinsi (if using dropdown)
                const provinsiVal = $('#provinsi').val();
                const tempatLahirVal = $('#tempat_lahir').val();

                if ($('#provinsi').length && !provinsiVal) {
                    $('#provinsi').addClass('is-invalid');
                    $('#provinsi_error').text('Provinsi harus dipilih');
                    $('#submitBtn').html('Simpan');
                    $('#submitBtn').prop('disabled', false);
                    return false;
                }

                if (!tempatLahirVal) {
                    $('#tempat_lahir').addClass('is-invalid');
                    $('#tempat_lahir_error').text('Tempat lahir harus dipilih');
                    $('#submitBtn').html('Simpan');
                    $('#submitBtn').prop('disabled', false);
                    return false;
                }

                // Validation for tanggal_lulus when status is 'lulus'
                if ($('#status_lulus').val() === 'lulus') {
                    const tanggalLulus = $('#tanggal_lulus').val();
                    if (!tanggalLulus) {
                        $('#tanggal_lulus').addClass('is-invalid');
                        $('#tanggal_lulus_error').text('Tanggal lulus harus diisi');
                        $('#submitBtn').html('Simpan');
                        $('#submitBtn').prop('disabled', false);
                        return false;
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
