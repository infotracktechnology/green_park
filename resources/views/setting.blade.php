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
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="admission-tab" data-toggle="tab" href="#admission" role="tab" aria-controls="admission" aria-selected="true">Admission</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">Document List</a>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="admission" role="tabpanel" aria-labelledby="admission-tab">
                  <div class="row mt-3">
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
                <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                  <div class="row mt-3">
                    <div class="col-lg-6">
                      <form method="post" action="{{ route('option.document') }}">
                        @csrf
                        <div class="form-group">
                          <label>Document List</label>
                          <div class="input-group">
                            <input type="text" name="document_name" class="form-control" placeholder="Enter document name" required>
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="submit">Add Document</button>
                            </div>
                          </div>
                        </div>
                      </form>
                      <div class="table-responsive mt-4">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Document Name</th>
                              <th class="text-right">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($documents as $key => $doc)
                            <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>{{ $doc }}</td>
                              <td class="text-right">
                                <form method="post" action="{{ route('option.document') }}" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <input type="hidden" name="document_name" value="{{ $doc }}">
                                  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                            @empty
                            <tr>
                              <td colspan="3" class="text-center">No document options found.</td>
                            </tr>
                            @endforelse
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
  // $('.table').DataTable();
   $('.set-value').on('click', function() {
     let data = $(this).data('row');
     $('#SetValue').modal('show');
     $('#id').val(data.id);
     $('#value').val(data.value);
   });
  
   // Preserve active tab on page reload
   $(document).ready(function() {
     $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
         localStorage.setItem('activeTab', $(e.target).attr('href'));
     });
     var activeTab = localStorage.getItem('activeTab');
     if(activeTab){
         $('#myTab a[href="' + activeTab + '"]').tab('show');
     }
   });
</script>
@endsection