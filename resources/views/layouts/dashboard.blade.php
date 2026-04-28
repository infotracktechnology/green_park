<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  @yield('meta')
  <title>@yield('title')</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <link rel='shortcut icon' type='image/x-icon' href='{{ asset('img/favicon.png') }}' />

  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('bundles/select2/dist/css/select2.min.css') }}" />

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    /* Table & General Styles */
    thead th {
      background-color: #2b66a2 !important;
      color: #fff !important;
    }
    table th, table td {
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
    /* Alpine Hide until loaded */
    [x-cloak] {
      display: none !important;
    }
  </style>

  @yield('css')
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg" style="background-color: #2b66a2;"></div>

      <?php
        $exam = auth()->user()->GetExam();
        $mockTest = auth()->user()->GetMockTest(); 

        $isExamActive = $exam && ($exam->start_at <= now() && $exam->end_at >= now());
        $isMockActive = $mockTest && ($mockTest->start_at <= now() && $mockTest->end_at >= now());
      ?>

      <!-- TOP NAVIGATION BAR -->
      <nav class="navbar navbar-expand-lg main-navbar sticky" style="background-color: #2b66a2;">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3 align-items-center">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
            <li class="d-none d-md-block"><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i class="fas fa-expand"></i></a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right align-items-center">

          <!-- Current Time Clock -->
          <li class="nav-item dropdown d-none d-md-block">
            <a href="#" class="nav-link nav-link-lg">
              <span class="col-white font-weight-bold" id="clock"></span>
            </a>
          </li>

          <!-- User Profile -->
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ auth()->user()->photo }}" class="user-img-radious-style">
              <span class="d-none d-md-inline-block ml-2 text-white">{{ auth()->user()->user_name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
              <div class="dropdown-title">Hi, {{ auth()->user()->user_name }}</div>
              <div class="dropdown-divider"></div>
              <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="javascript:void(0);" class="dropdown-item has-icon text-danger logout">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </nav>

      <!-- SIDEBAR -->
      <div class="main-sidebar sidebar-style-2" id="sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="#">
              <img alt="image" src="{{ asset('img/logo.jpg') }}" class="header-logo" style="height: 70px;" />
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main Menu</li>

            @if($isExamActive)
            <li class="dropdown active">
              <a href="{{ route('student.instruction', base64_encode($exam->id)) }}" class="nav-link">
                <i class="fas fa-file-alt" style="font-size: 20px; color: #2196f3;"></i><span>Online Exam</span>
              </a>
            </li>
            @elseif($isMockActive)
            <li class="dropdown active">
              <a href="{{ route('student.mock') }}" class="nav-link">
                <i class="fas fa-book-open" style="font-size: 20px; color: #2196f3;"></i><span>Mock Test</span>
              </a>
            </li>
            @else
            <li class="dropdown {{ request()->routeIs('studentdashboard') ? 'active' : '' }}">
              <a href="{{ route('studentdashboard') }}" class="nav-link">
                <i class="fas fa-home" style="font-size: 20px; color: #2196f3;"></i><span>Home</span>
              </a>
            </li>
            <li class="dropdown {{ request()->routeIs('student.profile') ? 'active' : '' }}">
              <a href="{{ route('student.profile') }}" class="nav-link">
                <i class="fas fa-user-circle" style="font-size: 20px; color: #2196f3;"></i><span>Profile</span>
              </a>
            </li>
            @foreach (auth()->user()->menu ?? [] as $menu)
            @if($menu['route'] != '')
            <li class="dropdown {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
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
      @yield('main')

      <!-- FOOTER -->
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
  <script src="{{ asset('js/app.min.js') }}"></script>
  <script src="{{ asset('js/scripts.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>
  <script src="{{ asset('bundles/select2/dist/js/select2.full.min.js') }}"></script>
  <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) window.location.reload(); 
    });
    
    $(document).on('contextmenu', event => event.preventDefault());
    
    $(function () {
      const pad = n => String(n).padStart(2, "0");
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
    
    $(document).on('click', 'a', function(e) {
        let href = $(this).attr('href');
        if (!href || href === '#' || href.startsWith('javascript:')) return;
    
        let isPdf = href.toLowerCase().endsWith('.pdf') || href.toLowerCase().includes('.pdf');
        let isVideo = href.includes('/video/') || $(this).text().toLowerCase().includes('watch');
        let isAttachment = $(this).text().toLowerCase().includes('attachment') || $(this).closest('.notice-board-item-date').length > 0;
        let isDownload = $(this).hasClass('btn-primary') && $(this).text().toLowerCase().includes('download');
    
        if (isPdf || isVideo || isAttachment || isDownload) {
            let modules = $('title').text().trim() || 'Communication';
            let titleText = '';
    
            let $row = $(this).closest('tr');
            if ($row.length > 0) {
                let $tds = $row.find('td');
                if ($tds.eq(0).text().trim().length < 4 && !isNaN($tds.eq(0).text().trim())) {
                    titleText = $tds.eq(1).text().trim() + ($tds.eq(2).length ? '-' + $tds.eq(2).text().trim() : '');
                } else {
                    titleText = $tds.eq(0).text().trim();
                }
            } else if ($(this).closest('.notice-board-item').length > 0) {
                titleText = $(this).closest('.notice-board-item').find('.notice-board-item-title').text().replace('Title :', '').trim();
            }
            
            if (!titleText) titleText = $(this).text().trim() || href.split('/').pop();
            
            let action = 'Seen'+titleText;
    
            $.post('{{ route("student.logActivity") }}', {
                _token: '{{ csrf_token() }}',
                module: modules,
                action: action
            });
        }
    });
  </script>
  @yield('js')
</body>

</html>