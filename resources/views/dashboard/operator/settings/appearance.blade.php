@extends('layout.app')

@section('title', 'Pengaturan Tampilan')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pengaturan Tampilan</h4>
                    <p class="card-description">
                        Kustomisasi tampilan website
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('operator.settings.appearance.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="primary_color">Warna Utama</label>
                            <div class="input-group">
                                <input type="color" class="form-control" id="primary_color" name="primary_color" 
                                    value="{{ $settings['primary_color'] ?? '#4B49AC' }}">
                                <div class="input-group-append">
                                    <input type="text" class="form-control" id="primary_color_hex" 
                                        value="{{ $settings['primary_color'] ?? '#4B49AC' }}" style="width: 100px;">
                                </div>
                            </div>
                            <small class="form-text text-muted">Warna utama digunakan untuk tombol, link, dan elemen interaksi lainnya.</small>
                        </div>

                        <div class="form-group mt-4">
                            <label>Preview Tampilan</label>
                            <div class="appearance-preview mt-3 p-4 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button id="preview-button" class="btn btn-block" style="background-color: {{ $settings['primary_color'] ?? '#4B49AC' }}; color: white;">Tombol Contoh</button>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="border-color: {{ $settings['primary_color'] ?? '#4B49AC' }};">
                                            <div class="card-header" style="background-color: {{ $settings['primary_color'] ?? '#4B49AC' }}; color: white;">
                                                Header Card
                                            </div>
                                            <div class="card-body">
                                                <p>Isi card dengan <a href="#" id="preview-link" style="color: {{ $settings['primary_color'] ?? '#4B49AC' }};">tautan</a> berwarna.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert" style="background-color: rgba({{ hexToRgb($settings['primary_color'] ?? '#4B49AC', 0.2) }}); color: {{ $settings['primary_color'] ?? '#4B49AC' }}; border-color: {{ $settings['primary_color'] ?? '#4B49AC' }};">
                                            Alert dengan warna utama
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label for="font_family">Font Utama</label>
                            <select class="form-control" id="font_family" name="font_family">
                                <option value="system-ui" {{ ($settings['font_family'] ?? '') == 'system-ui' ? 'selected' : '' }}>Default (System UI)</option>
                                <option value="'Roboto', sans-serif" {{ ($settings['font_family'] ?? '') == "'Roboto', sans-serif" ? 'selected' : '' }}>Roboto</option>
                                <option value="'Open Sans', sans-serif" {{ ($settings['font_family'] ?? '') == "'Open Sans', sans-serif" ? 'selected' : '' }}>Open Sans</option>
                                <option value="'Poppins', sans-serif" {{ ($settings['font_family'] ?? '') == "'Poppins', sans-serif" ? 'selected' : '' }}>Poppins</option>
                                <option value="'Montserrat', sans-serif" {{ ($settings['font_family'] ?? '') == "'Montserrat', sans-serif" ? 'selected' : '' }}>Montserrat</option>
                            </select>
                            <small class="form-text text-muted">Font yang akan digunakan untuk seluruh website.</small>
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Sync color input and text
        $('#primary_color').on('input', function() {
            const colorValue = $(this).val();
            $('#primary_color_hex').val(colorValue);
            updatePreview(colorValue);
        });
        
        $('#primary_color_hex').on('input', function() {
            const colorValue = $(this).val();
            $('#primary_color').val(colorValue);
            updatePreview(colorValue);
        });
        
        // Update preview elements
        function updatePreview(color) {
            // Convert hex to rgba for the alert background
            const rgb = hexToRgb(color);
            const rgbaBackground = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.2)`;
            
            $('#preview-button').css('background-color', color);
            $('.card-header').css('background-color', color);
            $('.card').css('border-color', color);
            $('#preview-link').css('color', color);
            $('.alert').css({
                'background-color': rgbaBackground,
                'color': color,
                'border-color': color
            });
        }
        
        // Helper function to convert hex to rgb
        function hexToRgb(hex) {
            // Remove # if present
            hex = hex.replace('#', '');
            
            // Parse the hex values
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            
            return { r, g, b };
        }
    });
    
    // PHP function equivalent for JavaScript
    function hexToRgb(hex, alpha = 1) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        return `${r}, ${g}, ${b}, ${alpha}`;
    }
</script>
@endpush

@php
// Function to convert hex to RGB for PHP
function hexToRgb($hex, $alpha = 1) {
    $hex = str_replace('#', '', $hex);
    
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return "$r, $g, $b";
}
@endphp
