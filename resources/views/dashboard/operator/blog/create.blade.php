@extends('layout.app')

@section('title', 'Dashboard | Tambah Blog')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Tambah Blog Baru</h4>
                                <a href="{{ route('operator.blog.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                            
                            <div id="alert-container"></div>
                            
                            <form id="blogForm" class="forms-sample">
                                @csrf
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="title">Judul <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Judul blog" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="editor">Konten <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="editor" name="content" rows="15" required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h5 class="card-title">Pengaturan Publikasi</h5>
                                                
                                                <div class="form-group">
                                                    <label for="category">Kategori</label>
                                                    <input type="text" class="form-control" id="category" name="category" 
                                                        placeholder="Masukkan kategori" list="category-list">
                                                    <datalist id="category-list">
                                                        <!-- Categories will be loaded here -->
                                                    </datalist>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="author">Penulis</label>
                                                    <input type="text" class="form-control" id="author" name="author" placeholder="Nama penulis">
                                                    <small class="text-muted">Kosongkan untuk menggunakan nama user</small>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="image">Gambar Utama</label>
                                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                    <small class="text-muted">Ukuran maksimal: 2MB. Format: JPG, PNG</small>
                                                    
                                                    <div id="image-preview" class="mt-3 text-center d-none">
                                                        <img src="" alt="Preview" class="img-fluid img-thumbnail">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-check form-check-primary mt-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="is_published" id="is_published">
                                                        Publikasikan sekarang
                                                    </label>
                                                </div>
                                                
                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary btn-block" id="saveBtn">
                                                        <i class="mdi mdi-content-save"></i> Simpan Blog
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .ck-editor__editable {
            min-height: 500px;
        }
        
        #image-preview img {
            max-height: 200px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize rich text editor with custom toolbar
            let editor;
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo' ]
                })
                .then(newEditor => {
                    editor = newEditor;
                })
                .catch(error => {
                    console.error(error);
                });

            // Load categories for datalist
            $.ajax({
                url: _baseURL + 'api/blog-categories',
                method: 'GET',
                success: function(response) {
                    const datalist = $('#category-list');
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(category => {
                            datalist.append(`<option value="${category.name}">`);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load categories');
                }
            });

            // Image preview
            $('#image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').removeClass('d-none');
                        $('#image-preview img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#image-preview').addClass('d-none');
                }
            });

            // Form submission with AJAX
            $('#blogForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous error messages
                $('#alert-container').empty();
                
                // Basic form validation
                const title = $('#title').val().trim();
                if (!title) {
                    $('#alert-container').html('<div class="alert alert-danger">Judul blog tidak boleh kosong</div>');
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    return;
                }
                
                if (editor.getData().trim() === '') {
                    $('#alert-container').html('<div class="alert alert-danger">Konten blog tidak boleh kosong</div>');
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    return;
                }
                
                const submitBtn = $('#saveBtn');
                submitBtn.prop('disabled', true)
                    .html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                // Create FormData for file uploads
                const formData = new FormData(this);
                
                // Add editor content
                formData.append('content', editor.getData());
                
                // Add published status
                formData.append('is_published', $('#is_published').is(':checked') ? 1 : 0);
                
                // Generate slug from title - ensure it's valid
                const slug = title.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-')
                    .replace(/^-+|-+$/g, ''); // Trim dashes from beginning and end
                
                // If slug is empty (e.g., if title contained only special characters), use a timestamp
                formData.append('slug', slug || 'blog-' + Date.now());
                
                // Debug to console what's being sent
                console.log('Submitting blog with title:', title);
                console.log('Slug generated:', slug || 'blog-' + Date.now());
                
                // Make sure CSRF token is included
                const token = $('meta[name="csrf-token"]').attr('content');
                if (!token) {
                    console.error('CSRF token not found');
                }
                
                // Show loading indicator
                Swal.fire({
                    title: 'Menyimpan Blog',
                    html: 'Mohon tunggu, sedang menyimpan data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Direct AJAX call using jQuery
                $.ajax({
                    url: _baseURL + 'api/blog',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Success response:', response);
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Blog berhasil disimpan',
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                // Redirect to blog index page
                                window.location.href = "{{ route('operator.blog.index') }}";
                            });
                        } else {
                            // Display error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan blog. Silakan coba lagi.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error response:', xhr.responseText);
                        console.error('Status:', status);
                        console.error('Error:', error);
                        
                        Swal.close();
                        
                        // Display error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan blog. Silakan coba lagi.'
                        });
                        
                        submitBtn.prop('disabled', false)
                            .html('<i class="mdi mdi-content-save"></i> Simpan Blog');
                    }
                });
            });
            
            // Add a direct click handler for the save button as a fallback
            $('#saveBtn').on('click', function(e) {
                if (!$(this).prop('disabled')) {
                    $('#blogForm').submit();
                }
            });
        });
    </script>
@endpush
