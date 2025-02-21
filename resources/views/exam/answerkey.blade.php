@extends('layouts.app')

@section('title', 'Answer key Upload')
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
                    <form method="post" id="myForm" action="{{ route('exam.answerkey.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                           <div class="col-md-8 col-sm-12 mb-3">
                              <h6 class="col-deep-purple">Answer Key Upload</h6>
                             </div>
                            <div class="row">
    
                               <!-- Answer Key Upload Input -->
                               <div class="form-group col-lg-4">
                                 <label>&nbsp;</label>
                                  <input type="file" name="answer_key" class="form-control form-control-sm" accept=".csv" required>
                                  @if ($errors->has('answer_key'))
                                      <span class="text-danger">{{ $errors->first('answer_key') }}</span>
                                  @endif
                               </div>
    
                               <div class="form-group col-lg-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
                             </div>
                            </div>
                         </div>


                         <div class="card-body">
                            <h6 class="col-deep-purple">Uploaded File Information</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="myTable">
                              <thead>
                                <tr>
                                  <th>Test ID</th>
                                  <th>File Name</th>
                                  <th>Upload Time</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($answerkey_logs as $log)
                                <tr>
                                  <td>{{ $log->test_id }}</td>
                                  <td>{{ $log->file_name }}</td>
                                  <td>{{ $log->upload_time }}</td>
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
   </section>
</div>
@endsection
@section('js')
<script>
    const table = $("#myTable").DataTable({
     lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "All"],
     ],
    });
  </script>
@endsection