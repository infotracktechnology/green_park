 @extends('layouts.app')
 @section('title', 'Dashboard')
 @section('main')
 <div class="main-content">
    <section class="section">
    <div class="row ">
        <div class="col-xl-4 col-sm-12">
          <div class="card card-primary">
            <div class="card-statistic-4">
              <div class="align-items-center justify-content-between">
                <div class="row ">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                      <h5 class="font-18" style="color: #f08017">Active users</h5>
                      <h2 class="mb-3 font-18 ">{{ $activeUsersCount }}</h2> 
                      
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                      <img src="{{asset('img/banner/active.png')}}" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-8 col-sm-12">
          <form action="{{ route('admin.home') }}" method="get">
          <div class="row m-t-40">
            <div class="form-group col-lg-4">
              <label for="academic_year">Academic Year</label>
              <select name="academic_year" id="academic_year" class=" form-control form-control-sm" required>
                  <option value="">Select Current Academic Year</option>
                  @foreach (\App\Models\AcademicYear::all() as $row)
                      <option value="{{ $row->academic_year }}">{{ $row->academic_year }}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group col-lg-8">
            <button type="submit" class="btn btn-primary m-t-25" id="update_academic_year">Update Academic Year</button>
          </div>
        </div>
        </form>
        </div>
        {{-- <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="card">
            <div class="card-statistic-4">
              <div class="align-items-center justify-content-between">
                <div class="row ">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                      <h5 class="font-15"> Customers</h5>
                      <h2 class="mb-3 font-18"></h2>
                      
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                      <img src="{{asset('img/banner/2.png')}}" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="card">
            <div class="card-statistic-4">
              <div class="align-items-center justify-content-between">
                <div class="row ">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                      <h5 class="font-15">Total Vehicles</h5>
                      <h2 class="mb-3 font-18"></h2>
                      
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                      <img src="{{asset('img/banner/3.png')}}" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="card">
            <div class="card-statistic-4">
              <div class="align-items-center justify-content-between">
                <div class="row ">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                    <div class="card-content">
                      <h5 class="font-15">Revenue</h5>
                      <h2 class="mb-3 font-18">$48,697</h2>
                      <p class="mb-0"><span class="col-green">42%</span> Increase</p>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                    <div class="banner-img">
                      <img src="{{asset('img/banner/4.png')}}" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}

      {{-- <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
          <div class="card ">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12">
                  <div id="chart1"></div>
                  
                </div>

              
              </div>
            </div>
          </div>
        </div>
      </div> --}}

      
    </section>     
  </div>
@endsection