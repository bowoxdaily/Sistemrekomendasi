@extends('layout.app')

@section('title', 'Pengaturan Sekolah')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Informasi Sekolah</h4>
                    <p class="card-description">
                        Informasi tentang sekolah yang akan ditampilkan di website
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('operator.settings.school.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="school_name">Nama Sekolah</label>
                            <input type="text" class="form-control" id="school_name" name="school_name" 
                                value="{{ $settings['school_name'] ?? 'SMKN 1 Terisi' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="school_address">Alamat Sekolah</label>
                            <textarea class="form-control" id="school_address" name="school_address" rows="3" required>{{ $settings['school_address'] ?? 'Jl. Raya Terisi No. 1, Indramayu' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_phone">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="school_phone" name="school_phone" 
                                        value="{{ $settings['school_phone'] ?? '(021) 1234567' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_email">Email Sekolah</label>
                                    <input type="email" class="form-control" id="school_email" name="school_email" 
                                        value="{{ $settings['school_email'] ?? 'info@smkn1terisi.sch.id' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_website">Website Sekolah</label>
                            <input type="url" class="form-control" id="school_website" name="school_website" 
                                value="{{ $settings['school_website'] ?? 'https://smkn1terisi.sch.id' }}">
                        </div>

                        <div class="form-group">
                            <label for="school_description">Tentang Sekolah</label>
                            <textarea class="form-control" id="school_description" name="school_description" rows="5">{{ $settings['school_description'] ?? 'SMK Negeri 1 Terisi adalah sekolah menengah kejuruan unggulan di Indramayu yang fokus pada pendidikan berkualitas dan pengembangan karir siswa.' }}</textarea>
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
