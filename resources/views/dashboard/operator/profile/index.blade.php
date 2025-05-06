@extends('layout.app')

@section('title', 'Profile Operator')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Profile Incomplete Alert --}}
            @if (!$operator || !$operator->nama_lengkap)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-alert-circle-outline mr-3" style="font-size: 24px;"></i>
                                <div>
                                    <h4 class="alert-heading mb-1">Profil Anda Belum Lengkap!</h4>
                                    <p class="mb-0">Silakan lengkapi profil Anda untuk mengoptimalkan penggunaan sistem.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

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
                            <h4 class="mb-1">{{ $operator->nama_lengkap ?? 'Lengkapi Profil Anda' }}</h4>
                            <p class="text-muted mb-3">{{ $operator->user->role ?? 'Operator' }}</p>

                            <!-- Social Media Icons -->
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#" class="text-secondary mx-2"><i class="mdi mdi-twitter h5"></i></a>
                                <a href="#" class="text-secondary mx-2"><i class="mdi mdi-facebook h5"></i></a>
                                <a href="#" class="text-secondary mx-2"><i class="mdi mdi-instagram h5"></i></a>
                                <a href="#" class="text-secondary mx-2"><i class="mdi mdi-linkedin h5"></i></a>
                            </div>

                            @if (!$operator || !$operator->nama_lengkap)
                                <div class="mt-4">
                                    <button class="btn btn-primary" id="completeProfileBtn">
                                        <i class="mdi mdi-account-edit mr-1"></i> Lengkapi Profil
                                    </button>
                                </div>
                            @endif
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
                                    <a class="nav-link {{ !$operator || !$operator->nama_lengkap ? '' : 'active' }}"
                                        id="overview-tab" data-toggle="tab" href="#overview" role="tab">Overview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !$operator || !$operator->nama_lengkap ? 'active' : '' }}"
                                        id="edit-tab" data-toggle="tab" href="#edit" role="tab">Edit
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
                                <div class="tab-pane fade {{ !$operator || !$operator->nama_lengkap ? '' : 'show active' }}"
                                    id="overview" role="tabpanel">
                                    @if (!$operator || !$operator->nama_lengkap)
                                        <div class="text-center py-5">
                                            <div class="mb-4">
                                                <i class="mdi mdi-account-alert" style="font-size: 64px; color: #ccc;"></i>
                                            </div>
                                            <h5>Data Profil Belum Tersedia</h5>
                                            <p class="text-muted">Silakan lengkapi profil Anda di tab "Edit Profile"</p>
                                            <button class="btn btn-outline-primary mt-2"
                                                onclick="$('#edit-tab').tab('show')">
                                                <i class="mdi mdi-pencil mr-1"></i> Edit Profil Sekarang
                                            </button>
                                        </div>
                                    @else
                                        <h5 class="mt-4 mb-3">Profile Details</h5>
                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">Nama Lengkap</div>
                                            <div class="col-md-8 text-primary" data-field="nama_lengkap">
                                                {{ $operator->nama_lengkap }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">NIP</div>
                                            <div class="col-md-8 text-primary" data-field="nip">
                                                {{ $operator->nip ?? 'Belum diisi' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">Jabatan</div>
                                            <div class="col-md-8 text-primary" data-field="jabatan">
                                                {{ $operator->jabatan ?? 'Belum diisi' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">Jenis Kelamin</div>
                                            <div class="col-md-8 text-primary" data-field="jenis_kelamin">
                                                {{ $operator->jenis_kelamin ?? 'Belum diisi' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">Alamat</div>
                                            <div class="col-md-8 text-primary" data-field="alamat">
                                                {{ $operator->alamat ?? 'Belum diisi' }}
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-4 text-muted">Email</div>
                                            <div class="col-md-8 text-primary" data-field="email">
                                                {{ $operator->user->email ?? 'Belum diisi' }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Edit Profile Tab -->
                                <div class="tab-pane fade {{ !$operator || !$operator->nama_lengkap ? 'show active' : '' }}"
                                    id="edit" role="tabpanel">
                                    @if (!$operator || !$operator->nama_lengkap)
                                        <div class="alert alert-info mb-4">
                                            <i class="mdi mdi-information-outline mr-2"></i>
                                            Silakan lengkapi data profil Anda untuk pertama kali
                                        </div>
                                    @endif

                                    <form id="editProfileForm" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Profile Image -->
                                        <div class="form-group row mb-4">
                                            <label class="col-md-3 col-form-label text-md-right">Profile Image</label>
                                            <div class="col-md-9">
                                                <div class="text-start">
                                                    <div class="profile-image-container mb-3">
                                                        <img id="profilePic"
                                                            src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}"
                                                            alt="Profile Image"
                                                            style="width: 120px; height: 120px; object-fit: cover;">
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-sm mb-2"
                                                        onclick="document.getElementById('newProfilePhoto').click()">
                                                        <i class="mdi mdi-upload"></i> Upload
                                                    </button>
                                                    <input type="file" name="foto" id="newProfilePhoto"
                                                        class="d-none" accept="image/*">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NIP -->
                                        <div class="form-group row mb-3">
                                            <label for="nip"
                                                class="col-md-3 col-form-label text-md-right">NIP</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="nip" name="nip"
                                                    value="{{ $operator->nip ?? '' }}"
                                                    {{ isset($operator->nip) ? 'readonly' : '' }}>
                                                @if (isset($operator->nip))
                                                    <small class="text-muted">NIP tidak dapat diubah</small>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Full Name -->
                                        <div class="form-group row mb-3">
                                            <label for="nama_lengkap" class="col-md-3 col-form-label text-md-right">Nama
                                                Lengkap <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="nama_lengkap"
                                                    name="nama_lengkap" value="{{ $operator->nama_lengkap ?? '' }}"
                                                    required>
                                            </div>
                                        </div>

                                        <!-- Jabatan -->
                                        <div class="form-group row mb-3">
                                            <label for="jabatan" class="col-md-3 col-form-label text-md-right">Jabatan
                                                <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                                    value="{{ $operator->jabatan ?? '' }}" required>
                                            </div>
                                        </div>

                                        <!-- Jenis Kelamin -->
                                        <div class="form-group row mb-3">
                                            <label for="jenis_kelamin" class="col-md-3 col-form-label text-md-right">Jenis
                                                Kelamin <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin"
                                                    required>
                                                    <option value=""
                                                        {{ !isset($operator->jenis_kelamin) ? 'selected' : '' }}>-- Pilih
                                                        Jenis Kelamin --</option>
                                                    <option value="Laki-laki"
                                                        {{ isset($operator->jenis_kelamin) && $operator->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                                        Laki-laki</option>
                                                    <option value="Perempuan"
                                                        {{ isset($operator->jenis_kelamin) && $operator->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                                        Perempuan</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group row mb-3">
                                            <label for="alamat" class="col-md-3 col-form-label text-md-right">Alamat
                                                <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ $operator->alamat ?? '' }}</textarea>
                                            </div>
                                        </div>



                                        <div class="form-group row mb-0">
                                            <div class="col-md-9 offset-md-3">
                                                <button type="submit" id="profileUpdateBtn" class="btn btn-primary">
                                                    <i class="mdi mdi-content-save mr-1"></i>
                                                    {{ !$operator || !$operator->nama_lengkap ? 'Simpan Profil' : 'Perbarui Profil' }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <h5 class="mb-4">Change Password</h5>

                                    <form id="changePasswordForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Confirm New
                                                Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                        </div>

                                        <div class="text-end mt-4">
                                            <button type="submit" id="passwordUpdateBtn" class="btn btn-primary">
                                                <i class="mdi mdi-lock-reset mr-1"></i>
                                                Change Password
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
                // Submit form dengan AJAX
                $('#editProfileForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    $('#profileUpdateBtn').attr('disabled', true).text('Saving...');

                    $.ajax({
                        url: _baseURL + '/api/profile-operator/',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            toastr.success(res.message || 'Profil berhasil diperbarui');

                            if (res.updatedData) {
                                $('[data-field="nama_lengkap"]').text(res.updatedData.nama_lengkap);
                                $('[data-field="jenis_kelamin"]').text(res.updatedData
                                    .jenis_kelamin);
                                $('[data-field="alamat"]').text(res.updatedData.alamat);
                            }

                            if (res.foto_url) {
                                $('#profilePic').attr('src', res.foto_url);
                            }

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
                            $('#profileUpdateBtn').attr('disabled', false).text('Save Changes');
                        }
                    });
                });

                // Preview foto profil sebelum upload
                $('#newProfilePhoto').on('change', function() {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profilePic').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                });

                // Handle change password form submission
                $('#changePasswordForm').on('submit', function(e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('#passwordUpdateBtn').attr('disabled', true).text('Processing...');

                    const formData = {
                        current_password: $('#current_password').val(),
                        new_password: $('#new_password').val(),
                        new_password_confirmation: $('#new_password_confirmation').val(),
                        _token: $('input[name="_token"]').val()
                    };

                    $.ajax({
                        url: _baseURL + '/api/student/change-password/',
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
                                toastr.error('An error occurred while changing your password.');
                            }
                        },
                        complete: function() {
                            $('#passwordUpdateBtn').attr('disabled', false).text('Change Password');
                        }
                    });
                });
            });
        </script>
    @endpush



@endsection
