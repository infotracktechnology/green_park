@extends('layouts.app')
@section('title', 'Exam Portion')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">

    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
          @endif
          <div class="card card-primary">

            <div class="card-body">

              <div class="row">
                <div class="col-md-10 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Exam Portion</h6>
                </div>

                <div class="col-md-2 col-sm-6 mb-3 ">
                  <a href="{{route('examportion.create')}}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add</a>
                </div>
              </div>

              <form action="{{ route('examportion.index') }}" method="get">
                <div class="row">
                  <div class="form-group col-lg-3">
                    <select name="coaching_type" class="select2">
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
                <form action="{{ route('examportion.destroy','bulk') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger m-b-10">Delete Selected</button>

                  <div class="table-responsive">
                    <table class="table table-striped table-sm" id="myTable">
                      <thead>

                        <tr role="row">
                          <th>#</th>
                          <th>User Type</th>
                          <th>Course</th>
                          <th>Branch </th>
                          <th>Coaching Type</th>
                          <th>Title</th>
                          <th>Attachment</th>
                          <th>Seen Log</th>
                          <th>Edit</th>
                        </tr>

                      </thead>
                      <tbody>
                        @foreach ($examportions as $examportion)
                        <tr>
                          <td><input type="checkbox" name="ids[]" value="{{$examportion->id}}"></td>
                          <td>{{ $examportion->usertype }}</td>
                          <td>{{ $examportion->course }}</td>
                          <td>{{ $examportion->branchNames() }}</td>
                          <td>{{ $examportion->coaching_type }}</td>
                          <td>{{ $examportion->title }}</td>
                          <td>
                            @if($examportion->attachment)
                            <a href="{{ env('APP_URL').'/'.$examportion->attachment }}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>
                            @endif
                          </td>
                          <td>
                            <button type="button" class="btn btn-primary logbtn" data-toggle="modal" data-target="#seenlog" data-action="{{$examportion->title}}" data-module="Exam Portion"> <i class="fas fa-eye"></i></button>
                          </td>
                          <td>
                            <a href="{{ route('examportion.edit', $examportion->id) }}" class="btn btn-primary">
                              <i class="fas fa-edit"></i>
                            </a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </form>
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
          <table class="table table-bordered table-sm" id="logTable">
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
  const table = $('#myTable').DataTable({
  
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  
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
  
</script>
@endsection