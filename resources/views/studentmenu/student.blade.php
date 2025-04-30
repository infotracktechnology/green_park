@extends('layouts.app')
@section('title', 'Menu Assign')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary">
                     <form method="get" id="myForm" action="{{ route('studentmenu.student') }}" enctype="multipart/form-data">
                      <div class="card-body">
                      <div class="row">

                        <div class="col-md-12 col-sm-12 mb-3">
                            @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
                        @endif
                            <h6 class="col-deep-purple">Student Menu Assign</h6>
                        </div>

                            <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                
                                <select name="branch" id="branch" class="form-control form-control-sm" required>
                                    <option value="">-- Choose Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" data-name="{{ $branch->name }}" @selected($branch->id == request('branch'))>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="branch">Coaching Type</label>
                                <select name="type" id="type" class="form-control form-control-sm" required>
                                    <option value="">-- Choose Coaching Type --</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->coaching_type }}" @selected($type->coaching_type == request('type'))>{{ $type->coaching_type }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group col-lg-4">
                                <label for="branch">Student</label>
                                <select name="student" id="student" class="form-control form-control-sm select2" required>
                                    <option value="">-- Choose Student --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->student_id }}" @selected($student->student_id == request('student'))>{{ $student->user_name }} - {{ $student->student_name }}</option>
                                    @endforeach
                                </select>

                            </div>

                        @foreach($menus as $key => $field)
                        <div class="form-group col-lg-3">
                            <input type="checkbox" name="fields[]" value="{{ json_encode($field) }}" @checked(in_array($field['title'], $menu_student))> {{ $field['title'] }}
                        </div>
                        @endforeach
                        

                        <div class="form-group col-lg-12">
                            <button type="submit"  name="assign" class="btn btn-primary">Assign</button>
                        </div>
                      </div>
                      </div>
                     </form>
                  </div>
              </div>
          </div>
      </div>
   </section>
</div>
@endsection
@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script>
  $("#branch").on("change", function() {
    location.href = `{{ route('studentmenu.student') }}?branch=${$(this).val()}`;
  });

  $("#type").on("change", function() {
    location.href = `{{ route('studentmenu.student') }}?branch=${$('#branch').val()}&type=${$(this).val()}`;
  })

  $("#student").on("change", function() {
    location.href = `{{ route('studentmenu.student') }}?branch=${$('#branch').val()}&type=${$('#type').val()}&student=${$(this).val()}`;
  })


</script>
@endsection