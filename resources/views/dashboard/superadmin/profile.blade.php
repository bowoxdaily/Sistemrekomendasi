@extends('layout.app')

@section('title', 'Profil Super Admin')

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
                            <h4 class="mb-1">{{ $superAdmin->nama_lengkap ?? Auth::user()->name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="mdi mdi-account-star mr-1"></i>
                                Super Administrator
                            </p>
                            <p class="text-muted mb-3">
                                <i class="mdi mdi-email mr-1"></i>
                                {{ Auth::user()->email }}
                            </p>
                            <div class="badge badge-danger">
                                <i class="mdi mdi-shield-check mr-1"></i>
                                Status: Super Admin
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
                                        role="tab">Ubah Password</a>
                                </li>
                            </ul> <!-- Tab Content -->
                            <div class="tab-content" id="profileTabsContent">
                                <!-- Overview Tab -->
                                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                    <h5 class="mb-3">Informasi Super Admin</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Nama Lengkap</label>
                                                <p class="form-control-static">
                                                    {{ $superAdmin->nama_lengkap ?? Auth::user()->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Role</label>
                                                <p class="form-control-static">Super Administrator</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Jenis Kelamin</label>
                                                <p class="form-control-static">
                                                    {{ $superAdmin->jenis_kelamin ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Email</label>
                                                <p class="form-control-static">{{ Auth::user()->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Bergabung Sejak</label>
                                                <p class="form-control-static">
                                                    {{ Auth::user()->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <p class="form-control-static">
                                                    <span class="badge badge-danger">Super Admin</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Profile Tab -->
                                <div class="tab-pane fade" id="edit" role="tabpanel">
                                    <h5 class="mb-3">Edit Profil</h5>
                                    <form id="profileForm" action="{{ route('superadmin.profile.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nama_lengkap">Nama Lengkap <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_lengkap"
                                                        name="nama_lengkap"
                                                        value="{{ old('nama_lengkap', $superAdmin->nama_lengkap ?? Auth::user()->name) }}"
                                                        required>
                                                    @error('nama_lengkap')
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jenis_kelamin">Jenis Kelamin <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="jenis_kelamin" name="jenis_kelamin"
                                                        required>
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki"
                                                            {{ old('jenis_kelamin', $superAdmin->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                                            Laki-laki</option>
                                                        <option value="Perempuan"
                                                            {{ old('jenis_kelamin', $superAdmin->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                                            Perempuan</option>
                                                    </select>
                                                    @error('jenis_kelamin')
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        name="email" value="{{ old('email', Auth::user()->email) }}">
                                                    @error('email')
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="foto">Foto Profil</label>
                                                    <input type="file" class="form-control" id="foto"
                                                        name="foto" accept="image/*">
                                                    <small class="form-text text-muted">Format: JPG, PNG. Maksimal
                                                        2MB.</small>
                                                    @error('foto')
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save mr-1"></i>
                                                Simpan Perubahan
                                            </button>
                                            <button type="reset" class="btn btn-secondary ml-2">
                                                <i class="mdi mdi-refresh mr-1"></i>
                                                Reset
                                            </button>
                                        </div>
                                    </form>
                                </div> <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <h5 class="mb-3">Ubah Password</h5>
                                    <form id="passwordForm" action="{{ route('superadmin.password.update') }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label for="current_password">Password Saat Ini <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" required>
                                            @error('current_password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password">Password Baru <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" required>
                                            <small class="form-text text-muted">Password minimal 8 karakter.</small>
                                            @error('new_password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password_confirmation">Konfirmasi Password Baru <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                            @error('new_password_confirmation')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="mdi mdi-lock-reset mr-1"></i>
                                                Ubah Password
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle profile form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin mr-1"></i>Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Profil berhasil diperbarui',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload page to show updated data
                                window.location.reload();
                            });
                        } else {
                            throw new Error(response.message || 'Terjadi kesalahan');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join(', ');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle password form submission
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin mr-1"></i>Mengubah...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Clear form
                            $('#passwordForm')[0].reset();

                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Password berhasil diubah',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            throw new Error(response.message || 'Terjadi kesalahan');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat mengubah password';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join(', ');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // File input preview
            $('#foto').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.profile-image-wrapper img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .form-control-static {
            background-color: #f8f9fa;
            border: 1px solid #e3e6f0;
            padding: 0.375rem 0.75rem;
            border-radius: 0.35rem;
            color: #6e707e;
        }

        .nav-tabs-line {
            border-bottom: 1px solid #e3e6f0;
        }

        .nav-tabs-line .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: #6e707e;
        }

        .nav-tabs-line .nav-link:hover {
            border-bottom-color: #4e73df;
            color: #4e73df;
        }

        .nav-tabs-line .nav-link.active {
            border-bottom-color: #4e73df;
            color: #4e73df;
            background-color: transparent;
        }

        .profile-image-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-image-wrapper img {
            border: 4px solid #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .nip-readonly {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .nip-readonly:focus {
            background-color: #f8f9fa !important;
            box-shadow: none;
        }

        .form-group label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }

        .btn {
            font-weight: 600;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }

        .text-danger {
            color: #e74a3b !important;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    </style>
@endpush
