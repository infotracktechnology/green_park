@extends('layouts.dashboard')
@section('title', 'Medical Entries')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h4><i style="font-size: 30px;" class="fas fa-hotel"></i>  Medical Entries</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>In Time</th>
                      <th>Out Time</th>
                      <th>Illness/Injury</th>
                      <th>Medical Note</th>
                      <th>Action Taken</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($entries as $entry)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $entry->in_time?->format('d/m/Y h:i A') }}</td>
                      <td>{{ $entry->out_time?->format('d/m/Y h:i A') }}</td>
                      <td>{{ $entry->illness }}</td>
                      <td>{{ $entry->medical_note }}</td>
                      <td>{{ $entry->action_taken }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center">No entries found.</td>
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
