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

                    {{-- Upload Form --}}
                    <div class="card card-primary" x-data="app">
                        <form method="POST" action="{{ route('document.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <h6 class="mb-4 text-primary">Document Upload</h6>
                                <div class="form-row">
                                    {{-- <div class="form-group col-md-3">
                                        <label for="student_id">Student ID</label>
                                        <input type="text" name="student_id" class="form-control form-control-sm">
                                    </div> --}}

                                    <div class="form-group col-md-3">
                                        <label for="file_name">Document Type</label>
                                        <input type="text" name="file_name" class="form-control form-control-sm">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="document_file">Upload File</label>
                                        <input type="file" name="document_file" class="form-control form-control-sm" accept="application/pdf" required>

                                    </div>

                                    <div class="form-group col-lg-3">
                                        <button type="submit" class="btn btn-primary m-t-25">Upload</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif

                    {{-- Uploaded Documents Table --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Uploaded Documents</h6>
                        </div>

                         <div class="card-body">
                            
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="documentTable">
             
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            {{-- <th>Student ID</th> --}}
                                            <th>Document Type</th>
                                            <th>Download</th>
                                            <th>Uploaded At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($documents as $doc)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- <td>{{ $doc->student_id }}</td> --}}
                                                <td>{{ $doc->file_name }}</td>
                                                <td> <a href="{{ env('APP_URL').$doc->file }}" 
                                                    class="btn btn-primary text-white" download>
                                               <i class="fas fa-download"></i></a>
                                                
                                                </td>
                                                <td>{{ $doc->created_at->format('d-m-Y H:i') }}</td>
                                                <td>
                                                <form action="{{ route('document.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this video?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No documents uploaded yet.</td>
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
    </section>
</div>
@endsection

@section('scripts')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#documentTable').DataTable();
    });
</script>
@endsection
