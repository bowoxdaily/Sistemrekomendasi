<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Skydash Admin')</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/js/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/vertical-layout-light/style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    @yield('styles')
</head>

<body>
    <div class="container-scroller">
        <!-- Top Navbar -->
        @include('layout.navbar')

        <!-- Page Content Wrapper -->
        <div class="container-fluid page-body-wrapper">
            <!-- Theme Settings Panel -->
            @include('layout.settings-panel')

            <!-- Sidebar -->
            @include('layout.sidebar')

            <!-- Main Panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                <!-- Footer -->
                @include('layout.footer')
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- Core plugins JS -->
    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- Plugin js for this page -->
    <script src="{{ asset('admin/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('admin/js/dataTables.select.min.js') }}"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/todolist.js') }}"></script>

    <!-- Custom js for this page-->
    <script src="{{ asset('admin/js/dashboard.js') }}"></script>
    <script src="{{ asset('admin/js/Chart.roundedBarCharts.js') }}"></script>
    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- End custom js for this page-->
    @stack('scripts')

    <!-- Add this just before the closing </body> tag in your main layout file -->

    <!-- Logout Confirmation Modal -->
    
</div>


<script>
    $(document).ready(function() {
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Function to load student count
        function loadStudentCount() {
            $.ajax({
                url: 'api/get-student-count',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update the count
                    $('#student-count').text(data.current_count);
                    
                    // Format percentage with sign and color
                    let percentText = data.percentage_change + '% (' + data.days + ' days)';
                    if (data.percentage_change > 0) {
                        percentText = '↑ ' + percentText;
                        $('#percentage-change').css('color', '#a3ffb2');
                    } else if (data.percentage_change < 0) {
                        percentText = '↓ ' + percentText;
                        $('#percentage-change').css('color', '#ffadad');
                    }
                    
                    $('#percentage-change').text(percentText);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching student count:', error);
                    console.log(xhr.responseText);
                    $('#student-count').text('Error');
                    $('#percentage-change').text('Could not load data');
                }
            });
        }
        
        // Load initial data
        loadStudentCount();
        
        // Refresh every 5 minutes (optional)
        setInterval(loadStudentCount, 300000);
    });
</script>

</body>

</html>
