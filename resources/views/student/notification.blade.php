@extends('layouts.dashboard')

@section('title', 'Announcements')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection
@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-12">
                <!-- Support tickets -->
                <div class="card">
                  <div class="card-header">
                    <h4>Announcements</h4>
                    <form class="card-header-form">
                    </form>
                  </div>
                  <div class="card-body">
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                     
                      <div class="flex-1 ms-3">
                        
                        <span class="fw-bold">#89754</span>
                        <a href="javascript:void(0)">Please add advance table</a>
                        <p class="my-1">Hi, can you please add new table for advan...</p>
                        
                      </div>
                    </div>
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                     
                      <div class="flex-1 ms-3">
                      
                        <span class="fw-bold">#57854</span>
                        <a href="javascript:void(0)">Select item not working</a>
                        <p class="my-1">please check select item in advance form not work...</p>
                       
                      </div>
                    </div>
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                     
                      <div class="flex-1 ms-3">
                      
                        <span class="fw-bold">#85784</span>
                        <a href="javascript:void(0)">Are you provide template in Angular?</a>
                        <p class="my-1">can you provide template in latest angular 8.</p>
                        
                      </div>
                    </div>
                    <div class="support-ticket media pb-1 mb-3 d-flex">
                      
                      <div class="flex-1 ms-3">
                        
                        <span class="fw-bold">#25874</span>
                        <a href="javascript:void(0)">About template page load speed</a>
                        <p class="my-1">Hi, John, can you work on increase page speed of template...</p>
                       
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Support tickets -->
              </div>
            </div>
        </div>
      </div>
@endsection