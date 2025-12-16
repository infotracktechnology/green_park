<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  @yield('meta')
  <title>@yield('title')</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{asset('css/components.css')}}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.png')}}' />
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

    .hidden {
      display: none !important;
    }
  </style>
  @yield('css')
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar sticky" style="background-color: #2b66a2;">
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

          <li class="nav-item dropdown">
            <a href="#" class="nav-link nav-link-lg">
                <span class="col-white" id="clock"></span>
            </a>
        </li>
        

          <li class="nav-item dropdown">
            <a href="#" class="nav-link nav-link-lg" id="exam-timer-container" style="display: none;">
                <span style="font-size: 24px;">⏰</span> Exam starts in <span id="exam-timer" class="col-white"></span>
            </a>
        </li>
        
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
      <div class="main-sidebar sidebar-style-2" id="sidebar" >
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                 <a href="#">
              <img alt="image" src="{{asset('img/logo.jpg')}}" class="header-logo" style="height: 70px;"/>
              {{-- <span class="logo-name">{{ env('APP_NAME') }}</span> --}}
            </a>
            </div>
            <ul class="sidebar-menu">
              <li class="menu-header">Main</li>
              <?php
              $exam = auth()->user()->GetExam();
              ?>
              @if($exam)
              <li class="dropdown">
                <a href="{{ route('student.instruction',base64_encode($exam->id)) }}" class="nav-link">
                  <i class="fas fa-file-alt" style="font-size: 20px; color: #2196f3;"></i><span>Online Exam</span>
                </a>
              </li>
              @else
                <li class="dropdown">
                  <a href="{{ route('studentdashboard') }}" class="nav-link">
                    <i class="fas fa-home" style="font-size: 20px; color: #2196f3;"></i><span>Home</span>
                  </a>
                </li>
                <li class="dropdown">
                  <a href="{{ route('student.profile') }}" class="nav-link">
                    <i class="fas fa-user-circle" style="font-size: 20px; color: #2196f3;"></i><span>Profile</span>
                  </a>
                </li>
                @foreach (auth()->user()->menu ?? [] as $menu)
                @if($menu['route'] !='')
                <li class="dropdown">
                  <a href="{{ route($menu['route']) }}" class="nav-link">
                    <i class="{{ $menu['icon'] }}" style="font-size: 20px; color: #2196f3;"></i><span>{{ $menu['title'] }}</span>
                  </a>
                </li>
                @endif
                @endforeach

              @endif
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
  <script src="https://cdn.jsdelivr.net/npm/easytimer.js/dist/easytimer.min.js"></script>
  
  <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload(); 
        }
    });
</script>
  <script>
    $(document).on('contextmenu', event => event.preventDefault());
   
   document.addEventListener("DOMContentLoaded", function () {
    var examStartTime = "{{ $examStartTime ?? '' }}";

    if (!examStartTime) {
        console.log("No upcoming exams.");
        return;
    }

    var examStart = new Date(examStartTime).getTime();
    var timerElement = document.getElementById('exam-timer');
    var timerContainer = document.getElementById('exam-timer-container');

    function updateTimer() {
        var now = new Date().getTime();
        var timeDiff = examStart - now;

        if (timeDiff <= 0) {
            timerElement.innerHTML = "Exam has started!";
            timerContainer.style.display = "none";
            clearInterval(timerInterval);
            return;
        }

        var hours = Math.floor(timeDiff / (1000 * 60 * 60));
        var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        var timeString = (hours > 0 ? hours + "h " : "") + 
                         (minutes > 0 ? minutes + "m " : "") + 
                         seconds + "s";

        timerElement.innerHTML = timeString;
        timerContainer.style.display = "inline";
    }

    var timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
});

      const timetimer = new easytimer.Timer();
  
            function updateClock() {
                const now = new Date();
                const time = now.toLocaleTimeString(undefined, { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const date = now.toLocaleDateString('en-GB', {day: '2-digit',month: '2-digit',year: 'numeric'});
                const formattedTime = `${date} ${time}`;
                document.getElementById('clock').textContent = formattedTime;
            }
            
            updateClock();
            
            timetimer.addEventListener('secondsUpdated', updateClock);
            timetimer.start({ precision: 'seconds' });

</script>

  @yield('js')
</body>
</html>
