@extends('layouts.app')
@section('title', 'Hostel Room Transfer')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<style>

  .card-transfer {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  }
  .bg-light-accent {
    background-color: #f8fafc;
    border-left: 4px solid #6777ef;
  }
  .badge-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background-color: rgba(103, 119, 239, 0.1);
    color: #6777ef;
  }
  .divider-vr {
    border-right: 1px solid #e9ecef;
  }

</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row justify-content-center">
        <div class="col-md-11 col-sm-12">

          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          <div class="card card-primary card-transfer">
            <div class="card-header py-3 border-bottom-0">
              <h4 class="text-primary font-weight-bold mb-0">Student Room Transfer</h4>
            </div>
            
            <div class="card-body">
              <form action="{{ route('room.transfer') }}" method="POST">
                @csrf
                
                <div class="row">
                  <div class="col-md-6 divider-vr pr-md-4">
                    <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-4 d-flex align-items-center">
                      <span class="badge-icon mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      </span>
                      Student & Current Room
                    </h6>
                    
                    <div class="form-group mb-4">
                      <label class="font-weight-bold text-muted">Select Student <span class="text-danger">*</span></label>
                      <select class="form-control select2" name="student_id" id="student_id" required>
                        <option value="">Choose Student</option>
                        @foreach($students as $student)
                          <option value="{{ $student->student_id }}">{{ $student->student_id }} - {{ $student->student_name }}</option>
                        @endforeach
                      </select>
                    </div>

                      <div id="current_details_box" class="card border-0 bg-light-accent shadow-sm mt-3" style="display: none;">
                      <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                          <div class="badge-icon mr-2" style="background-color: rgba(103, 119, 239, 0.15)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                          </div>
                          <h6 class="mb-0 text-dark font-weight-bold">Current Occupancy</h6>
                        </div>
                        
                        <div class="row text-center text-md-left">
                          <div class="col-sm-4 border-right mb-2 mb-sm-0">
                            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">Hostel</small>
                            <span id="cur_hostel" class="font-weight-bold text-dark">-</span>
                          </div>
                          <div class="col-sm-4 border-right mb-2 mb-sm-0">
                            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">Room No</small>
                            <span id="cur_room" class="font-weight-bold text-dark">-</span>
                          </div>
                          <div class="col-sm-4">
                            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px; letter-spacing: 0.5px;">Cot No</small>
                            <span id="cur_cot" class="font-weight-bold text-dark">-</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6 pl-md-4">
                    <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-4 d-flex align-items-center">
                      <span class="badge-icon mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                      </span>
                      Transfer Destination (New Room)
                    </h6>
                    
                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-muted">Select Branch <span class="text-danger">*</span></label>
                      <select class="form-control select2" id="to_branch_id" required>
                        <option value="">Choose Branch</option>
                        @foreach($branches as $branch)
                          <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-muted">Select New Hostel <span class="text-danger">*</span></label>
                      <select class="form-control select2" name="to_hostel_id" id="to_hostel_id" required disabled>
                        <option value="">Select Hostel</option>
                      </select>
                    </div>

                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-muted">Select New Room No <span class="text-danger">*</span></label>
                      <select class="form-control select2" name="to_room_no" id="to_room_no" required disabled>
                        <option value="">Select Room</option>
                      </select>
                    </div>

                    <div class="form-group mb-3">
                      <label class="font-weight-bold text-muted">Select Available Cot <span class="text-danger">*</span></label>
                      <select class="form-control select2" name="to_cot_no" id="to_cot_no" required disabled>
                        <option value="">Select Cot</option>
                      </select>
                      <small class="form-text text-success" id="cot_avail_msg" style="display: none; font-size: 11px;">
                        <i class="fas fa-info-circle mr-1"></i> Only unoccupied cots in this room are visible.
                      </small>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-12 text-right border-top pt-3">
                    <button type="reset" class="btn btn-light px-4 mr-2" style="border-radius: 6px;">Reset</button>
                    <button type="submit" class="btn btn-primary px-4" id="submit_btn" style="border-radius: 6px;" disabled>Transfer Student</button>
                  </div>
                </div>
              </form>
            </div>
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
<script>
$(document).ready(function() {

    $('#student_id').on('change', function() {
        var studentId = $(this).val();
        if (studentId) {
            $.ajax({
                url: "{{ route('room.transfer') }}",
                type: "GET",
                data: { get_student_details: 1, student_id: studentId },
                success: function(response) {
                    if (response.success) {
                        $('#cur_hostel').text(response.hostel_name);
                        $('#cur_room').text(response.room_no);
                        $('#cur_cot').text(response.cot_no);
                        $('#current_details_box').slideDown();
                    } else {
                        $('#current_details_box').slideUp();
                    }
                }
            });
        } else {
            $('#current_details_box').slideUp();
        }
        checkSubmitButton();
    });

    $('#to_branch_id').on('change', function() {
        var branchId = $(this).val();
        $('#to_hostel_id').html('<option value="">Select Hostel</option>').prop('disabled', true);
        $('#to_room_no').html('<option value="">Select Room</option>').prop('disabled', true);
        $('#to_cot_no').html('<option value="">Select Cot</option>').prop('disabled', true);
        $('#cot_avail_msg').hide();

        if (branchId) {
            $.ajax({
                url: "{{ route('room.transfer') }}",
                type: "GET",
                data: { branch_id: branchId },
                success: function(data) {
                    $('#to_hostel_id').prop('disabled', false);
                    $.each(data, function(key, hostel) {
                        $('#to_hostel_id').append('<option value="' + hostel.id + '">' + hostel.name + '</option>');
                    });
                }
            });
        }
        checkSubmitButton();
    });

    $('#to_hostel_id').on('change', function() {
        var hostelId = $(this).val();
        $('#to_room_no').html('<option value="">Select Room</option>').prop('disabled', true);
        $('#to_cot_no').html('<option value="">Select Cot</option>').prop('disabled', true);
        $('#cot_avail_msg').hide();

        if (hostelId) {
            $.ajax({
                url: "{{ route('room.transfer') }}",
                type: "GET",
                data: { hostel_id: hostelId },
                success: function(data) {
                    $('#to_room_no').prop('disabled', false);
                    $.each(data, function(key, room) {
                        $('#to_room_no').append('<option value="' + room + '">' + room + '</option>');
                    });
                }
            });
        }
        checkSubmitButton();
    });

    $('#to_room_no').on('change', function() {
        var roomNo = $(this).val();
        var hostelId = $('#to_hostel_id').val();
        $('#to_cot_no').html('<option value="">Select Cot</option>').prop('disabled', true);
        $('#cot_avail_msg').hide();

        if (roomNo && hostelId) {
            $.ajax({
                url: "{{ route('room.transfer') }}",
                type: "GET",
                data: { room_no: roomNo, hostel_id: hostelId },
                success: function(data) {
                    $('#to_cot_no').prop('disabled', false);
                    if(data.length > 0) {
                        $.each(data, function(key, cot) {
                            $('#to_cot_no').append('<option value="' + cot + '">' + cot + '</option>');
                        });
                        $('#cot_avail_msg').show();
                    } else {
                        $('#to_cot_no').append('<option value="" disabled>No cots available</option>');
                    }
                }
            });
        }
        checkSubmitButton();
    });

    $('#to_cot_no').on('change', function() {
        checkSubmitButton();
    });

    function checkSubmitButton() {
        var student = $('#student_id').val();
        var hostel = $('#to_hostel_id').val();
        var room = $('#to_room_no').val();
        var cot = $('#to_cot_no').val();

        if (student && hostel && room && cot) {
            $('#submit_btn').prop('disabled', false);
        } else {
            $('#submit_btn').prop('disabled', true);
        }
    }
});
</script>
@endsection