@extends('layouts.app')
@section('title', 'Exam Name List')

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
                       <h6 class="col-deep-purple">Exam Name List</h6>
                    </div>

                    <div class="col-md-2 col-sm-12 mb-3">
                       <a href="{{ route('examname.create') }}"
                          class="btn btn-primary btn-block">
                          Add Exam
                       </a>
                    </div>
                 </div>

                 <div class="col-12">
                    <div class="table-responsive">

                       <table class="table table-striped table-sm" id="myTable">

                          <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Exam Date</th>
                                <th>Test Id</th>
                                <th>Test Category</th>
                                <th>Exam Name</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Section</th>
                                <th>Coaching Type</th>
                                <th>Branch</th>
                                <th>Academic Year</th>
                                <th>Action</th>
                                <th>Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($examNames as $examName)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $examName->exam_date }}</td>
                                    <td>{{ $examName->testid }}</td>
                                    <td>{{ $examName->testcategory }}</td>
                                    <td>{{ $examName->name }}</td>
                                    <td>{{ $examName->course }}</td>
                                    <td>{{ $examName->batch }}</td>
                                    <td>{{ $examName->section }}</td>
                                    <td>{{ $examName->coaching_type }}</td>
                                    <td>{{ $examName->branchNames()}}</td>
                                    <td>{{ $examName->academic_year }}</td>

                                    <td>
                                        <a href="{{ route('examname.edit', $examName->id) }}" class="btn btn-warning text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>

                                    <td>
                                        <form action="{{ route('examname.destroy', $examName->id) }}" method="post" style="display:inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this exam?')">
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
      "lengthMenu": [
         [10, 25, 50, -1],
         [10, 25, 50, "All"]
      ],
   });
</script>

@endsection
