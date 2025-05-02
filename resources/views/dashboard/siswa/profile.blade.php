@extends('layout.app')

@section('title', 'Profil Siswa')

@section('content')
<div class="main-panel">        
    <div class="content-wrapper">
        <div class="row">
            <!-- Kartu Profil (Kiri) -->
            <div class="col-md-4 grid-margin">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-image-wrapper mb-4">
                            <img 
                                src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}" 
                                alt="Foto Profil" 
                                class="rounded-circle img-fluid"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >
                        </div>
                        <h4 class="mb-1">{{ $student->nama_lengkap }}</h4>
                        <p class="text-muted mb-3">{{ $student->user->role ?? 'Web Designer' }}</p>
                        
                        <!-- Social Media Icons -->
                        <div class="d-flex justify-content-center mt-3">
                            <a href="#" class="text-secondary mx-2"><i class="mdi mdi-twitter h5"></i></a>
                            <a href="#" class="text-secondary mx-2"><i class="mdi mdi-facebook h5"></i></a>
                            <a href="#" class="text-secondary mx-2"><i class="mdi mdi-instagram h5"></i></a>
                            <a href="#" class="text-secondary mx-2"><i class="mdi mdi-linkedin h5"></i></a>
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
                                <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab">Edit Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">Change Password</a>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="profileTabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <h5 class="mt-4 mb-3">Profile Details</h5>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Nama Lengkap</div>
                                    <div class="col-md-8 text-primary" data-field="nama_lengkap">{{ $student->nama_lengkap }}</div>
                                </div>   
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">NISN</div>
                                    <div class="col-md-8 text-primary" data-field="nisn">{{ $student->nisn }}</div>
                                </div>   
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Tempat Lahir</div>
                                    <div class="col-md-8 text-primary" data-field="tempat_lahir">{{ $student->tempat_lahir }}</div>
                                </div>   
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Tanggal Lahir</div>
                                    <div class="col-md-8 text-primary" data-field="tanggal_lahir">{{ $student->tanggal_lahir }}</div>
                                </div>   
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Jenis Kelamin</div>
                                    <div class="col-md-8 text-primary" data-field="jenis_kelamin">{{ $student->jenis_kelamin }}</div>
                                </div>   
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Alamat</div>
                                    <div class="col-md-8 text-primary" data-field="alamat">{{ $student->alamat ?? 'Kendayakan, Kec. Terisi, Kabupaten Indramayu, Jawa Barat 45262' }}</div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">No telp</div>
                                    <div class="col-md-8 text-primary" data-field="no_telp">{{ $student->user->no_telp ?? '+628277XXX' }}</div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Email</div>
                                    <div class="col-md-8 text-primary" data-field="email">{{ $student->user->email ?? 'Student@smk1terisi.sch.id' }}</div>
                                </div>
                            </div>
                            
                            <!-- Edit Profile Tab -->
                            <div class="tab-pane fade" id="edit" role="tabpanel">
                                <form id="editProfileForm" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <!-- Profile Image -->
                                    <div class="form-group row mb-4">
                                        <label class="col-md-3 col-form-label text-md-right">Profile Image</label>
                                        <div class="col-md-9">
                                            <div class="text-start">
                                                <div class="profile-image-container mb-3">
                                                    <img 
                                                        id="profilePic" 
                                                        src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}" 
                                                        alt="Profile Image"
                                                        style="width: 120px; height: 120px; object-fit: cover;"
                                                    >
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm mb-2" onclick="document.getElementById('newProfilePhoto').click()">
                                                    <i class="mdi mdi-upload"></i> Upload
                                                </button>
                                                <input type="file" name="foto" id="newProfilePhoto" class="d-none" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- NISN -->
                                    <div class="form-group row mb-3">
                                        <label for="nisn" class="col-md-3 col-form-label text-md-right">NISN</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="nisn" name="nisn" value="{{ $student->nisn }}" readonly>
                                            <small class="text-muted">NISN tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Full Name -->
                                    <div class="form-group row mb-3">
                                        <label for="nama_lengkap" class="col-md-3 col-form-label text-md-right">Nama Lengkap</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ $student->nama_lengkap }}">
                                        </div>
                                    </div>
                                    
                                    <!-- Tempat Lahir -->
                                    <div class="form-group row mb-3">
                                        <label for="tempat_lahir" class="col-md-3 col-form-label text-md-right">Tempat Lahir</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ $student->tempat_lahir }}">
                                        </div>
                                    </div>
                                    
                                    <!-- Tanggal Lahir -->
                                    <div class="form-group row mb-3">
                                        <label for="tanggal_lahir" class="col-md-3 col-form-label text-md-right">Tanggal Lahir</label>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ $student->tanggal_lahir }}">
                                        </div>
                                    </div>
                                    
                                    <!-- Jenis Kelamin -->
                                    <div class="form-group row mb-3">
                                        <label for="jenis_kelamin" class="col-md-3 col-form-label text-md-right">Jenis Kelamin</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                                <option value="Laki-laki" {{ $student->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="Perempuan" {{ $student->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="form-group row mb-3">
                                        <label for="alamat" class="col-md-3 col-form-label text-md-right">Alamat</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ $student->alamat }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div class="form-group row mb-3">
                                        <label for="no_telp" class="col-md-3 col-form-label text-md-right">No. Telp</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $student->user->no_telp }}">
                                        </div>
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="form-group row mb-3">
                                        <label for="email" class="col-md-3 col-form-label text-md-right">Email</label>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $student->user->email }}">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row mb-0">
                                        <div class="col-md-9 offset-md-3">
                                            <button type="submit" id="profileUpdateBtn" class="btn btn-primary">Save Changes</button>
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
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                        <button type="submit" id="passwordUpdateBtn" class="btn btn-primary">Change Password</button>
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
    $(document).ready(function () {
        // Submit form dengan AJAX
        $('#editProfileForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            $('#profileUpdateBtn').attr('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('api.student.profile.update') }}",
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    toastr.success(res.message || 'Profil berhasil diperbarui');

                    // Update data yang terlihat di halaman jika tidak ingin reload
                    if (res.updatedData) {
                        $('[data-field="nama_lengkap"]').text(res.updatedData.nama_lengkap);
                        $('[data-field="tempat_lahir"]').text(res.updatedData.tempat_lahir);
                        $('[data-field="tanggal_lahir"]').text(res.updatedData.tanggal_lahir);
                        $('[data-field="jenis_kelamin"]').text(res.updatedData.jenis_kelamin);
                        $('[data-field="alamat"]').text(res.updatedData.alamat);
                        $('[data-field="no_telp"]').text(res.updatedData.no_telp);
                        $('[data-field="email"]').text(res.updatedData.email);
                    }

                    if (res.foto_url) {
                        $('#profilePic').attr('src', res.foto_url);
                    }

                    // Reload halaman setelah delay singkat agar toastr terlihat
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function () {
                    $('#profileUpdateBtn').attr('disabled', false).text('Save Changes');
                }
            });
        });

        // Preview foto profil sebelum upload
        $('#newProfilePhoto').on('change', function () {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#profilePic').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
        
        // Handle change password form submission
        $('#changePasswordForm').on('submit', function (e) {
            e.preventDefault();
            
            // Reset previous error messages
            $('.is-invalid').removeClass('is-invalid');
            
            // Disable button and show loading state
            $('#passwordUpdateBtn').attr('disabled', true).text('Processing...');
            
            // Get form data
            const formData = {
                current_password: $('#current_password').val(),
                new_password: $('#new_password').val(),
                new_password_confirmation: $('#new_password_confirmation').val(),
                _token: $('input[name="_token"]').val()
            };
            
            // Send AJAX request
            $.ajax({
                url: "{{ route('api.student.change-password') }}",
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        // Show success message
                        toastr.success(response.message);
                        
                        // Clear form
                        $('#changePasswordForm')[0].reset();
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        
                        if (errors) {
                            // Display each error with toastr
                            for (const field in errors) {
                                toastr.error(errors[field][0]);
                                $(`#${field}`).addClass('is-invalid');
                            }
                        } else if (xhr.responseJSON.message) {
                            // Single error message
                            toastr.error(xhr.responseJSON.message);
                        }
                    } else {
                        // General error
                        toastr.error('An error occurred while changing your password.');
                    }
                },
                complete: function () {
                    // Re-enable button
                    $('#passwordUpdateBtn').attr('disabled', false).text('Change Password');
                }
            });
        });
    });
</script>
@endpush


@endsection