@extends('layouts.app')
@section('title', 'Revision Video')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-body">
                         <div class="row">
                          <div class="col-md-8 col-sm-12 mb-3">
                                    <h6 class="col-deep-purple">Revision Video Videos</h6>
                                </div>
                               

                                 <div class="col-md-2 col-sm-12 mb-3">
                                    <a href="{{ route('revisionvideo.create') }}" class="btn btn-primary btn-block">Revision Video Upload</a>
                                 </div>
                         </div>
                               
                           
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Chapter</th>
                                            <th>Video Id</t>
                                            <th>Expiry At</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($revisionvideos as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->subject }}</td>
                                            <td>{{ $row->chapter }}</td>
                                            <td>{{ $row->video_id }}</td>
                                            <td>{{ $row->expire_at  }}</td>
                                            <td>
                                                <a href="{{ route('revisionvideo.edit', $row->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <form action="{{ route('revisionvideo.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this video?');">
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
    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });

    flatpickr(".datetime-picker", {
        enableTime: true,
        allowInput: true,
        dateFormat: "Y-m-d H:i",
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
                theme: "light"
            })
        ]
    });

    $('#checkAll').click(function() {
        if (this.checked) {
            $('.ids').each(function() {
                this.checked = true;
            });
        } else {
            $('.ids').each(function() {
                this.checked = false;
            });
        }
    });
    

    $("#myForm").submit(function(e) {
        e.preventDefault();
        if($('.ids:checked').length == 0) {
            alert("Please select at least one video");
            return false;
        }
        var formData = new FormData(this);
        $('.ids:checked').each(function(index) {
            formData.append(`ids[${index}]`, $(this).val());
        });
        
        $.ajax({
            url: "{{ route('classvideo.schedule') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                    location.reload();
                }
            }
        });
    });
</script>
@endsection
