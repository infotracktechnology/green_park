@extends('layouts.dashboard')
@section('title', 'InOut Register')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4><i style="font-size: 25px;" class="fas fa-sign-in-alt"></i> Hostel InOut Entries</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Hostel</th>
                      <th>Room No</th>
                      <th>Date & Time Leaving (Out)</th>
                      <th>Reason</th>
                      <th>Date & Time Returning (In)</th>
                      {{-- <th>Details of Courier</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($registers as $entry)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $entry->hostel?->name }}</td>
                      <td>{{ $entry->room_no }}</td>
                      <td>{{ $entry->datetime_out?->format('d/m/Y h:i A') }}</td>
                      <td>{{ $entry->reason }}</td>
                      <td>{{ $entry->datetime_in?->format('d/m/Y h:i A') }}</td>
                      {{-- <td>{{ $entry->courier_details }}</td> --}}
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center">No entries found.</td>
                    </tr>
                    @endforelse
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
