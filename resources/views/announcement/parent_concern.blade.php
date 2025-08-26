@extends('layouts.app')
@section('title', 'Parent Concern')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content" x-data="parent_concern()">
   <section class="section">

    <div class="section-body"> 
        <div class="row">
          
            <div class="col-md-7 col-sm-12">
             
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
            @endif
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                        <h6 class="col-deep-purple">Parent Concern</h6>
                 
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>Date </th>
          <th>Category</th>
          <th>Type</th>
          <th>Details Concern</th>
          <th>Status</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($parentconcerns as $row)
          <tr> 
            <td>{{date("Y-m-d", strtotime($row->created_at));}}</td>
            <td>{{$row->category}}</td>
            <td>{{$row->concern_type}}</td> 
            <td><a href="javascript:void(0)" X-on:click="details_concern({{ json_encode($row) }})">{{ \Str::limit($row->details_concern, 20)}}</a></td>
            <td><span class="badge {{ $row->status == 'Open' ? 'badge-danger' : 'badge-warning' }}">{{$row->status}}</span></td>
        </tr>
        @endforeach
          
        </tbody>
      </table>
    </table>
                </div>
            </div>

            
        </div>
    </div>

    

            </div>
          

        </div>

        <div class="col-md-5 col-sm-12" x-show="view" x-transition.duration.1000ms>
            <div class="card card-primary">
                <div class="card-body">
                <div class="row">
                  <form action="{{ route('parent_concern') }}" class="col-md-12" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <h4 x-text="details.category"></h4>
                        <h5 x-text="details.concern_type"></h5>
                        <p x-text="details.details_concern"></p>
                        <a x-bind:href="attachment" x-show="attachment != null" target="_blank"> 
                            <i class="fas fa-paperclip"></i> Attachment
                        </a>
                    </div>
                    <div class="form-group">
                        <label for="status"><small>Status</small></label>
                        <input type="hidden" name="id" x-model="details.id">
                        <select name="status" x-model="details.status" id="status" class="form-control form-control-sm">
                            <option value="">Select Status</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                
                    <div class="form-group" x-show="details.status === 'Closed'" x-transition>
                        <label for="progress"><small>Remark</small></label>
                        <input type="text" name="remark" id="progress" class="form-control form-control-sm" placeholder="Progress Report" x-model="details.progress">
                    </div>
                
                    <div class="form-group" x-show="details.status === 'Closed'" x-transition>
                        <label for="file"><small>Attachment (PDF or Image)</small></label>
                        <input type="file" name="file" id="file" class="form-control form-control-sm" accept=".pdf,image/*">
                    </div>
                
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
                
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

  });
  function parent_concern(){
    return {
      view: false,
      details: {},
      attachment: null,
      details_concern(row){
        console.log(row);
        this.view = true;
        this.details = row;
        this.attachment = row.attachment != null ? `{{ env('APP_URL') }}/${row.attachment}` : null;
      }
    };
  }

</script>

@endsection