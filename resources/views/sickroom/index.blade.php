@extends('layouts.app')
@section('title', 'Sickroom')
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
                  <h6 class="col-deep-purple">Sick Room Entry</h6>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <a href="{{route('sickroom.create')}}" class="btn btn-primary btn-block">Add Entry</a>
                </div>

                
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-striped table-sm" id="myTable">

                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Hostel</th>
                          <th>Room</th>
                          <th>Student ID</th>
                          <th>Student Name</th>
                          <th>In Time</th>
                          <th>Out Time</th>
                          <th>Illness/Injury</th>
                          <th>Action Taken</th>
                          <th>Medical Note</th>
                          <th>Total Hrs spent</th>
                          <th>Expance</th>
                          <th>Edit</th>
                          <th>Delete</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($entries as $entry)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $entry->hostel?->name }}</td>
                          <td>{{ $entry->room_no }}</td>
                          <td>{{ $entry->student?->student_id }}</td>
                          <td>{{ $entry->student?->student_name }}</td>
                          <td>{{ $entry->in_time->format(' d/m/Y h:i A') }}</td>
                          <td>{{ $entry->out_time->format(' d/m/Y h:i A') }}</td>
                          <td>{{ $entry->illness }}</td>
                          <td>{{ $entry->action_taken }}</td>
                          <td>{{ $entry->medical_note }}</td>
                          <td>{{ $entry->hours_spent }}</td>
                          <td>{{ $entry->expense }}</td>
                          <td>
                            <a href="{{ route('sickroom.edit', $entry->id) }}" class="btn btn-warning text-white">
                              <i class="fas fa-edit"></i>
                            </a>
                          </td>

                          <td>
                            <form action="{{ route('sickroom.destroy', $entry->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this Entry?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          </td>
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
    </div>

  </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script>
  const table = $('#myTable').DataTable({
    pageLength: 25,
  });
</script>
@endsection