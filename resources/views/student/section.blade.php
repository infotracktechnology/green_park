@extends('layouts.app')
@section('title', 'Section Shuffle')
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
                    <form method="post" id="myForm"  action="{{ route('section.update') }}" enctype="multipart/form-data">
                       @csrf
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-md-9 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Section Shuffle </h6>


                    <div class="form-group col-lg-3">
                        <label>Section</label>
                        <select name="section" id="section" class="form-control form-control-sm" required>
                         <option value="">Select Section</option>
                         <option value="A">A</option>
                         <option value="B">B</option>
                         <option value="C">C</option>
                       </select>
                     </div>
                    </div>
                  
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-sm table-hover" id="myTable">
  
      <thead>
  
        <tr role="row">
            <th>
                #
            </th>
        <th>Student ID</th>
          <th>Campus</th>
          <th>Couching Type</th>
          <th>Student Name</th>
          <th>Section</th>
          <th>Gender</th>
          <th>Father Name</th>
          <th>Mobile No 1</th>
          <th>Mobile No 2</th>
       
        </tr>
  
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td>
                    <input type="checkbox" name="student_ids[]" value="{{$student->id}}" />
                </td>
              <td>{{$student->id}}</td>
              <td>{{$student->campus}}</td>
              <td>{{$student->coaching_type}}</td>
              <td>{{$student->student_name}}</td>
              <td>{{$student->section}}</td>
              <td>{{$student->gender}}</td>
              <td>{{$student->father_name}}</td>
              <td>{{$student->ph_no1}}</td>
              <td>{{$student->ph_no2}}</td>
        
            </tr>
            @endforeach
            
          </tbody>
  
        
       
      </table>
    </table>
  

                </div>
            </div>
            <div class="form-group col-lg-12">
                <button type="submit" class="btn btn-primary">Submit</button>
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
  const table = $('#myTable').DataTable({

    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],

  });

</script>
document.getElementById('selectAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
    });
});

@endsection