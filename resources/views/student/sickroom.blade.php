@extends('layouts.app')
@section('title', 'Sickroom Entries')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-body">
              <h6 class="col-deep-purple mb-4">My Sick Room Entries</h6>
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
