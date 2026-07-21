@extends('layouts.app')
@section('title', 'Menu Assign')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <form method="get" id="myForm" action="{{ route('studentmenu.student') }}" enctype="multipart/form-data">
              
              <!-- Hidden input to trigger controller "assign" logic on submission -->
              <input type="hidden" name="assign" value="1">

              <div class="card-body">
                <div class="row">
                  <div class="col-12 mb-3">
                    @if(session('success'))
                      <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
                    @endif
                    <h6 class="col-deep-purple">Student Menu Assign</h6>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Branch</label>
                    <select name="branch" id="branch" class="form-control form-control-sm" required>
                      <option value="">-- Choose Branch --</option>
                      @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-name="{{ $branch->name }}" @selected($branch->id == request('branch'))>
                          {{ $branch->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Coaching Type</label>
                    <select name="type" id="type" class="form-control form-control-sm" required>
                      <option value="">-- Choose Coaching Type --</option>
                      @foreach ($coachingtype as $row)
                        <option value="{{ $row }}" @selected($row == request('type'))>{{ $row }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-lg-4">
                    <label>Student</label>
                    <select name="student" id="student" class="form-control form-control-sm select2" required>
                      <option value="">-- Choose Student --</option>
                      @foreach ($students as $student)
                        <option value="{{ $student->student_id }}" @selected($student->student_id == request('student'))>
                          {{ $student->user_name }} - {{ $student->student_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="d-flex flex-wrap col-12 mb-3">
                    @foreach($menus as $field)
                      <div class="form-group col-lg-3">
                        <label>
                          <input type="checkbox" name="fields[]" value="{{ json_encode($field) }}" 
                            @checked(in_array($field['title'], $menu_student))>
                          {{ $field['title'] }}
                        </label>
                      </div>
                    @endforeach
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn btn-primary">Assign</button>
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
<script>
  const goToMenu = (params = {}) => {
    const query = new URLSearchParams(params).toString();
    window.location = `{{ route('studentmenu.student',[],false) }}?${query}`;
  };

  const updateMenu = () => goToMenu({
    branch: $("#branch").val(),
    type: $("#type").val(),
    student: $("#student").val(),
    branch_name: $("#branch option:selected").data('name')
  });

  // Handles dropdown dynamic reloading
  $("#branch, #type, #student").on("change", function(e) {
    // Optional: Reset nested choices if a higher parent changes
    if (this.id === 'branch') {
      goToMenu({ branch: $(this).val() });
    } else if (this.id === 'type') {
      goToMenu({ branch: $("#branch").val(), type: $(this).val() });
    } else {
      updateMenu();
    }
  });
</script>
@endsection