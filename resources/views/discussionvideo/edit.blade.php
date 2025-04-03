@extends('layouts.app')
@section('title', 'Discussion Video')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
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
                  <div class="card card-primary" x-data="app">
                     <form method="post" id="myForm" action="{{ route('discussionvideo.update', $video->id) }}" enctype="multipart/form-data">
                        @csrf 
                        @method('PUT') 
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 mb-3">
                                 <h6 class="col-deep-purple"> Discussion Videos</h6>
                              </div>
                              


                              <div class="form-group col-lg-3">
                                <label for="academic_year">Academic Year</label>
                                <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                                    {{-- <option value="">Select Academic Year</option> --}}
                                    @foreach ($academicyear as $row)
                                        <option value="{{ $row->academic_year }}" {{ $video->academic_year == $row->academic_year ? 'selected' : '' }}>{{ $row->academic_year }}</option>
                                    @endforeach
                                </select>
                            </div>


                              <div class="form-group col-lg-4">
                                <label for="branch">Branch</label>
                                <select name="branch[]" class="form-control form-control-sm select2 @error('branch') is-invalid @enderror" multiple required>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ in_array($branch->id, explode(',', $video->branch)) ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                              </div>

                              <div class="form-group col-lg-4">
                                  <label>Coaching Type</label>
                                  <select name="coaching_type[]" class="form-control form-control-sm select2" multiple required>
                                      @foreach(['Offline', 'Online Recorded', 'Online Live', 'Test Series', '11', '12'] as $type)
                                          <option value="{{ $type }}" {{ in_array($type, explode(',', $video->coaching_type)) ? 'selected' : '' }}>
                                              {{ $type }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>


                              <div class="col-lg-4 form-group">
                                <label for="subject">Subject</label>
                                <select name="subject" id="subject" class="form-control form-control-sm" required>
                                    <option value="">Select Subject</option>
                                    <option value="physics" @selected( $video->subject == 'physics')>Physics</option>
                                    <option value="chemistry" @selected( $video->subject == 'chemistry')>Chemistry</option>
                                    <option value="botany" @selected( $video->subject == 'botany')>Botany</option>
                                    <option value="zoology" @selected( $video->subject == 'zoology')>Zoology</option>
                                </select>
                            </div>

                              <div class="form-group col-lg-3">
                                <label for="part">Part</label>
                                <select name="part" class="form-control form-control-sm" required>
                                    <option value="">Select Part</option>
                                    <option value="Part 1" {{ $video->part == 'Part 1' ? 'selected' : '' }}>Part 1</option>
                                    <option value="Part 2" {{ $video->part == 'Part 2' ? 'selected' : '' }}>Part 2</option>
                                    <option value="Part 3" {{ $video->part == 'Part 3'? 'selected' : '' }}>Part 3</option>
                                    <option value="Part 4" {{ $video->part == 'Part 4' ? 'selected' : '' }}>Part 4</option>
                                </select>
                            </div>
                            
                              
                              <div class="form-group col-lg-3">
                                  <label for="video">Video ID</label>
                                  <input type="number" name="video_id" class="form-control form-control-sm" value="{{ $video->video_id }}" required>
                              </div>


                              <div class="form-group col-lg-3">
                                <label>Start Datetime</label>
                                <input type="text" id="start_at" name="start_at" class="datetime-picker form-control form-control-sm" value="{{ $video->start_at }}" required>
                            </div>
                    
                            <!-- End Date -->
                            <div class="form-group col-lg-3">
                                <label>End Datetime</label>
                                <input type="text" id="end_at" name="end_at" class="datetime-picker form-control form-control-sm" value="{{ $video->end_at }}" required>
                                <div id="end_at_error" class="text-danger"></div>
                            </div>




        
 <div class="form-group col-lg-12">
        <button type="submit" class="btn btn-primary">Submit</button>
 </div>

    </form>

                        
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

<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script>
    flatpickr(".datetime-picker", {
        enableTime: true,
        allowInput: true,
        dateFormat: "Y-m-d H:i",
        plugins: [
            new confirmDatePlugin({
                confirmText: "OK",
                showAlways: false,
                theme: "light"
            })
        ]
    });

    $('#end_at').change(function() {
        $('#end_at_error').text('');
        const startTime = new Date($('#start_at').val());
        const endTime = new Date($(this).val());
        if (startTime >= endTime) {
            $('#end_at_error').text('End time must be greater than start time.');
            $(this).val('');
        }
    });
</script>
@endsection
