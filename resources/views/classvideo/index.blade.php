@extends('layouts.app')
@section('title', 'Class Videos')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
          </div>
          @endif
          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
          </div>
          @endif
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <div class="col-md-8 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Class Videos</h6>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{ route('classvideo.upload.form') }}" class="btn btn-primary btn-block">Class Video Upload</a>
                </div>

                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{ route('classvideo.create') }}" class="btn btn-primary btn-block">Add Class Video</a>
                </div>
              </div>

              <form method="post" class="my-3" id="myForm" enctype="multipart/form-data">
                @csrf
                <h6 class="col-deep-purple">Class Videos Schedule</h6>
                <div class="row">
                  <div class="form-group col-lg-3 mt-6">
                    <label>Start Datetime</label>
                    <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm" required>
                  </div>

                  <!-- End Date -->
                  <div class="form-group col-lg-3">
                    <label>End Datetime</label>
                    <input type="text" id="end_at" name="end_at" class="datetime-picker form-control form-control-sm" required>
                    <div id="end_at_error" class="text-danger"></div>
                  </div>

                  <!-- Submit Button -->
                  <div class="form-group col-lg-3 ">
                    <button type="submit" class="btn btn-primary m-t-25">Update Schedule</button>
                  </div>
                </div>
              </form>

               <form action="{{ route('classvideo.index') }}" method="get">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <select name="coaching_type" class="select2" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}" @selected(request('coaching_type')==$row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-3">
                    <select name="course" class="select2" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected(request('course')==$row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>
                  
                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>

              <div class="col-md-6 col-sm-12 mb-3">
                <button type="button" class="btn btn-danger mt-3" id="deleteSelected">
                  <i class="fas fa-trash"></i> Delete Selected
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="checkAll" /></th>
                      <th>Coaching Type</th>
                      <th>Date</th>
                      <th>Course</th>
                      <th>Subject</th>
                      <th>Period</th>
                      <th>Chapter</th>
                      <th>Part</th>
                      <th>Video Id</th>
                      <th>Start At</th>
                      <th>End At</th>
                      <th>Seen Log</th>
                      <th>Edit</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($classvideos as $classvideo)
                    <tr>
                      <td><input type="checkbox" class='ids' name="ids[]" value="{{$classvideo->id}}" /></td>
                      <td>{{ $classvideo->coaching_type }}</td>
                      <td>{{ $classvideo->date }}</td>
                      <td>{{ $classvideo->course }}</td>
                      <td>{{ $classvideo->subject }}</td>
                      <td>{{ $classvideo->period }}</td>
                      <td>{{ $classvideo->chapter }}</td>
                      <td>{{ $classvideo->part }}</td>
                      <td>{{ $classvideo->video_id }}</td>
                      <td>{{ $classvideo->start_at }}</td>
                      <td>{{ $classvideo->end_at }}</td>
                      <td>
                        <button type="button" class="btn btn-primary logbtn" data-toggle="modal" data-target="#seenlog" data-action="{{$classvideo->chapter}}" data-module="Class Videos"> <i class="fas fa-eye"></i></button>
                      </td>
                      <td>
                        <a href="{{ route('classvideo.edit', $classvideo->id) }}" class="btn btn-primary">
                          <i class="fas fa-edit"></i>
                        </a>
                      </td>
                      <td>
                        <form action="{{ route('classvideo.destroy', $classvideo->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Log Modal -->
<div class="modal fade" id="seenlog" tabindex="-1" role="dialog" aria-labelledby="seenlogLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="seenlogLabel">Seen Log - <span id="logTitle"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-sm" id="logTable" style="width: 100%;">
            <thead>
              <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Section</th>
                <th>Action</th>
                <th>Seen At</th>
              </tr>
            </thead>
            <tbody id="logBody">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
  $(document).ready(function() {
      $('#myTable').DataTable({
        pageLength: 25,
        searching: false,
      });
  });

  const logTable = $('#logTable').DataTable({
    paging: false,
    dom: 'Bfrtip',
    searching: false,
    buttons: [
     'excelHtml5'
    ]
  });

  $(document).on('click', '.logbtn', function() {
    var action = $(this).data('action');
    var modules = $(this).data('module');
    $('#logTitle').text(action);
    logTable.clear().draw();
    $('#logBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    $.post("{{ route('student.getlogactivity') }}", {
      action: action,
      module: modules,
      _token: "{{ csrf_token() }}"
    },
    function(data, status) {
      logTable.clear();
      if(data.success && data.logs.length > 0) {
        data.logs.forEach(log => {
          logTable.row.add([
            log.student_name || '',
            log.student_id || '',
            log.section || '',
            log.action || '',
            log.created_at ? new Date(log.created_at).toLocaleString() : ''
          ]);
        });
      }
      logTable.draw();
    });
  });
  
  flatpickr(".datetime-picker", {
      enableTime: true,
      allowInput: true,
      dateFormat: "Y-m-d H:i",
      plugins: [
          new confirmDatePlugin({
              confirmText: "OK",
              showAlways: false,
              theme: "light"
          })
      ]
  });
  
  $('#checkAll').click(function() {
      if (this.checked) {
          $('.ids').each(function() {
              this.checked = true;
          });
      } else {
          $('.ids').each(function() {
              this.checked = false;
          });
      }
  });
  
  
  $("#myForm").submit(function(e) {
      e.preventDefault();
      if($('.ids:checked').length == 0) {
          alert("Please select at least one video");
          return false;
      }
      var formData = new FormData(this);
      $('.ids:checked').each(function(index) {
          formData.append(`ids[${index}]`, $(this).val());
      });
      
      $.ajax({
          url: "{{ route('classvideo.schedule') }}",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
              if (response.success) {
                  alert(response.message);
                  location.reload();
              } else {
                  alert(response.message);
                  location.reload();
              }
          }
      });
  });
  
   
  
  $('#deleteSelected').click(function() {
  let selectedIds = [];
  $('.ids:checked').each(function() {
      selectedIds.push($(this).val());
  });
  
  if (selectedIds.length === 0) {
      alert("Please select at least one video to delete.");
      return;
  }
  
  if (!confirm("Are you sure you want to delete the selected videos?")) {
      return;
  }
  
  $.ajax({
      url: "{{ route('classvideo.bulk-delete') }}",
      type: "POST",
      data: {
          _token: "{{ csrf_token() }}",
          ids: selectedIds
      },
      success: function(response) {
          sessionStorage.setItem('successMessage', 'Selected videos deleted successfully!');
          location.reload();
      },
      error: function(xhr, status, error) {
          alert('An error occurred while deleting the videos.');
          console.log(xhr.responseText);
      }
  });
  });
  
  
  
</script>
@endsection