@extends('layouts.app')
@section('title', 'Student')
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
                <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
            @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
                      <div class="row ">
                       
                        <div class="col-md-10 col-sm-12">
                            <h6 class="col-deep-purple">Students Details</h6>
                        </div>
                   
                              <div class="col-md-2 col-sm-12 mb-3">
                           <a href="{{ route('student.create') }}" class="btn btn-primary btn-block">Add Students</a>
                                </div>
                           
                    </div>
                    
                   
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>Student ID</th>          
          {{-- <th>SET</th> --}}
          <th>Campus</th>
          <th>Coaching Type</th>
          <th>User Name</th>
          <th>Password</th>
          <th>Student Name</th>
          <th>Gender</th>
          <th>Father Name</th>
          <th>Phone1</th>
          <th>Phone2</th>
          <th>H/D</th>
          {{-- <th>Allotment Letter</th>
          <th>Verification Latter</th> --}}
          <th>Edit </th>
          <th>Action</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($students as $student)
          <tr>
            <td>{{$student->id}}</td>
            <td>{{$student->campus}}</td>
            <td>{{$student->coaching_type}}</td>
            <td>{{$student->user_name}}</td>
            <td>{{$student->password_1}}</td>
            <td>{{$student->student_name}}</td>
            <td>{{$student->gender}}</td>
            <td>{{$student->father_name}}</td>
            <td>{{$student->ph_no1}}</td>
            <td>{{$student->ph_no2}}</td>
            <td>{{$student->hostel_dayscholar}}</td>
            
            <td>
              <a href="{{route('student.edit', $student->id)}}" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a>
            </td>
        
            <td>
              <form action="{{route('student.destroy', $student->id)}}" method="post" onsubmit="return confirm('Are you sure you want to Delete This student?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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

@endsection