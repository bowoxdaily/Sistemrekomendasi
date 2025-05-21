@extends('layout.app')

@section('title', 'Pengaturan Logo')

@php
    use App\Models\Setting;
@endphp

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Logo</h4>
                    <p class="card-description">
                        Ubah logo dan favicon website
                    </p>

                    <form action="{{ route('operator.settings.logo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo Utama</label>
                                    <div class="mb-3">
                                        <img src="{{ $logo }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    <input type="file" class="form-control-file" id="logo" name="logo">
                                    @error('logo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2MB.
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="logo_alt_text">Alt Text untuk Logo</label>
                                    <input type="text" class="form-control" id="logo_alt_text" name="logo_alt_text" 
                                        value="{{ Setting::get('logo_alt_text', 'Logo Sistem') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <div class="mb-3">
                                        <img src="{{ $favicon }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px;" id="favicon-display">
                                    </div>
                                    <input type="file" class="form-control-file" id="favicon" name="favicon">
                                    @error('favicon')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Format yang didukung: ICO, PNG. Ukuran maksimal: 1MB. Disarankan 16x16 atau 32x32 piksel.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline"></i>
                                    Logo akan ditampilkan di header website dan pada halaman login.
                                    Favicon akan ditampilkan pada tab browser.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light ml-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toastr notifications
        @if(session('success'))
            toastr.success('{{ session('success') }}', 'Sukses');
        @endif
        
        @if(session('error'))
            toastr.error('{{ session('error') }}', 'Error');
        @endif
        
        // Update existing logo image with preview
        $('#logo').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('img[alt="Logo"]').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Update existing favicon image with preview
        $('#favicon').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#favicon-display').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush