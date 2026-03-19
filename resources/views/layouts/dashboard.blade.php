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
  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="{{asset('bundles/select2/dist/css/select2.min.css')}}" />
  <style>
    thead th{
      background-color: #2b66a2 !important;
       color: #fff !important;
    }
    table th,table td {
      border: 1px solid #222 !important;
      height: 45px !important;
    }
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
    /* Hide Alpine components until loaded */
    [x-cloak] {
      display: none !important;
    }
      
    /* Timer Banner Styling */
    .timer-banner {
      background-color: #343a40; /* Dark background */
      color: white; 
      padding: 10px 15px; 
      text-align: center; 
      font-size: 16px; 
      font-weight: bold; 
      width: 100%; 
      z-index: 890; 
      position: relative;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .timer-banner .time-highlight {
      color: #ffc107; /* Warning yellow for the time */
      font-size: 18px;
      margin-left: 5px;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 767px) {
      .navbar-brand-mobile { max-width: 150px; }
      .timer-banner { font-size: 14px; }
      .timer-banner .time-highlight { font-size: 16px; }
    }
  </style>
  @yield('css')
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php
        $exam = auth()->user()->GetExam();
        $mockTest = auth()->user()->GetMockTest(); 

        $isExamActive = $exam && ($exam->start_at <= now() && $exam->end_at >= now());
        $isMockActive = $mockTest && ($mockTest->start_at <= now() && $mockTest->end_at >= now());

        $upcomingTestTime = null;
        $upcomingTestName = '';

        if ($exam && $exam->start_at > now()) {
            $upcomingTestTime = $exam->start_at->toIso8601String();
            $upcomingTestName = 'Exam';
        } elseif ($mockTest && $mockTest->start_at > now()) {
            $upcomingTestTime = $mockTest->start_at->toIso8601String();
            $upcomingTestName = 'Mock Test';
        }
      ?>

      <!-- TOP NAVIGATION BAR -->
      <nav class="navbar navbar-expand-lg main-navbar sticky" style="background-color: #2b66a2;">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3 align-items-center">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
            <!-- Hidden fullscreen button on mobile to save space -->
            <li class="d-none d-md-block"><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i class="fas fa-expand"></i></a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right align-items-center">

          <!-- Hidden standard clock on mobile to save space -->
          <li class="nav-item dropdown d-none d-md-block">
            <a href="#" class="nav-link nav-link-lg">
              <span class="col-white" id="clock"></span>
            </a>
          </li>

          <!-- User Profile -->
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ auth()->user()->photo }}" class="user-img-radious-style">
              <!-- Username only shows on tablet/desktop to save mobile space -->
              <span class="d-none d-md-inline-block ml-2 text-white">{{ auth()->user()->user_name }}</span>
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

      <!-- NEW: FULL WIDTH TIMER BANNER -->
      <!-- Placed right below the navbar to prevent mobile overlap issues -->
      <div x-data="testCountdown('{{ $upcomingTestTime }}', '{{ $upcomingTestName }}')" x-show="isActive" x-cloak class="timer-banner">
        <span style="font-size: 18px; margin-right: 5px;">⏰</span>
        <span x-text="testName"></span> starts in <span class="time-highlight" x-text="timeLeft"></span>
      </div>

      <!-- SIDEBAR -->
      <div class="main-sidebar sidebar-style-2" id="sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="#">
              <img alt="image" src="{{asset('img/logo.jpg')}}" class="header-logo" style="height: 70px;" />
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>

            @if($isExamActive)
            <li class="dropdown">
              <a href="{{ route('student.instruction',base64_encode($exam->id)) }}" class="nav-link">
                <i class="fas fa-file-alt" style="font-size: 20px; color: #2196f3;"></i><span>Online Exam</span>
              </a>
            </li>
            @elseif($isMockActive)
            <li class="dropdown">
              <a href="{{ route('student.mock') }}" class="nav-link">
                <i class="fas fa-book-open" style="font-size: 20px; color: #2196f3;"></i><span>Mock Test</span>
              </a>
            </li>
            @else
            <!-- Show regular menu elements if no test is currently active -->
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

      <!-- MAIN CONTENT AREA -->
      <!-- NOTE: Add the class "force-wrap" to any text inside your views (like GPCA,COIMBATORE) that is breaking off the screen -->
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
  <script src="{{asset('bundles/select2/dist/js/select2.full.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/easytimer.js/dist/easytimer.min.js"></script>

  <script>
    // Alpine.js Timer logic
    document.addEventListener('alpine:init', () => {
      Alpine.data('testCountdown', (startTime, testName) => ({
          isActive: false,
          timeLeft: '',
          testName: testName || 'Exam',
          
          init() {
              if (!startTime) return;
    
              const startTs = new Date(startTime).getTime();
              if (isNaN(startTs)) return;
    
              const updateTimer = () => {
                  const now = Date.now();
                  const diff = startTs - now;
    
                  if (diff <= 0) {
                      this.isActive = false;
                      location.reload(); // Auto refresh the page when Exam/Mock Test starts
                      return true; // Flag to clear interval
                  }
    
                  const h = Math.floor(diff / 3600000);
                  const m = Math.floor((diff % 3600000) / 60000);
                  const s = Math.floor((diff % 60000) / 1000);
    
                  this.timeLeft = `${h ? h + 'h ' : ''}${m ? m + 'm ' : ''}${s}s`;
                  this.isActive = true;
                  return false; // Flag to keep interval going
              };
    
              // Initialize immediately, set Interval if time still remaining
              if (!updateTimer()) {
                  const timerInterval = setInterval(() => {
                      if (updateTimer()) clearInterval(timerInterval);
                  }, 1000);
              }
          }
      }));
    });
    
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload(); 
        }
    });
    $(document).on('contextmenu', event => event.preventDefault());
    
    $(function () {
      const pad = n => String(n).padStart(2, "0");
      
      // Standard Date/Time Clock
      function initClock() {
          const $clock = $("#clock");
          function renderClock() {
              const d = new Date();
              $clock.text(d.toLocaleDateString("en-GB") + " " +`${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`);
          }
          renderClock();
          setInterval(renderClock, 1000);
      }
      initClock();
    });
  </script>
  @yield('js')
</body>
</html>