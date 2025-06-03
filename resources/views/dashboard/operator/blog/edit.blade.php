@extends('layout.app')

@section('title', 'Dashboard | Edit Blog')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Edit Blog</h4>
                                <a href="{{ route('operator.blog.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                            
                            <div id="alert-container"></div>
                            
                            <form id="blogForm" class="forms-sample">
                                @csrf
                                <input type="hidden" id="blog_id" value="{{ $blogId }}">
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
                                                    
                                                    <div id="image-preview" class="mt-3 text-center">
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
                                                        <i class="mdi mdi-content-save"></i> Perbarui Blog
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
            console.log("Document ready for blog edit page"); // Basic debugging
            const blogId = $('#blog_id').val();
            console.log("Blog ID for editing:", blogId);
            
            // Initialize rich text editor with custom toolbar
            let editor;
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo' ]
                })
                .then(newEditor => {
                    editor = newEditor;
                    // Load blog data after editor is ready
                    loadBlogData();
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
                }
            });

            // Load blog data for editing
            function loadBlogData() {
                console.log("Loading blog data for ID:", blogId); // Debug log
                
                $.ajax({
                    url: _baseURL + 'api/blog/' + blogId,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("Blog data loaded:", response); // Debug log
                        
                        if (response.success && response.data) {
                            const blog = response.data;
                            
                            // Set form values
                            $('#title').val(blog.title);
                            $('#author').val(blog.author);
                            $('#category').val(blog.category);
                            $('#is_published').prop('checked', blog.is_published);
                            
                            // Set editor content
                            editor.setData(blog.content);
                            
                            // Show image preview if available
                            if (blog.image) {
                                $('#image-preview').removeClass('d-none');
                                $('#image-preview img').attr('src', _baseURL + blog.image);
                            } else {
                                $('#image-preview').addClass('d-none');
                            }
                        } else {
                            toastr.error('Gagal memuat data blog');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading blog data:", xhr); // Debug log
                        toastr.error('Gagal memuat data blog');
                        setTimeout(function() {
                            window.location.href = "{{ route('operator.blog.index') }}";
                        }, 1000);
                    }
                });
            }

            // Form submission with AJAX - updating for better reliability
            $('#blogForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
            
            // Add direct click handler for the save button as a backup
            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                console.log("Save button clicked directly");
                submitForm();
            });
            
            // Extracted form submission logic for reuse
            function submitForm() {
                console.log("Submitting blog update form");
                const submitBtn = $('#saveBtn');
                
                // Disable button and show loading indicator
                submitBtn.prop('disabled', true)
                    .html('<i class="mdi mdi-loading mdi-spin"></i> Memperbarui...');
                
                // Check if form is valid
                if (!$('#title').val().trim()) {
                    toastr.warning('Judul blog tidak boleh kosong');
                    submitBtn.prop('disabled', false)
                        .html('<i class="mdi mdi-content-save"></i> Perbarui Blog');
                    return;
                }
                
                if (!editor.getData().trim()) {
                    toastr.warning('Konten blog tidak boleh kosong');
                    submitBtn.prop('disabled', false)
                        .html('<i class="mdi mdi-content-save"></i> Perbarui Blog');
                    return;
                }

                // Create FormData for file uploads
                const formData = new FormData(document.getElementById('blogForm'));
                
                // Add editor content
                formData.append('content', editor.getData());
                
                // Add published status
                formData.append('is_published', $('#is_published').is(':checked') ? 1 : 0);
                
                // Add method override for PUT
                formData.append('_method', 'PUT');
                
                // Check CSRF token
                const token = $('meta[name="csrf-token"]').attr('content');
                if (!token) {
                    console.error("CSRF token missing");
                    toastr.error("CSRF token not found. Please refresh the page.");
                    submitBtn.prop('disabled', false)
                        .html('<i class="mdi mdi-content-save"></i> Perbarui Blog');
                    return;
                }
                
                // Show loading indicator for better UX
                Swal.fire({
                    title: 'Menyimpan Perubahan',
                    html: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: _baseURL + 'api/blog/' + blogId,
                    type: 'POST', // Always POST for FormData
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log("Update success:", response);
                        Swal.close();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Blog berhasil diperbarui',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.href = "{{ route('operator.blog.index') }}";
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Update error:", xhr.responseText);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        
                        Swal.close();
                        
                        let errorMessage = 'Terjadi kesalahan saat memperbarui blog.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                            Object.values(xhr.responseJSON.errors).forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                            errorHtml += '</ul></div>';
                            $('#alert-container').html(errorHtml);
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                        } else {
                            toastr.error(errorMessage);
                        }
                        
                        submitBtn.prop('disabled', false)
                            .html('<i class="mdi mdi-content-save"></i> Perbarui Blog');
                    }
                });
            }
        });
    </script>
@endpush
