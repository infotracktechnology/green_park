 @extends('layouts.app')
 @section('title', 'Dashboard')
 @section('main')
 <style>
  .ibox-content {
    background-color: #ffffff;
    padding: 15px 20px 20px 20px;
    border-color: #e7eaec;
    border-style: solid solid none;
    border-width: 1px 0;
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
}
.list-item{
  display: flex;
  justify-content: space-between;
}
 </style>
<div class="main-content">
  <section class="section">
    <div class="row">

      <div class="col-md-3">
        <div class="card">
          <div class="card-body card-type-3">
            <div class="row">
              <div class="col">
               <h4 class="col-black">Boys</h4>
                <span class="col-black font-18">{{ $students->where('gender', 'Male')->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="card-circle l-bg-orange text-white">
                  <i class="fas fa-user-friends font-18"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card">
          <div class="card-body card-type-3">
            <div class="row">
              <div class="col">
               <h4 class="col-black">Girls</h4>
                <span class="col-black font-18">{{ $students->where('gender', 'Female')->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="card-circle l-bg-orange text-white">
                  <i class="fas fa-user-friends font-18"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-md-3">
        <div class="card">
          <div class="card-body card-type-3">
            <div class="row">
              <div class="col">
                <h4 class="col-black">Active Users</h4>
                <span class="col-black font-18">{{ $students->where('active', 1)->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="card-circle l-bg-orange text-white">
                  <i class="fas fa-user-friends font-18"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3"></div>

      <div class="col-md-4">
        <div class="ibox-content">
          <h5 class="col-black">Branch Users</h5>
            <ul class="m-0 p-0">
              @foreach($branches as $branch)
                  <li class="list-item list-group-item">
                  <h6 class="col-black">{{ $branch->name }}</h6>
                  <span class="col-black font-16">{{ $branch->student->count() }}</span>
              </li>       
              @endforeach  
            </ul>
        </div> 
      </div>

      <div class="col-md-4">
        <div class="ibox-content">
          <h5 class="col-black">Coaching Type Users</h5>
            <ul class="m-0 p-0">
              @foreach($students->groupBy('coaching_type') as $key => $coaching_type)
                  <li class="list-item list-group-item">
                  <h6 class="col-black">{{ $key }}</h6>
                  <span class="col-black font-16">{{ $coaching_type->count() }}</span>
              </li>       
              @endforeach  
            </ul>
        </div> 
      </div>


    </div>
  </section>
</div>

@endsection