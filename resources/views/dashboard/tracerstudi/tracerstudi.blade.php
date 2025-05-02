@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Data Tracer Studi</h4>
              
              
              <!-- Search Bar -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    
                    <div class="input-group-append">
                      
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="table-responsive">
                <table class="table" id="dataTable">
                  <thead>
                    <tr>
                      <th>Profile</th>
                      <th>VatNo.</th>
                      <th>Created</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Jacob</td>
                      <td>53275531</td>
                      <td>12 May 2017</td>
                      <td><label class="badge badge-danger">Pending</label></td>
                    </tr>
                    <tr>
                      <td>Messsy</td>
                      <td>53275532</td>
                      <td>15 May 2017</td>
                      <td><label class="badge badge-warning">In progress</label></td>
                    </tr>
                    <tr>
                      <td>John</td>
                      <td>53275533</td>
                      <td>14 May 2017</td>
                      <td><label class="badge badge-info">Fixed</label></td>
                    </tr>
                    <tr>
                      <td>Peter</td>
                      <td>53275534</td>
                      <td>16 May 2017</td>
                      <td><label class="badge badge-success">Completed</label></td>
                    </tr>
                    <tr>
                      <td>Dave</td>
                      <td>53275535</td>
                      <td>20 May 2017</td>
                      <td><label class="badge badge-warning">In progress</label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<!-- Tambahkan script untuk search functionality -->
@section('scripts')
<script>
  $(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#dataTable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true
    });
    
    // Search functionality
    $('#searchInput').keyup(function() {
      table.search($(this).val()).draw();
    });
    
    $('#searchButton').click(function() {
      table.search($('#searchInput').val()).draw();
    });
  });
</script>
@endsection

@endsection