@extends('layouts.dashboard')

@section('title', 'Document Upload')

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
          <div class="card card-primary" x-data="app">
            <div class="card-header">
              <h4>Document Upload</h4>
            </div>
            <div class="card-body">

              {{-- Success Message --}}
              @if(session('success'))
              <div class="alert alert-success alert-dismissible show fade">
                <div class="alert-body">
                  <button class="close" data-dismiss="alert"><span>&times;</span></button>
                  {{ session('success') }}
                </div>
              </div>
              @endif

              {{-- Tab Navigation --}}
              <ul class="nav nav-tabs" id="documentTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="upload-tab" data-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="true">
                    <i class="fas fa-upload"></i> Document Upload Form
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="false">
                    <i class="fas fa-list"></i> Uploaded Documents List
                  </a>
                </li>
              </ul>

              {{-- Tab Content --}}
              <div class="tab-content" id="documentTabsContent">

                {{-- Tab 1: Document Upload Form --}}
                <div class="tab-pane fade show active pt-4" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                  <form method="POST" action="{{ route('document.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="file_name">Document List</label>
                        <select name="document_name" class="form-control form-control-sm" required>
                          <option value="">Select Document List</option>
                          @foreach($options as $row)
                          <option value="{{ $row }}">{{ $row }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="document_file">Upload File (Max Size: 2MB)</label>
                        <input type="file" name="document_file" class="form-control form-control-sm" accept="application/pdf,image/*" required>
                      </div>

                      <div class="form-group col-lg-2 align-self-center">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Upload Document</button>
                      </div>
                    </div>
                  </form>
                </div>

                {{-- Tab 2: Uploaded Documents List --}}
                <div class="tab-pane fade pt-4" id="list" role="tabpanel" aria-labelledby="list-tab">
                  <div class="table-responsive">
                    <table class="table table-striped table-sm" id="documentTable" style="width:100%;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Document</th>
                          <th>Download</th>
                          <th>Uploaded At</th>
                          {{-- <th>Action</th> --}}
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($documents as $doc)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $doc->document_name }}</td>
                          <td>
                            <a href="{{ env('APP_URL').$doc->file }}" class="btn btn-primary text-white" download>
                              <i class="fas fa-download"></i>
                            </a>
                          </td>
                          <td>{{ $doc->created_at->format('d-m-Y H:i') }}</td>
                          {{-- <td>
                            <form action="{{ route('document.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this document?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          </td> --}}
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center">No documents uploaded yet.</td>
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
  </section>
</div>
@endsection

@section('scripts')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script>
  $(document).ready(function () {
      var table = $('#documentTable').DataTable({
          "autoWidth": false
      });
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          if ($(e.target).attr('href') === '#list') {
              table.columns.adjust().draw();
          }
      });
  });
</script>
@endsection