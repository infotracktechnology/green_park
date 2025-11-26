@extends('layouts.app')
@section('title', 'Staff Details')
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
            <div class="card-header"><h4>Reactive Staff</h4></div>
            <div class="card-body">

           

              <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="myTable">
                    <thead>
                      <tr role="row">
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Staff School Initial</th>
                        <th>Designation</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Mobile No</th>
                        <th>Gender</th>
                        <th>Inactive Date & Time</th>
                        <th>Reason/Remarks</th>
                        <th>Reactive</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($staffs as $row)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->school_initial }}</td>
                        <td>{{ $row->designation }}</td>
                        <td>{{ $row->branch?->name ?? 'N/A' }}</td>
                        <td>{{ $row->department }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->mob_no }}</td>
                        <td>{{ $row->gender }}</td>
                        <td>{{ $row->deleted_at->format('d/m/Y h:i A') }}</td>
                        <td>{{ $row->remarks }}</td>
                        <td>
                           <form action="{{route('staffs.restore')}}" class="no-loader" method="post" onsubmit="return confirm('Are you sure you want to restore this staff?')">
                            @csrf
                            <input type="hidden" value="{{ $row->id }}" name="id" />
                            <button type="submit" class="btn btn-danger"><i class="fas fa-sync"></i></button>
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
<script>
  const table = $('#myTable').DataTable({
    paging: false,
  });
</script>
@endsection