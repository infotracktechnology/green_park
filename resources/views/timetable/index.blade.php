@extends('layouts.app')
@section('title', 'Academic Year List')
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
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
           @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-md-10 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Timetable Details</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('timetable.create')}}" class="btn btn-primary btn-block">Add Timetable</a>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>#</th>
          <th>Academic Year</th>
          <th>Name</th>
          <th>Assign Sections</th>
          <th>Start Time</th>
          <th>Edit/Subject Assign</th>
          <th>Action</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($timetables as $timetable)
          <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$timetable->academic_year}}</td>
            <td>{{$timetable->name}}</td>
            <td><button type="button" class="btn btn-primary assign" data-toggle="modal" data-target="#assignSection" data-id="{{$timetable->id}}" data-section="{{ json_encode(explode(',', $timetable->section)) }}"><i class="fas fa-edit"></i> Assign Section</button><br> {{$timetable->section}}</td>
            <td>{{$timetable->start_time}}</td>
            <td>
              <a href="{{ route('timetable.edit', $timetable->id) }}" class="btn btn-primary">
                 <i class="fas fa-edit"></i>
              </a>
           </td>
           <td>
              <form action="{{ route('timetable.destroy', $timetable->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this branch?')">
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
    </table>
                </div>
            </div>
        </div>
    </div>

    

            </div>
          

        </div>
    </div>

   </section>
</div>

<div class="modal fade" id="assignSection">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign Section</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('timetable.index') }}" id="assignForm" method="get" enctype="multipart/form-data">
          <div class="row">
          <div class="form-group col-12">
            <label for="branch">Sections</label>
            <input type="hidden" name="id" id="assign_id">
            @foreach ($sections as $row)
            <input type="checkbox" name="section[]" value="{{$row->section}}"> {{$row->section}}
           @endforeach
          </div>
        
            <div class="form-group col-12">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
  const table = $('#myTable').DataTable({

    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],

  });

$(".assign").click(function(){
  var id = $(this).data('id');
  var section = $(this).data('section');
  $('input:checkbox').prop('checked', false);
  $.each(section, function(index, value){
    $('input:checkbox[value="'+value+'"]').prop('checked', true);
  })
  $("#assign_id").val(id);
  $("#assignSection").modal('show');
});
// $("#assignForm").on('submit', function(e){
//   e.preventDefault();
//     if($('input:checkbox').length == 0) {
//         alert("Please select at least one section");
//     } else {
//         this.submit();
//     }
// });
</script>

@endsection