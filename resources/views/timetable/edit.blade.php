@extends('layouts.app')

@section('title', 'Academic Year')

@section('main')
<div class="main-content">
   <section class="section">
      <div class="section-body"> 
          <div class="row">
              <div class="col-12">
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('timetable.update', $timetable->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                           
                           <div class="row">

                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Assign Sections to TimeTable</h6>
                              </div>

                              <div class="form-group m-b-10 col-lg-6">
                                <label for="academic_year">Sections</label>
                                <select name="section[]" id="section" class="select" multiple required>
                                  <option value="">Select Multiple Section</option>
                                    @foreach ($sections as $row)
                                        <option value="{{ $row->section }}" @selected(in_array($row->section,explode(',', $timetable->section)))>{{ $row->section }}</option>
                                    @endforeach
                                </select>
                            </div>

                              {{-- <div class="col-md-12 col-sm-12 mb-3">
                                <ul class="nav nav-pills" id="myTab3" role="tablist">
                                  @foreach($timetable->structure  as $index => $day)
                                  <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{$index}}-tab" data-toggle="tab" href="#{{$index}}" role="tab" aria-controls="{{$index}}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                       {{ $index }}
                                    </a>
                                </li>
                                  @endforeach
                                </ul>
                                <div class="tab-content" id="myTabContent2">
                                  @foreach($timetable->structure  as $index => $day)
                                  <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{$index}}" role="tabpanel" aria-labelledby="{{$index}}-tab">
                                  
                                  </div>
                                  @endforeach
                                </div>
                              </div> --}}

                              <div class="col-md-12 col-sm-12 mb-3">
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th>Day</th>
                                        @foreach(collect($timetable->structure)->first() as $data)
                                        <th>{{ $data['period'] }}</th>
                                        @endforeach
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($timetable->structure as $day => $structure)
                                      <tr>
                                        <td>{{ $day }}</td>
                                        @foreach($structure as $index => $data)
                                        <td>
                                          @if($data['type'] == 'academic')
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
//   function City(state) {
//      if (!state) return;
//      $.get("{{ route('staff.create') }}", {state: state}, function(data) {
//          let html = '<option value="">Select City</option>';
//          $.each(data, function(key, value) {
//              html += '<option value="' + value.District + '">' + value.District + '</option>';
//          });
//          $('#city').html(html);
//      });
//   }
</script>
@endsection
