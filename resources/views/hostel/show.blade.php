@extends('layouts.app')
@section('title', 'Hostel Details')
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
                    <div class="col-md-12 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Hostel Room Details</h6>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
                         <table class="table table-striped table-sm" id="myTable">
  
                            <thead>
                        
                                <tr role="row">
                                <th>#</th>
                                <th>Hostel</th>
                                <th>Floor</th>
                                <th>Room</th>
                                <th>No Of Cots</th>
                                <th>Cart Nos</th>
                                <th>Delete</th>
                                </tr>
                        
                                </thead>
                        
                                <tbody>
                                @foreach ($rooms as $room)
                                <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $hostel->name }}</td>
                                <td>{{ $room->floor }}</td>
                                <td>{{ $room->room_no }}</td>
                                <td>{{ $room->no_of_cots }}</td>
                                <td>{{ $room->cart_no }}</td>
                                <td>
                                        <form action="{{ route('room.delete') }}" method="post" onsubmit="return confirm('Are you sure you want to delete this hostel?')">
                                            @csrf
                                           <input type="hidden" name="room_no" value="{{ $room->room_no }}">
                                           <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
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
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "paging": 0,

  });

</script>

@endsection