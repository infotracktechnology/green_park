@extends('layouts.app')
@section('title', 'Admin Settings')

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
            <div class="card-header">
              <h4>Admin Settings</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-striped" style="width:100%;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Attribute</th>
                          <th>Value</th>
                          <th>Created Time</th>
                          <th>Set Value</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($setting as $key => $row)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $row->key }}</td>
                          <td>{{ $row->value }}</td>
                          <td>{{ $row->created_at->diffForHumans() }}</td>
                          <td>
                            <button class="btn btn-primary set-value" data-toggle="modal" data-target="#SetValue" data-row="{{ json_encode($row) }}">Set Value</button>
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
    </div>
  </section>
</div>




<div id="SetValue" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Set Value Form</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" action="{{ route('admin.setting') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="form-group">
              <label>New Value</label>
              <input type="hidden" name="id" id="id">
              <input type="text" id="value" name="value" class="form-control form-control-sm" required>
            </div>
          </div>
      
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
  $('.table').DataTable();
  $('.set-value').on('click', function() {
    let data = $(this).data('row');
    $('#SetValue').modal('show');
    $('#id').val(data.id);
    $('#value').val(data.value);
  });
</script>
@endsection