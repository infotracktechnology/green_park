@extends('layouts.app')
@section('title', 'Exam Category')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css" />
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
          @endif

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <div class="col-md-8 col-sm-12 mb-3">
                  <h6 class="col-deep-purple">Exam Category</h6>
                </div>
                <div class="col-md-2 col-sm-12 mb-3">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal">Add Category </button>
                </div>
              </div>
              <div class="col-6">
                  <div class="table-responsive">
                    <table class="table table-striped table-sm" id="myTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Test Category</th>
                          <th>Delete</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($category as $row)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $row }}</td>
                          <td>
                          <form action="{{ route('exam.testcategory') }}" onsubmit="return confirm('Are you sure you want to delete this?')" method="post">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="category" value="{{ $row }}">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                          </form>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<div class="modal fade" id="categoryModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Test Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{ route('exam.testcategory') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="form-group col-12">
              <label>Category Name</label>
              <input type="text" name="category" class="form-control form-control-sm" required>
            </div>

            <div class="form-group col-12">
              <button type="submit" class="btn btn-primary">Add</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
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