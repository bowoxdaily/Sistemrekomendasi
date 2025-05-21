@extends('layout.app')

@section('title', 'Pengaturan Umum')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Umum</h4>
                    <p class="card-description">
                        Konfigurasi dasar website
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('operator.settings.general.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="site_name">Nama Website</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                value="{{ $settings['site_name'] ?? 'Sistem Tracer Study & Rekomendasi Karir' }}" required>
                            <small class="form-text text-muted">Nama website yang akan ditampilkan pada judul browser dan header.</small>
                        </div>

                        <div class="form-group">
                            <label for="site_description">Deskripsi Website</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? 'Sistem Pelacakan Alumni dan Rekomendasi Karir untuk SMK' }}</textarea>
                            <small class="form-text text-muted">Deskripsi singkat tentang website ini.</small>
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                value="{{ $settings['meta_keywords'] ?? 'tracer study, alumni, karir, smk' }}">
                            <small class="form-text text-muted">Kata kunci untuk optimasi mesin pencari, pisahkan dengan koma.</small>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
