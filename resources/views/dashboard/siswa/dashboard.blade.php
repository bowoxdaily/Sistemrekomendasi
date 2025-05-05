<!-- views/dashboard.blade.php -->
@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Check if student profile is complete (only for students)
            @if (Auth::check() && Auth::user()->role === 'siswa')
                checkStudentProfile();
            @endif

            // Function to check if student profile is complete
            function checkStudentProfile() {
                $.ajax({
                    url: '{{ route('api.student.profile.check') }}',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (!response.complete) {
                            // Tampilkan alert
                            $('#profile-alert').removeClass('d-none');

                            // Atau jika kamu tetap ingin pakai modal juga:
                            $('#profileModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking profile:', xhr);
                    }
                });
            }


            // Handle "Lengkapi Profil" button click
            $('#goToProfileBtn').click(function() {
                window.location.href = '{{ route('student.profile.edit') }}';
            });

            // Load student count statistics via AJAX

        });
    </script>
@endpush
