@extends('layouts.app')
@section('title', 'Discussion Videos')
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
                    <h6 class="col-deep-purple">Discussion Videos </h6>
                    <button type="button" class="btn btn-danger mt-3" id="deleteSelected">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <a href="{{route('discussionvideo.create')}}" class="btn btn-primary btn-block">Add Discussion Video</a>
                    </div>
                    </div>
                 
                    
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm" id="myTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>#</th>
                                        <th>Academic Year</th>
                                        <th>Branch</th>
                                        <th>Coaching Type</th>
                                        <th>Subject</th>
                                        <th>Part</th>
                                        <th>Video Id</th>
                                        <th>Start At</th>
                                        <th>End At</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @foreach ($discussionvideos as $discussionvideo)
                                    <tr>
                                        <td><input type="checkbox" class="checked_ids" name="ids[]" value="{{ $discussionvideo->id }}"></td>
                                
                                        <td>{{ $discussionvideo->id }}</td>
                                        <td>{{ $discussionvideo->academic_year }}</td>
                                        <td>
                                            @php
                                                $branchNames = \App\Models\Branch::whereIn('id', explode(',', $discussionvideo->branch))->pluck('name')->toArray();
                                            @endphp
                                            {{ implode(', ', $branchNames) }}
                                        </td>
                                        <td>{{ implode(', ', explode(',', $discussionvideo->coaching_type)) }}</td>
                                        <td>{{ $discussionvideo->subject }}</td>
                                        <td>{{ $discussionvideo->part }}</td>
                                        <td>{{ $discussionvideo->video_id }}</td>
                                        <td>{{ $discussionvideo->start_at }}</td>
                                        <td>{{ $discussionvideo->end_at }}</td>
                                        <td>
                                            <a href="{{route('discussionvideo.edit', $discussionvideo->id)}}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        </td>
                                        <td>
                                            <form action="{{ route('discussionvideo.destroy', $discussionvideo->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();

        $('#selectAll').click(function() {
            $('.checked_ids').prop('checked', this.checked);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#myTable').DataTable();

        $('#selectAll').click(function() {
            $('.checked_ids').prop('checked', this.checked);
        });
        if (sessionStorage.getItem('successMessage')) {
            let message = sessionStorage.getItem('successMessage');
            $('.section-body').prepend(`
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            sessionStorage.removeItem('successMessage');
        }
    });

    $('#deleteSelected').click(function(e) {
        e.preventDefault();

        var selectedIds = [];
        $('input:checkbox[name="ids[]"]:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            if (confirm("Are you sure you want to delete the selected videos?")) {
                $.ajax({
                    url: "{{ route('discussionvideo.bulkDelete') }}",
                    type: "DELETE",
                    data: {
                        ids: selectedIds.join(","),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        sessionStorage.setItem('successMessage', 'Selected videos deleted successfully!');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred while deleting the videos.');
                        console.log(xhr.responseText);
                    }
                });
            }
        } else {
            alert("Please select at least one video to delete.");
        }
    });
</script>

{{-- <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#myTable').DataTable();

        // Handle "Select All" checkbox
        $('#selectAll').click(function() {
            $('.checked_ids').prop('checked', this.checked);
        });

        // Handle Delete Selected
        $('#deleteSelected').click(function(e) {
            e.preventDefault();

            var selectedIds = [];
            $('input:checkbox[name="ids[]"]:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0) {
                if (confirm("Are you sure you want to delete the selected videos?")) {
                    $.ajax({
                        url: "{{ route('discussionvideo.bulkDelete') }}",
                        type: "DELETE",
                        data: {
                            ids: selectedIds.join(","),
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Redirect with a success message
                            window.location.href = "{{ route('discussionvideo.index') }}?success=" + encodeURIComponent(response.message);
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred while deleting the videos.');
                            console.log(xhr.responseText);
                        }
                    });
                }
            } else {
                alert("Please select at least one video to delete.");
            }
        });
    });
</script> --}}

@endsection

  
