@extends('layouts.app')

@section('title', 'Academic Year')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                  
                       
                        <div class="card-body">
                          <form method="get" action="{{ route('timetable.edit', $timetable->id) }}">
                           <div class="row">
                            

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Assign Subject to TimeTable</h6>
                              </div>

                             

                              <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                <select name="branch_id" id="branch_id"  class="form-control form-control-sm"  required>
                                  <option value="">Select  Branch</option>
                                    @foreach ($branches as $branch)
                                              <option value="{{ $branch->id }}" @selected($branch->id == request('branch_id'))>{{ $branch->name }}</option>
                                    @endforeach
                                  </select>
                              </div>
                    
                        
                              <div class="form-group col-lg-4">
                                <label for="coaching_type">Coaching Type</label>
                                <select name="coaching_type" id="coaching_type"  class="form-control form-control-sm"  required>
                                  <option value="">Select  Coaching Type</option>
                                    @foreach (\App\Models\Student::select('coaching_type')->distinct()->get() as $row)
                                              <option value="{{ $row->coaching_type }}" @selected($row->coaching_type == request('coaching_type'))>{{ $row->coaching_type }}</option>
                                    @endforeach
                                  </select>
                              </div>
                    
                        
                      <div class="form-group col-4">
                        <label for="branch">Sections</label>
                        <select name="section" id="section"  class="form-control form-control-sm"  required>
                          <option value="">Select  Section</option>
                            @foreach ($sections as $section)
                                      <option value="{{ $section->section }}" @selected($section->section == request('section'))>{{ $section->section }}</option>
                            @endforeach
                        </select>
                      </div>

                      <div class="form-group col-12">
                        <button type="submit" name="show"  class="btn btn-primary">Get TimeTable</button>
                      </div>
                           </div>
                          </form>
                            
                      @if($periods)
                      <form method="post" id="myForm" action="{{ route('timetable.update', $timetable->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="assign_id" value="{{ $periods->id }}">
                       <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>Day</th>
                                        @foreach(collect($periods->periods)->first() as $data)
                                        <th>{{ $data['period'] }}</th>
                                        @endforeach
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($periods->periods as $day => $structure)
                                      <tr>
                                        <td>{{ $day }}</td>
                                        @foreach($structure as $index => $data)
                                        <td>
                                          @if($data['period'] != "break")
                                          <input type="hidden" name="index[]" value="{{ $index }}">
                                          <input type="hidden" name="day[]" value="{{ $day }}">
                                          <select name="subject[]" class="form-control form-control-sm">
                                            <option value="">Select Subject</option>
                                            @foreach(["PHYSICS", "CHEMISTRY", "BOTANY", "ZOOLOGY", "MATHEMATICS"] as $subject)
                                            <option value="{{ $subject }}" @selected($data['subject'] == $subject)>{{ $subject }}</option>
                                            @endforeach
                                          </select>
                                          @endif
                                        </td>
                                        @endforeach
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>
                              </div>


                            
                              <div class="form-group col-lg-12">
                                 <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                  
                           </div>
                          </form>
                           @else
                           <p class="text-danger">No Periods Found</p>
                           @endif
                        </div>
                     
                  </div>
              </div>
          </div>
      </div>
   </section>
</div>
@endsection



@section('js')
<script>
 $("#branch_id").change(function(){
    $("#coaching_type").val('');
  });

  $("#coaching_type").change(function(){
    var coaching_type = $(this).val();
    var branch_id = $("#branch_id").val();
    location.href = `{{ route('timetable.edit',$timetable->id) }}?coaching_type=${coaching_type}&branch_id=${branch_id}`;
  });
</script>
@endsection
