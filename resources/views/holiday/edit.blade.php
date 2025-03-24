@extends('layouts.app')
@section('title', 'Holiday')
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
              @if($holiday->type == 'Week Of')
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('holiday.update', $holiday->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple">Week of Holiday Details</h6>
                              </div>
                            
                              <div class="form-group col-lg-3">
                                <input type="hidden" name="name" value="Week Of Leave">
                                <input type="hidden" name="type" value="Week Of">
                                    <label for="academic_year">Academic Year</label>
                                    <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach ($academicyear as $row)
                                            <option value="{{ $row->academic_year }}" @selected($holiday->academic_year == $row->academic_year)>{{ $row->academic_year }}</option>
                                        @endforeach
                                    </select>
                              </div>

                            
                              <div class="form-group col-lg-3">
                                <label for="month">Month</label>
                                
                                <select name="month" id="month"  class="form-control form-control-sm" required>
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" @selected($holiday->month == $i)>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                  </select>
                              </div>

                              <div class="form-group col-lg-2">
                                <label for="day">Day</label>
                                <select name="day" id="day"  class="form-control form-control-sm" required>
                                    <option value="">Select Day</option>
                                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                                    <option value="{{ $i }}" @selected($holiday->day == $day)>{{ $day }}</option>
                                    @endforeach
                                  </select>
                              </div>

                              <div class="form-group col-lg-2">
                                <label for="week_of">Week Of</label>
                                <select name="week_of" id="week_of"  class="form-control form-control-sm" required>
                                    <option value="">Select Week Of</option>
                                    @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected($holiday->week_of == $i)>Week Of {{ $i }}</option>
                                    @endfor
                                  </select>
                              </div>
                              
                                <div class="form-group col-lg-2">
                                        <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                                </div>
                           </div>
                        </div>
                            </form>

                        
                        </div>
                        @else



                        <div class="card card-info" x-data="app">
                            <form method="post" id="myForm" action="{{ route('holiday.update', $holiday->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                               <div class="card-body">
                                  <div class="row">
                                     <div class="col-md-12 col-sm-12 mb-3">
                                        <h6 class="col-deep-purple">Holiday Details</h6>
                                     </div>
                                     
                                    
                                     <div class="form-group col-lg-2">
                                            <label for="academic_year">Academic Year</label>
                                            <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach ($academicyear as $row)
                                                    <option value="{{ $row->academic_year }}" @selected($holiday->academic_year == $row->academic_year)>{{ $row->academic_year }}</option>
                                                @endforeach
                                            </select>
                                      </div>
        
                                    
                                      
                                      <div class="form-group col-lg-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" value="{{ $holiday->name }}"  class="form-control form-control-sm">
                                      </div>

                                      <div class="form-group col-lg-3">
                                        <label for="holiday_type">Holiday Type</label>
                                        <select name="type" id="type"  class="form-control form-control-sm" required>
                                            <option value="">Select Holiday Type</option>
                                            @foreach(['Public Holiday','Vacation','Events','Other'] as $holiday_type)
                                            <option value="{{ $holiday_type }}" @selected($holiday->type == $holiday_type)>{{ $holiday_type }}</option>
                                            @endforeach
                                          </select>
                                      </div>

                                      
                                      <div class="form-group col-lg-2">
                                        <label for="month">Holiday Date</label>
                                        <input type="date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ $holiday->date }}" class="form-control form-control-sm" required>
                                      </div>
               
                                <div class="form-group col-lg-2">
                                    <button type="submit" class="btn btn-primary m-t-25">Submit</button>
                                </div>
                                  </div>
                               </div>
       
                                 </form>
       
                               
                               </div>
                               @endif
                     
                  </div>
              </div>
          </div>
      </div>
   </section>
</div>
@endsection

@section('js')

@endsection
