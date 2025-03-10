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
                   
                        <div class="card-body">
                          <form method="post" id="myForm" action="{{ route('exam.answerkey.upload') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                              <div class="col-md-6 col-sm-12 mb-3">
                                <h6 class="col-deep-purple">Answer Key Upload</h6>
                               </div>
  
                               <div class="col-md-6 col-sm-12 mb-3">
                                <a href="{{ env('APP_URL').'template/answer_key.csv'}}" class="btn btn-primary"><i class="fa fa-download"></i> Answer Key Upload Template (Format)</a>
                               </div>
    
                               <!-- Answer Key Upload Input -->
                               <div class="form-group col-lg-4">
                                 <label class="text-danger">(Upload only CSV file)</label>
                                  <input type="file" name="answer_key" id="answer_key" class="form-control form-control-sm" accept=".csv" required>
                                  @if ($errors->has('answer_key'))
                                      <span class="text-danger">{{ $errors->first('answer_key') }}</span>
                                  @endif
                               </div>
    
                               {{-- <div class="form-group col-lg-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload</button>
                             </div> --}}

                             <div class="col-md-12 m-t-20">
                              <h6 class="col-deep-purple">Answer Key Preview</h6>
                              </div>
                              
                              <div class="col-md-12">
                              <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="preview">
                                </table>
                              </div>
                              </div>

                              <div class="form-group col-md-12">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Validate</button>
                             </div>

                            </div>
                          </form>

                         </div>
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
                                  <th>No Records</th>
                                  <th>Upload Time</th>
                                  <th>Download</th>
                                  <th>Delete</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($answerkey_logs as $log)
                                <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $log->test_id }}</td>
                                  <td>{{ $log->test_name }}</td>
                                  <td>{{ $log->file_name }}</td>
                                  <td>{{ $log->no_rows }}</td>
                                  <td>{{ $log->upload_time }}</td>
                                  <td>{!! $log->path!= null ? '<a href="'.env('APP_URL').$log->path.'" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>' : 'N/A' !!}</td>
                                  <td>
                                    <form action="{{ route('answerkey.delete', ['id' => $log->id, 'test_id' => $log->test_id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete?');">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
<script>
    $('#answer_key').on('change', function() {
      let file = this.files[0];
      if(file.type != 'text/csv') {
        $('#answer_key').val('');
        alert('Please select only CSV file');
        return false;
      }
   Papa.parse(file, {
    header: true,
    dynamicTyping: true,
    skipEmptyLines: true,
      complete: function(results) {
        let tableHtml = '<thead><tr>';
        Object.keys(results.data[0]).forEach(header => {
            tableHtml += `<th>${header}</th>`;
        });
        tableHtml += '</tr></thead><tbody>';

        results.data.forEach(row => {
            tableHtml += '<tr>';
            Object.values(row).forEach(value => {
                tableHtml += `<td>${value}</td>`;
            });
            tableHtml += '</tr>';
        });
        tableHtml += '</tbody>';

        $('#preview').html(tableHtml);
    }
});
    })
  </script>
@endsection