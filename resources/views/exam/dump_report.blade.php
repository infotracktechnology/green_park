
@extends('layouts.app')

@section('title', 'Test Dump Report')
@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}" />
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" />
<style>
    table {
        width: 100%;
        overflow-x: auto !important;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        color: #000 !important;
    }
    th {
        background-color: #eeece1;
    }
</style>
@endsection

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
         <div class="row">
            <div class="col-md-12 col-sm-12">
             
               <div class="card card-primary">
                
                  <form method="get" id="myForm" action="{{ route('exam.report.dump') }}" enctype="multipart/form-data">
                     <div class="card-body">
                        <h6>Test Dump Report</h6>
                        <div class="row">
                           
                          <div class="form-group col-lg-4">
                             <label>Test Name</label>
                             <select name="test_name" id="test_name" class="form-control form-control-sm" required>
                                 <option value="">Select Test</option>
                                 @foreach ($tests as $test)
                                     <option value="{{ $test->name }}" @if($test->name == $test_name) selected @endif>
                                      {{ $test->name }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                           <div class="form-group col-lg-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                         </div>
                        </div>
                        @if($test_name && count($results) > 0)
                        <div class="row m-t-20">
                            <div class="col-lg-12">
                                <button class="btn btn-primary m-b-20" id="exportpdf"><i class="fa fa-download"></i> Export to PDF</button>
                                <a class="btn btn-primary m-b-20" href="{{ route('exam.csv_download', ['test_ids' => $test_ids]) }}"><i class="fa fa-download"></i> Export to CSV</a>
                            </div>

                            <?php
                            $subjects = explode(',', $results[0]->subjects);
                            ?>

                            <div class="col-lg-12">
                            <div class="table-responsive">
                                <table  id="export">
                                 <thead>
                                  <tr role="row">
                                   <th rowspan="2">S.NO</th>
                                   <th rowspan="2">Test Id</th>
                                   <th rowspan="2">SID</th>
                                   <th rowspan="2">Mode</th>
                                   <th rowspan="2">Name</th>
                                   <th rowspan="2">Sex</th>
                                   <th rowspan="2">Campus</th>
                                   <th rowspan="2">Coach Type</th>
                                   <th rowspan="2">SEC</th>
                            
                                   @foreach ($subjects as $subject)
                                   <th colspan="4">{{ $subject }}</th>
                                   @endforeach
                                   <th rowspan="2">Net Total</th>
                                  </tr>
                                  <tr>
                                    @foreach ($subjects as $subject)
                                    <th>R</th>
                                    <th>W</th>
                                    <th>L</th>
                                    <th>TOT</th>
                                    @endforeach
                                  </tr>
                                 </thead>
                       
                                 <tbody>
                                    @foreach ($results as $result)
                                    <tr>
                                       <td>{{ $loop->iteration }}</td>
                                       <td>{{ $result->test_id }}</td>
                                       <td>{{ $result->student_id }}</td>
                                       <td>{{ $result->stmode }}</td>
                                       <td>{{ $result->student_name }}</td>
                                       <td>{{ $result->gender }}</td>
                                       <td>{{ $result->name }}</td>
                                       <td>{{ $result->coaching_type }}</td>
                                       <td>{{ $result->section }}</td>
                                       @foreach ($subjects as $subject)
                                       <?php
                                       $marks = \DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,subject FROM `exam_answer` where test_id in($test_ids) and student_id=$result->student_id and subject='$subject'");
                                       ?>
                                       <td>{{ $marks[0]->r }}</td>
                                       <td>{{ $marks[0]->w }}</td>
                                       <td>{{ $marks[0]->l }}</td>
                                       <td>{{ $marks[0]->tot }}</td>
                                       @endforeach
                                       <td>{{ $result->mark }}</td>
                                 </tbody>
                                 @endforeach
                                </table>
                            </div>
                        </div>
                        <form method="get" onsubmit="return confirm('Are you sure you want to publish?')" action="{{ route('exam.report.dump') }}" enctype="multipart/form-data">
                            <input type="hidden" name="test_name" value="{{ $test_name }}">
                            <div class="col-lg-12">
                                <h6>Exam Publish</h6>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Result Publish</label>
                                <select name="publish" id="publish" class="form-control form-control-sm" required>
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary m-b-20" type="Submit">Publish</button>
                            </div>
                        </form>
                        </div>
                        @else
                       
                        <p>No data found</p>
                       
                        @endif
                        
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
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>

    function exportTable() {
        const table = document.getElementById('export');
        const options = {
        margin:[0.5,0,0.5,0],
        filename:"test_dump_report.pdf",
        jsPDF:{ unit: 'in', format: 'letter', orientation: 'landscape' },
        };
        html2pdf().set(options).from(table).save().then(() => {
            alert('PDF successfully generated');
            location.href = "{{ route('exam.report.dump') }}";
        });
    }
    $('#exportpdf').click(function(e){
        e.preventDefault();
        exportTable();
    });


</script>

@endsection