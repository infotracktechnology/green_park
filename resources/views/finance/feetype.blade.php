@extends('layouts.app')
@section('title', 'Fee Type')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content" x-data="fee_type">
   <section class="section">

    <div class="section-body"> 
        <div class="row">
            <div class="col-md-12 col-sm-12">
              @if(session()->has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
           @endif
                 
        
                <div class="card card-primary">
  
                    <div class="card-body">
  
                    <div class="row">
                    <div class="col-md-10 col-sm-12 mb-3">
                    <h6 class="col-deep-purple">Fee Type</h6>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3">
                      <button type="button" class="btn btn-primary btn-block" x-on:click="add()">Add Fee Type</button>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="table-responsive">
      <table class="table table-striped table-sm" id="myTable">
  
      <thead>
  
        <tr role="row">
          <th>S.No</th>
          <th>Fee Type</th>
          <th>Amount</th>
          <th>Edit </th>
          <th>Action</th>
        </tr>
  
        </thead>
  
        <tbody>
          @foreach ($feetype as $key => $row)
          <tr>
            <td>{{$key+1}}</td>
            <td>{{$row['feetype']}}</td>
            <td>{{$row['amount']}}</td>
            <td>
              <button type="button" class="btn btn-warning text-white" X-on:click="edit({{$key}},{{ json_encode($row) }})">
                 <i class="fas fa-edit"></i>
              </a>
           </td>
           <td>
            <form action="{{ route('feetype') }}" method="get" onsubmit="return confirm('Are you sure you want to delete this?')">
                <input type="hidden" name="index" value="{{$key}}">
                <button type="submit" name="delete" class="btn btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
           </td>
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
    </div>

   </section>

   <div class="modal fade" id="FeeType">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" x-text="title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('feetype') }}" method="get" enctype="multipart/form-data">
            <div class="row">
          
            <div class="form-group col-12">
              <label>Feetype</label>
              <input type="hidden" x-model="fees.index" name="index">
              <input type="text" x-model="fees.feetype"  name="feetype" class="form-control form-control-sm" required>
          </div>
          
          <div class="form-group col-12">
              <label>Amount</label>
              <input type="text" x-model="fees.amount"  name="amount" class="form-control form-control-sm numberk" required>
          </div>
          
         
          
              <div class="form-group col-12">
                <button type="submit" :name="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

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

function fee_type(){
  return {
    fees:{},
    submit:'create',
    title:'Add Fee Type',
    add(){
      this.submit='create';
      this.title='Add Fee Type';
      this.fees={};
      $('#FeeType').modal('show');
    },
    edit(index,row){
      this.fees.index = index;
      this.fees.feetype = row.feetype;
      this.fees.amount = row.amount;
      this.submit='update';
      this.title='Edit Fee Type';
      $('#FeeType').modal('show');
    }


  };
}
</script>

@endsection