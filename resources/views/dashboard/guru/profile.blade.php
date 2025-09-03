@extends('layout.app')

@section('title', 'Profil Guru')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <!-- Kartu Profil (Kiri) -->
                <div class="col-md-4 grid-margin">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="profile-image-wrapper mb-4">
                                <img src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}"
                                    alt="Foto Profil" class="rounded-circle img-fluid"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <h4 class="mb-1">{{ $teacher->nama_lengkap ?? Auth::user()->name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="mdi mdi-school mr-1"></i>
                                {{ $teacher->jabatan ?? 'Guru' }}
                            </p>
                            <p class="text-muted mb-3">
                                <i class="mdi mdi-email mr-1"></i>
                                {{ Auth::user()->email }}
                            </p>
                            <div class="badge badge-info">
                                <i class="mdi mdi-account-check mr-1"></i>
                                Status: Aktif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Profil (Kanan) -->
                <div class="col-md-8 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs nav-tabs-line mb-4" id="profileTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview"
                                        role="tab">Overview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab">Edit
                                        Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-toggle="tab" href="#password"
                                        role="tab">Change Password</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="profileTabContent">
                                <!-- Overview Tab -->
                                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                    <h5 class="mt-4 mb-3">Informasi Profil Guru</h5>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">Nama Lengkap</div>
                                        <div class="col-md-8 text-primary" data-field="nama_lengkap">
                                            {{ $teacher->nama_lengkap ?? Auth::user()->name }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">NIP</div>
                                        <div class="col-md-8 text-primary" data-field="nip">{{ $teacher->nip ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">Jabatan</div>
                                        <div class="col-md-8 text-primary" data-field="jabatan">
                                            {{ $teacher->jabatan ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">Jenis Kelamin</div>
                                        <div class="col-md-8 text-primary" data-field="jenis_kelamin">
                                            {{ $teacher->jenis_kelamin ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">Alamat</div>
                                        <div class="col-md-8 text-primary" data-field="alamat">
                                            {{ $teacher->alamat ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">No. Telepon</div>
                                        <div class="col-md-8 text-primary" data-field="no_telp">
                                            {{ Auth::user()->no_telp ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 text-muted">Email</div>
                                        <div class="col-md-8 text-primary" data-field="email">
                                            {{ Auth::user()->email }}</div>
                                    </div>
                                </div>

                                <!-- Edit Profile Tab -->
                                <div class="tab-pane fade" id="edit" role="tabpanel">
                                    <form id="editProfileForm" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Profile Image -->
                                        <div class="form-group row mb-4">
                                            <label class="col-md-3 col-form-label text-md-right">Foto Profil</label>
                                            <div class="col-md-9">
                                                <div class="text-start">
                                                    <div class="profile-image-container mb-3">
                                                        <img id="profilePic"
                                                            src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}"
                                                            alt="Foto Profil" class="rounded-circle"
                                                            style="width: 120px; height: 120px; object-fit: cover;">
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-sm mb-2"
                                                        onclick="document.getElementById('newProfilePhoto').click()">
                                                        <i class="mdi mdi-upload"></i> Upload Foto
                                                    </button>
                                                    <input type="file" name="foto" id="newProfilePhoto" class="d-none"
                                                        accept="image/*">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NIP -->
                                        <div class="form-group row mb-3">
                                            <label for="nip"
                                                class="col-md-3 col-form-label text-md-right">NIP</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="nip" name="nip"
                                                    value="{{ $teacher->nip ?? '' }}" readonly>
                                                <small class="text-muted">NIP tidak dapat diubah. Hubungi admin jika ada
                                                    kesalahan.</small>
                                            </div>
                                        </div>

                                        <!-- Full Name -->
                                        <div class="form-group row mb-3">
                                            <label for="nama_lengkap" class="col-md-3 col-form-label text-md-right">Nama
                                                Lengkap</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="nama_lengkap"
                                                    name="nama_lengkap"
                                                    value="{{ $teacher->nama_lengkap ?? Auth::user()->name }}">
                                            </div>
                                        </div>

                                        <!-- Jabatan -->
                                        <div class="form-group row mb-3">
                                            <label for="jabatan"
                                                class="col-md-3 col-form-label text-md-right">Jabatan</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                                    value="{{ $teacher->jabatan ?? '' }}" readonly>
                                                <small class="text-muted">Jabatan tidak dapat diubah. Hubungi admin jika
                                                    ada
                                                    kesalahan.</small>
                                            </div>
                                        </div>

                                        <!-- Jenis Kelamin -->
                                        <div class="form-group row mb-3">
                                            <label for="jenis_kelamin" class="col-md-3 col-form-label text-md-right">Jenis
                                                Kelamin</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                                    <option value="">Pilih Jenis Kelamin</option>
                                                    <option value="Laki-laki"
                                                        {{ ($teacher->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                                        Laki-laki</option>
                                                    <option value="Perempuan"
                                                        {{ ($teacher->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                                        Perempuan</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group row mb-3">
                                            <label for="alamat"
                                                class="col-md-3 col-form-label text-md-right">Alamat</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ $teacher->alamat ?? '' }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="form-group row mb-3">
                                            <label for="no_telp" class="col-md-3 col-form-label text-md-right">No.
                                                Telepon</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="no_telp" name="no_telp"
                                                    value="{{ Auth::user()->no_telp ?? '' }}">
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group row mb-3">
                                            <label for="email"
                                                class="col-md-3 col-form-label text-md-right">Email</label>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ Auth::user()->email }}">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-md-9 offset-md-3">
                                                <button type="submit" id="profileUpdateBtn" class="btn btn-primary">
                                                    <i class="mdi mdi-content-save mr-1"></i>Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <h5 class="mb-4">
                                        <i class="mdi mdi-lock-outline mr-2"></i>Ubah Password
                                    </h5>

                                    <form id="changePasswordForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Password Saat Ini</label>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Password Baru</label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" required>
                                            <small class="text-muted">Password minimal 8 karakter</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password
                                                Baru</label>
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                        </div>

                                        <div class="text-end mt-4">
                                            <button type="submit" id="passwordUpdateBtn" class="btn btn-primary">
                                                <i class="mdi mdi-key-change mr-1"></i>Ubah Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Submit form profil dengan AJAX
                $('#editProfileForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    $('#profileUpdateBtn').attr('disabled', true).text('Menyimpan...');

                    $.ajax({
                        url: _baseURL + 'api/guru/update/profile',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            toastr.success(res.message || 'Profil berhasil diperbarui');

                            // Update tampilan data di halaman
                            if (res.data) {
                                $('[data-field="nama_lengkap"]').text(res.data.nama_lengkap);
                                $('[data-field="nip"]').text(res.data.nip || '-');
                                $('[data-field="jabatan"]').text(res.data.jabatan || '-');
                                $('[data-field="jenis_kelamin"]').text(res.data.jenis_kelamin ||
                                    '-');
                                $('[data-field="alamat"]').text(res.data.alamat || '-');
                                $('[data-field="no_telp"]').text(res.data.no_telp || '-');
                                $('[data-field="email"]').text(res.data.email);
                            }

                            if (res.data && res.data.foto_url) {
                                $('#profilePic').attr('src', res.data.foto_url);
                                $('.profile-image-wrapper img').attr('src', res.data.foto_url);
                            }

                            // Reload halaman setelah delay singkat agar toastr terlihat
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                for (let key in errors) {
                                    toastr.error(errors[key][0]);
                                }
                            } else {
                                toastr.error('Terjadi kesalahan saat menyimpan data.');
                            }
                        },
                        complete: function() {
                            $('#profileUpdateBtn').attr('disabled', false).html(
                                '<i class="mdi mdi-content-save mr-1"></i>Simpan Perubahan');
                        }
                    });
                });

                // Preview foto profil sebelum upload
                $('#newProfilePhoto').on('change', function() {
                    if (this.files && this.files[0]) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            $('#profilePic').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Submit form ubah password dengan AJAX
                $('#changePasswordForm').on('submit', function(e) {
                    e.preventDefault();

                    $('.is-invalid').removeClass('is-invalid');
                    $('#passwordUpdateBtn').attr('disabled', true).text('Memproses...');

                    const formData = {
                        current_password: $('#current_password').val(),
                        new_password: $('#new_password').val(),
                        new_password_confirmation: $('#new_password_confirmation').val(),
                        _token: $('input[name="_token"]').val()
                    };

                    $.ajax({
                        url: _baseURL + 'api/guru/change-password',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#changePasswordForm')[0].reset();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                if (errors) {
                                    for (const field in errors) {
                                        toastr.error(errors[field][0]);
                                        $(`#${field}`).addClass('is-invalid');
                                    }
                                } else if (xhr.responseJSON.message) {
                                    toastr.error(xhr.responseJSON.message);
                                }
                            } else {
                                toastr.error('Terjadi kesalahan saat mengubah password.');
                            }
                        },
                        complete: function() {
                            $('#passwordUpdateBtn').attr('disabled', false).html(
                                '<i class="mdi mdi-key-change mr-1"></i>Ubah Password');
                        }
                    });
                });
            });
        </script>
    @endpush


@endsection

@push('styles')
    <style>
        /* Readonly field styling */
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }

        .form-control[readonly]:focus {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            box-shadow: none;
        }

        /* Profile image styling */
        .profile-image-wrapper img,
        .profile-image-container img {
            border: 3px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        .profile-image-wrapper img:hover,
        .profile-image-container img:hover {
            border-color: #007bff;
        }
    </style>
@endpush
