@extends('layouts.app')
@section('title', 'Hostel List Report')

@section('css')
<style>
  thead th {
    background-color: #56ade8 !important;
    color: #222 !important;
  }
  table th, table td {
    border: 1px solid #222 !important;
    height: 0px !important;
  }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          
          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Hostel List Reports</h4>
            </div>
            <div class="card-body">
              
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="section-wise-tab" data-toggle="tab" href="#section-wise" role="tab" aria-controls="section-wise" aria-selected="true">Section Wise</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="room-wise-tab" data-toggle="tab" href="#room-wise" role="tab" aria-controls="room-wise" aria-selected="false">Room Wise</a>
                </li>
              </ul>

              <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane fade show active" id="section-wise" role="tabpanel" aria-labelledby="section-wise-tab">
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <form method="get" action="{{ route('report.hostellist') }}">
                        <input type="hidden" name="active_tab" value="section_tab">
                        <div class="row">
                          
                          <div class="form-group col-lg-3">
                            <label>Branch</label>
                            <select name="branch" id="section_branch" class="form-control form-control-sm" required onchange="this.form.submit()">
                              <option value="">Select Branch</option>
                              @foreach ($branches as $b)
                              <option value="{{ $b->id }}" @selected($b->id == request('branch'))>{{ $b->name }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="form-group col-lg-3">
                            <label>Hostel</label>
                            <select name="hostel" class="form-control form-control-sm" required>
                              <option value="">Select Hostel</option>
                              @foreach ($hostels as $h)
                              <option value="{{ $h->id }}" @selected($h->id == request('hostel'))>{{ $h->name }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="form-group col-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block btn-sm">Submit</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  @if(request('branch') && request('hostel') && request('active_tab') == 'section_tab')
                  <div class="row mt-4">
                    <div class="col-md-6">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>S.No</th>
                              <th>Sections</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($sections as $key => $sec)
                            <tr>
                              <td>{{ $key + 1 }}</td>
                              <td><strong>{{ $sec }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                              <td colspan="2" class="text-center">No Sections found for this Hostel.</td>
                            </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <form method="post" class="no-loader" action="{{ route('report.hostellist.sectionpdf') }}">
                        @csrf
                        <input type="hidden" name="download_type" value="section">
                        <input type="hidden" name="branch" value="{{ request('branch') }}">
                        <input type="hidden" name="hostel" value="{{ request('hostel') }}">
                        
                        <div class="form-group mx-auto col-lg-6">
                          <label>Select Section</label>
                          <select name="section" class="form-control form-control-sm" required>
                            <option value="">Select Section</option>
                            @foreach($sections as $sec)
                            <option value="{{ $sec }}">{{ $sec }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="form-group mx-auto col-lg-6">
                          <label>Report Type</label>
                          <select name="view" class="form-control form-control-sm" required>
                            <option value="">Select Report</option>
                            <option value="hostelsectionlist">Attendance List</option>
                            <option value="phoneturn">Phone Turn List</option>
                            <option value="specialphoneturn">Special Phone Turn Register</option>
                            <option value="ac_nonacattendance">AC / NON AC Attendance</option>
                          </select>
                        </div>

                        <div class="form-group mx-auto col-lg-6">
                          <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                  @endif
                </div>

                <div class="tab-pane fade" id="room-wise" role="tabpanel" aria-labelledby="room-wise-tab">
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <form method="get" action="{{ route('report.hostellist') }}">
                        <input type="hidden" name="active_tab" value="room_tab">
                        <div class="row">
                          
                          <div class="form-group col-lg-4">
                            <label>Branch</label>
                            <select name="room_branch" id="room_branch" class="form-control form-control-sm" required onchange="this.form.submit()">
                              <option value="">Select Branch</option>
                              @foreach ($branches as $b)
                              <option value="{{ $b->id }}" @selected($b->id == request('room_branch'))>{{ $b->name }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="form-group col-lg-4">
                            <label>Hostel</label>
                            <select name="room_hostel" class="form-control form-control-sm" required>
                              <option value="">Select Hostel</option>
                              @foreach ($room_hostels as $rh)
                              <option value="{{ $rh->id }}" @selected($rh->id == request('room_hostel'))>{{ $rh->name }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="form-group col-lg-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block btn-sm">Submit</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  @if(request('room_branch') && request('room_hostel') && request('active_tab') == 'room_tab')
                  <div class="row mt-4">
                    
                    <div class="col-md-6">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Room No</th>
                              <th>Total Students</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($rooms as $key => $r)
                            <tr>
                              <td>{{ $key + 1 }}</td>
                              <td><strong>{{ $r->room_no }}</strong></td>
                              <td>{{ $r->total_students }}</td>
                            </tr>
                            @empty
                            <tr>
                              <td colspan="3" class="text-center">No rooms registered.</td>
                            </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <form method="post" class="no-loader" action="{{ route('report.hostellist.roompdf') }}">
                        @csrf
                        <input type="hidden" name="download_type" value="room">
                        <input type="hidden" name="room_branch" value="{{ request('room_branch') }}">
                        <input type="hidden" name="room_hostel" value="{{ request('room_hostel') }}">
                        
                        <div class="form-group mx-auto col-lg-6">
                          <label>Select Room No</label>
                          <select name="room_no" class="form-control form-control-sm" required>
                            <option value="">Select Room No</option>
                            @foreach($rooms as $r)
                            <option value="{{ $r->room_no }}">{{ $r->room_no }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="form-group mx-auto col-lg-6">
                          <label>Report Type</label>
                          <select name="view" class="form-control form-control-sm" required>
                            <option value="">Select Report</option>
                            <option value="hostelroomlist">Room Wise Attendance</option>
                            <option value="phoneturn">Phone Turn List</option>
                            <option value="specialphoneturn">Special Phone Turn Register</option>
                            <option value="ac_nonacattendance">AC / NON AC Summary</option>
                          </select>
                        </div>

                        <div class="form-group mx-auto col-lg-6">
                          <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                      </form>
                    </div>

                  </div>
                  @endif

                </div>

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
<script>
  $(document).ready(function() {
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeHostelTab', $(e.target).attr('href'));
    });
    
    var activeTab = localStorage.getItem('activeHostelTab');
    if(activeTab){
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }
  });
</script>
@endsection