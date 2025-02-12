<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title')</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{asset('css/components.css')}}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.png')}}' />
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    .select2 {
      width: 100% !important;
    }

    .error {
      color: red;
      font-weight: bold;
    }

    .nav-link i {
      font-size: 18px;
      margin-right: 10px;
      color: #333;
    }

    .notification-badge {
      display: inline-block;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background-color: red;
      color: white;
      font-weight: bold;
      text-align: center;
      line-height: 20px;
      margin-left: 5px;
      position: absolute;
      top: 10px;
      right: 15px;
    }
  </style>
  @yield('css')
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg bg-blue main-navbar sticky">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i class="fas fa-expand"></i></a></li>
            <li>
              <form class="form-inline mr-auto">
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
          <li class="dropdown dropdown-list-toggle">
            <a href="#" data-bs-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i data-feather="bell" class="bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
              <div class="dropdown-header">Notifications</div>
              <div class="dropdown-list-content dropdown-list-icons">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <span class="dropdown-item-icon bg-primary text-white"><i class="fas fa-code"></i></span>
                  <span class="dropdown-item-desc">
                    Template update is available now! 
                    <span class="time">2 Min Ago</span>
                  </span>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{asset('img/user.png')}}" class="user-img-radious-style">
              <span class="d-sm-none d-lg-inline-block"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
              <div class="dropdown-title">Hi, {{ auth()->user()->user_name }}</div>
              <div class="dropdown-divider"></div>
              <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="javascript:void(0);" class="dropdown-item has-icon text-danger logout">
                <i class="fas fa-sign-out-alt"></i>Logout
              </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="#">
              <img alt="image" src="{{asset('img/favicon.png')}}" class="header-logo" />
              <span class="logo-name">Green Park</span>
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown">
              <a href="{{ route('student.home') }}" class="nav-link">
                <i style="font-size: 20px;color:#2196f3;" class="fas fa-home"></i><span>Home</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="{{ route('student.profile') }}" class="nav-link">
                <i style="font-size: 20px;color:#2196f3;" class="fas fa-user-circle"></i><span>Profile</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color:#2196f3;" class="fas fa-file-alt"></i><span>Online Exam</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="{{ route('student.notification') }}" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-bell"></i><span>Notifications</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="{{ route('student.chairmanvideo') }}" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-video"></i><span>Chairman's Video</span>
              </a>
            </li>
            @if (auth()->user()->coaching_type != 'Offline')
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="	fas fa-play-circle"></i><span>Class video</span>
              </a>
            </li>
            @endif
            <li class="dropdown">
              <a href="{{ route('student.examportion') }}" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-file-pdf"></i><span>Exam Portions</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-chart-bar"></i><span>Mark Details</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-question-circle"></i><span>Question Papers</span>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-key"></i><span>Answer Key</span>
              </a>
            </li>
            {{-- <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-comments"></i><span>Discussion Video</span>
              </a>
            </li> --}}
            <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #5daaf1;" class="fas fa-download"></i><span>Downloads</span>
              </a>
            </li>
            {{-- <li class="dropdown">
              <a href="#" class="nav-link">
                <i style="font-size: 20px;color: #2196f3;" class="fas fa-file-alt"></i><span>Worksheet & Answer Key</span>
              </a>
            </li> --}}
          </ul>
        </aside>
      </div>
      @yield('main')
      <footer class="main-footer">
        <div class="footer-center">
          <a href="http://www.infotrackin.com/its/" target="_blank">
            <span>Copyright &copy; {{ date('Y') }} Version 1.0 - Developed By <b style="color: #27a9e0">Infotrack Technologies</b></span>
          </a>
        </div>
      </footer>
    </div>
  </div>
  <!-- General JS Scripts -->
  <script src="{{asset('js/app.min.js')}}"></script>
  <script src="{{asset('js/scripts.js')}}"></script>
  <script src="{{asset('js/custom.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.select').each(function () {
        new TomSelect(this, {
          create: true,
          sortField: { field: "text", direction: "asc" }
        });
      });
    });
  </script>
  <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload(); 
        }
    });
</script>
  {{-- <script>
    $(document).on('contextmenu', event => event.preventDefault());
    $(document).on('mousedown', event => event.preventDefault());
  </script> --}}
  @yield('js')
</body>
</html>
