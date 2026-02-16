@extends('layouts.app')
@section('title', 'User Menu Assign')

@section('css')
<style>
  .table-vcenter td { vertical-align: middle !important; }
  .sub-item-badge { margin: 5px; display: inline-block; }
</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">

            <form method="POST" action="{{ route('users.menuassign', $user->id) }}" enctype="multipart/form-data">
              @csrf
              <div class="card-header">
                <h4>Menu Assign: {{ $user->username }}</h4>
              </div>

              <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                      <tr>
                        <th style="width: 250px;">
                          <strong>Main Module</strong>

                        </th>
                        <th>Sub-Menus / Permissions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($menus as $key => $menu)
                      <tr>

                        <td>
                          <div class="custom-control custom-checkbox">

                            <input type="checkbox" class="custom-control-input main-checkbox" id="main_{{ $key }}" data-target="row_{{ $key }}" @if(!isset($menu['submenu'])) name="menus[{{ $menu['title'] }}][self]" @checked(in_array($menu['title'], $authorized_titles)) @endif>
                            <label class="custom-control-label" for="main_{{ $key }}">
                              @if(isset($menu['icon'])) <i data-feather="{{ $menu['icon'] }}" width="15"></i> @endif
                              {{ $menu['title'] }}
                            </label>
                          </div>
                        </td>


                        <td>
                          @if(isset($menu['submenu']))
                          <div class="row row_{{ $key }}">
                            @foreach($menu['submenu'] as $sub)
                            <div class="col-md-4">
                              <div class="custom-control custom-checkbox sub-item-badge">
                                <input type="checkbox" class="custom-control-input sub-checkbox" id="sub_{{Str::slug($menu['title'])}}_{{ $loop->index }}" name="menus[{{ $menu['title'] }}][submenu][{{ $sub['title'] }}]" @checked(in_array($sub['title'], $authorized_titles))>
                                <label class="custom-control-label" for="sub_{{Str::slug($menu['title'])}}_{{ $loop->index }}">
                                  {{ $sub['title'] }}
                                </label>
                              </div>
                            </div>
                            @endforeach
                          </div>
                          @else
                          <span class="text-muted"><small><i>No sub-menus (Single Page)</i></small></span>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary">Save Menu Permissions</button>
                </div>

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
<script>
  $(document).ready(function() {
      $('.main-checkbox').on('change', function() {
          var isChecked = $(this).is(':checked');
          var targetClass = $(this).data('target');
          $('.' + targetClass).find('.sub-checkbox').prop('checked', isChecked);
      });
  
      $('.sub-checkbox').on('change', function() {
          var rowDiv = $(this).closest('.row');
          var rowClass = rowDiv.attr('class').split(' ').pop();
          var mainCheckbox = $('.main-checkbox[data-target="'+rowClass+'"]');
          if($(this).is(':checked')) {
              mainCheckbox.prop('checked', true);
          }
          else if($('.sub-checkbox:checked').length == 0) {
              mainCheckbox.prop('checked', false);
          }
      });
  });
</script>
@endsection