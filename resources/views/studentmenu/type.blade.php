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
            <form method="get" id="myForm" action="{{ route('studentmenu.type') }}" enctype="multipart/form-data">
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12 col-sm-12 mb-3">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
                    @endif
                    <h6 class="col-deep-purple">Type Menu Assign</h6>
                  </div>

                  <div class="form-group col-lg-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control form-control-sm" required>
                      <option value="">Select Course</option>
                      @foreach ($course as $row)
                      <option value="{{$row}}" @selected($row==request('course'))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>



                  <div class="form-group col-lg-4">
                    <label for="branch">Branch</label>
                    <input type="hidden" name="branch_name" id="branch_name" value="{{ request('branch_name') }}">
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
                      @foreach ($types as $row)
                      <option value="{{$row}}" @selected($row==request('type'))>{{$row}}</option>
                      @endforeach
                    </select>
                  </div>

                  @foreach($menus as $key => $field)
                  <div class="form-group col-lg-3">
                    <input type="checkbox" name="fields[]" value="{{ json_encode($field) }}" @checked(in_array($field['title'], $menu_type))> {{ $field['title'] }}
                  </div>
                  @endforeach


                  <div class="form-group col-lg-12">
                    <button type="submit" name="assign" class="btn btn-primary">Assign</button>
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
   const goToMenu = (params = {}) => {
      const query = new URLSearchParams(params).toString();
      window.location = `{{ route('studentmenu.type',[],false) }}?${query}`;
   };

   $("#branch").on("change", function () {
      goToMenu({
         course: $("#course").val(),
         branch: this.value,
         branch_name: $(this).find(':selected').data('name')
      });
   });

   $("#type").on("change", function () {
      goToMenu({
         course: $("#course").val(),
         branch: $("#branch").val(),
         type: this.value,
         branch_name: $("#branch option:selected").data('name')
      });
   });
</script>
@endsection