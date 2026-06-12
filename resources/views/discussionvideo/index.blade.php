@extends('layouts.app')
@section('title', 'Discussion Videos')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">

    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          <div class="card card-primary">

            <div class="card-body">

              <div class="row">
                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Discussion Videos </h6>
                  <button type="button" class="btn btn-danger mt-3" id="deleteSelected">
                    <i class="fas fa-trash"></i> Delete Selected
                  </button>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{route('discussionvideo.create')}}" class="btn btn-primary btn-block">Add Discussion Video</a>
                </div>
              </div>

               <form action="{{ route('discussionvideo.index') }}" method="get">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <select name="coaching_type" class="select2" required>
                      <option value="">Select Coaching Type</option>
                      @foreach ($coachingtype as $row)
                      <option value="{{$row}}" @selected(request('coaching_type')==$row)>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-lg-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                  </div>
                </div>
              </form>

              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="myTable">
                    <thead>
                      <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Academic Year</th>
                        <th>User Type</th>
                        <th>Course</th>
                        <th>Branch </th>
                        <th>Coaching Type</th>
                        <th>Date</th>
                        <th>Part</th>
                        <th>Subject</th>
                        <th>Video Id</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>Seen Log</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($discussionvideos as $discussionvideo)
                      <tr>
                        <td><input type="checkbox" class="checked_ids" name="ids[]" value="{{ $discussionvideo->id }}"></td>
                        <td>{{ $discussionvideo->academic_year}}</td>
                        <td>{{ $discussionvideo->usertype}}</td>
                        <td>{{ $discussionvideo->course}}</td>
                        <td>{{ $discussionvideo->branchNames()}}</td>
                        <td>{{ $discussionvideo->coaching_type}}</td>
                        <td>{{ $discussionvideo->date}}</td>
                        <td>{{ $discussionvideo->part }}</td>
                        <td>{{ $discussionvideo->subject }}</td>
                        <td>{{ $discussionvideo->video_id }}</td>
                        <td>{{ $discussionvideo->start_at }}</td>
                        <td>{{ $discussionvideo->end_at }}</td>
                        <td>
                          <button type="button" class="btn btn-primary logbtn" data-toggle="modal" data-target="#seenlog" data-action="{{$discussionvideo->title}}" data-module="Discussion Videos"> <i class="fas fa-eye"></i></button>
                        </td>
                        <td>
                          <a href="{{ route('discussionvideo.edit', $discussionvideo->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                          </a>
                        </td>
                        <td>
                          <form action="{{ route('discussionvideo.destroy', $discussionvideo->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this video?');">
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
<script>
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
  $(document).ready(function() {
      $('#myTable').DataTable();
  
      $('#selectAll').click(function() {
          $('.checked_ids').prop('checked', this.checked);
      });
  });
</script>

<script>
  $(document).ready(function() {
      $('#myTable').DataTable();
  
      $('#selectAll').click(function() {
          $('.checked_ids').prop('checked', this.checked);
      });
      if (sessionStorage.getItem('successMessage')) {
          let message = sessionStorage.getItem('successMessage');
          $('.section-body').prepend(`
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  ${message}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          `);
          sessionStorage.removeItem('successMessage');
      }
  });
  
  $('#deleteSelected').click(function(e) {
      e.preventDefault();
  
      var selectedIds = [];
      $('input:checkbox[name="ids[]"]:checked').each(function() {
          selectedIds.push($(this).val());
      });
  
      if (selectedIds.length > 0) {
          if (confirm("Are you sure you want to delete the selected videos?")) {
              $.ajax({
  url: "{{ route('discussionvideo.bulkDelete') }}",
  type: "POST", 
  data: {
      _token: "{{ csrf_token() }}",
      ids: selectedIds.join(",")
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
  
          }
      } else {
          alert("Please select at least one video to delete.");
      }
  });
</script>
@endsection