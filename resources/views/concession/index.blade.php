@extends('layouts.app')
@section('title', 'Concession List')
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
                 
        
                <div class="card card-primary">
                    @if(session()->has('success'))
                      <div class="row">
                        <div class="col-12">
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                          {{ session('success') }}
                      </div>
                        </div>
                      </div>
                  @endif
                  @if(session()->has('error'))
                      <div class="row">
                        <div class="col-12">
                          <div class="alert alert-error alert-dismissible fade show" role="alert">
                          {{ session('error') }}
                      </div>
                        </div>
                      </div>
                  @endif

                    <div class="card-header">
                        <h4>Concession List</h4>
                        <div class="card-header-action">
                            <a href="{{route('concession.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="concessiontable">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Concession Name</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($concessions as $concession)
                                    <tr>
                                        <td class="text-center">{{$concession->id}}</td>
                                        <td>{{$concession->name}}</td>
                                        <td>{{$concession->amount}}</td>
                                        <td>
                                            @if($concession->status == 1)
                                            <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('concession.edit', $concession->id) }}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
   </section>
</div>
@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.flash.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/jszip.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/pdfmake.min.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/vfs_fonts.js')}}"></script>
<script src="{{asset('bundles/datatables/export-tables/buttons.print.min.js')}}"></script>
<script src="{{asset('js/page/datatables.js')}}"></script>
<script>
  const table = $('#concessiontable').DataTable({
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  });
</script>
@endsection
