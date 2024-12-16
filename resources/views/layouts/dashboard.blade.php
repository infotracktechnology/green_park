<!DOCTYPE html>
<html lang="en">
<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title')</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{asset('css/components.css')}}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.ico')}}' />
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    .select2{
      width: 100% !important;
    }
  </style>
  @yield('css')
</head>
<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav style="left:0;" class="navbar navbar-expand-lg bg-grey main-navbar sticky">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
            <li><form class="form-inline mr-auto">
                <div class="search-element">
                  <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="200">
                  <button class="btn" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
            </li>
          </ul>
        </div>



        <ul class="navbar-nav navbar-right">


          <li class="dropdown dropdown-list-toggle"><a href="#" data-bs-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i data-feather="bell" class="bell"></i>
          </a>
          <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
            <div class="dropdown-header">Notifications</div>
            <div class="dropdown-list-content dropdown-list-icons">

              <a href="#" class="dropdown-item dropdown-item-unread"> <span
                  class="dropdown-item-icon bg-primary text-white"> <i class="fas
                      fa-code"></i>
                </span> <span class="dropdown-item-desc"> Template update is
                  available now! <span class="time">2 Min
                    Ago</span>
                </span>
              </a>
           
            </div>
            <div class="dropdown-footer text-center">
              <a href="#">View All <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </li>


             <li class="dropdown"><a href="#" data-toggle="dropdown"class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="{{asset('img/user.png')}}"class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>



            <div class="dropdown-menu dropdown-menu-right pullDown">
            <div class="dropdown-title">Hi, {{ auth()->user()->name }}</div>
              <div class="dropdown-divider"></div>
              <a onclick="event.preventDefault();document.getElementById('logout-form').submit();" href="javascript:void(0);" class="dropdown-item has-icon text-danger logout"><i class="fas fa-sign-out-alt"></i>Logout</a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
          </li>

        </ul>
      </nav>



      <div style="padding-left:15px;" class="main-content">
    @yield('main')
      </div>

  </div>
</div>
  <!-- General JS Scripts -->
  
  
<script src="{{asset('js/app.min.js')}}"></script>
<script src="{{asset('js/scripts.js')}}"></script>
<script src="{{asset('js/custom.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
$(document).ready(function() {
  $('.select').each(function() {
    new TomSelect(this, {
      create: true,
      sortField: {
        field: "text",
        direction: "asc"
      }
    });
})
});
</script>
@yield('js')
</body>
</html>