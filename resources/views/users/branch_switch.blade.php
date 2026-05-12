@extends('layouts.app')
@section('title', 'Branch Switch')
@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
          <div class="card">
            <div class="card-header">
              <h4>Assigned Branches</h4>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Branch Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($branches_list as $branch)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>
                      @if($user->branch == $branch->id)
                      <span class="badge badge-success">Current Branch</span>
                      @else
                      <form action="{{ route('users.branchswitch', $user->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="branch" value="{{ $branch->id }}">
                        <button type="submit" class="btn btn-primary">Switch Branch</button>
                      </form>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection