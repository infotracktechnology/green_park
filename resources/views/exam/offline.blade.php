@extends('layouts.app')

@section('title', 'Offline/OMR Upload')
@section('css')

@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
            @if(session()->has('success'))
               <div class="alert alert-success alert-dismissible show fade">{{ session('success') }}</div>
           @endif
           
           @if(session()->has('error'))
               <div class="alert alert-danger alert-dismissible show fade">{{ session('error') }}</div>
           @endif

                  <div class="card card-primary">
                    <form method="post" id="myForm" action="{{ route('exam.offline.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Offline/OMR Upload</h6>
                               </div>

                               <div class="col-md-6 col-sm-12 mb-3">
                                <a href="{{ env('APP_URL').'template/omr_response.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Offline/OMR Upload Template (Format)</a>
                               </div>

                           
                                
                               <!-- Test ID Dropdown -->
                               <div class="form-group col-lg-4">
                                <label>&nbsp;</label>
                                <input type="file" name="offline" class="form-control form-control-sm" accept=".csv"  required>
                                @if ($errors->has('offline'))
                                    <span class="text-danger">{{ $errors->first('offline') }}</span>
                                @endif
                             </div>
    
                               
                               <div class="form-group col-lg-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
                             </div>
                            </div>
                            
                         </div>
                      </form>
                    
                  </div>

                  
                  <div class="card card-success">
                    <div class="card-body">
                       <h6 class="col-deep-purple">Uploaded File Information</h6>
                       <div class="table-responsive">
                           <table class="table table-striped table-sm" id="myTable">
                         <thead>
                           <tr>
                             <th>S.No</th>
                             <th>Test Ids</th>
                             <th>Test Name</th>
                             <th>File Name</th>
                             <th>Upload Time</th>
                             <th>Download</th>
                             <th>Delete</th>
                           </tr>
                         </thead>
                         <tbody>
                           @foreach($offline_logs as $log)
                           <tr>
                             <td>{{ $loop->iteration }}</td>
                             <td>{{ $log->test_id }}</td>
                             <td>{{ $log->test_name }}</td>
                             <td>{{ $log->file_name }}</td>
                             <td>{{ $log->upload_time }}</td>
                             <td>{!! $log->path!= null ? '<a href="'.env('APP_URL').$log->path.'" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>' : 'N/A' !!}</td>
                             <td>
                               <form action="{{ route('offline.delete', ['id' => $log->id, 'test_id' => $log->test_id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete?');">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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

@endsection