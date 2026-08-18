<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  @yield('meta')
  <title>@yield('title')</title>
  
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-S6Y9QBHDM9"></script>
  <script> window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-S6Y9QBHDM9');
  </script>

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
    
    /* Clean Full-Width adjustments for pages without Sidebars */
    body.no-sidebar .main-sidebar {
      display: none !important;
    }
    body.no-sidebar .main-content {
      padding-left: 30px !important;
      padding-right: 30px !important;
    }
    body.no-sidebar .main-navbar {
      left: 0 !important;
      width: 100% !important;
    }
    body.no-sidebar .collapse-btn {
      display: none !important;
    }
    /* Contact Modal */
    #contactModal .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    #contactModal .modal-header {
        background: linear-gradient(135deg, #1e4d7c 0%, #2b66a2 100%);
        border-bottom: none;
    }
    #contactModal .card {
        border: 1px solid #eef2f5;
        border-radius: 10px !important;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    #contactModal .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }
    #contactModal .card-header {
        background-color: #ffffff;
        border-bottom: none;
        padding: 0;
    }
    #contactModal .accordion-btn {
        text-decoration: none !important;
        font-size: 0.95rem;
        font-weight: 600;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #333333;
    }
    #contactModal .accordion-btn:focus {
        box-shadow: none;
    }
    #contactModal .phone-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #f1f5f9;
        color: #2b66a2;
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        margin: 6px;
        transition: all 0.2s ease;
        text-decoration: none !important;
        border: 1px solid #e2e8f0;
    }
    #contactModal .phone-btn:hover {
        background-color: #2b66a2;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(43, 102, 162, 0.25);
    }
    #contactModal .phone-btn i {
        margin-right: 8px;
        font-size: 0.85rem;
    }
  </style>

  @yield('css')
</head>

<body class="@yield('body_class')">
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
             <a href="javascript:void(0);"class="dropdown-item has-icon" data-toggle="modal" data-target="#contactModal"> <i class="fas fa-address-book"></i> Contact Us </a>
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
              <a href="{{ route('student.exam', base64_encode($exam->id)) }}" class="nav-link">
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

      <!-- CONTACT MODAL -->
      <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
              <div class="modal-content">

                  <!-- Header -->
                  <div class="modal-header text-white align-items-center p-3">
                      <div class="d-flex align-items-center flex-wrap">
                          <img src="{{ asset('img/logo.jpg') }}"
                              alt="Green Park Logo"
                              class="rounded mr-3 bg-white p-1 shadow-sm"
                              style="width:160px; height:auto; max-height: 60px; object-fit: contain;">

                          <div class="mt-2 mt-sm-0">
                              <h5 class="mb-0 font-weight-bold">Green Park Coaching Centre</h5>
                              <p class="mb-0 text-white-50 small"></i> CONTACT SUPPORT</p>
                          </div>
                      </div>

                      <button type="button" class="close text-white ml-auto" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                          <span style="font-size:32px;" aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <div class="modal-body p-4 bg-light">
                      <div id="contactAccordion">
                          <div class="card">
                              <div class="card-header" id="offlineHeading">
                                  <button class="btn btn-link btn-block text-left accordion-btn"
                                          data-toggle="collapse"
                                          data-target="#offlineCollapse"
                                          aria-expanded="true">
                                      <span>
                                          <i class="fas fa-school text-primary mr-2"></i>
                                          OFFLINE ADMISSION
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="offlineCollapse" class="collapse show" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9361088506" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9361088506
                                      </a>
                                      <a href="tel:9342441936" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9342441936
                                      </a>
                                      <a href="tel:9342440024" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9342440024
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="card">
                              <div class="card-header">
                                  <button class="btn btn-link btn-block text-left accordion-btn collapsed"
                                          data-toggle="collapse"
                                          data-target="#onlineCollapse">
                                      <span>
                                          <i class="fas fa-globe text-success mr-2"></i>
                                          ONLINE ADMISSION
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="onlineCollapse" class="collapse" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9080274132" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9080274132
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="card">
                              <div class="card-header">
                                  <button class="btn btn-link btn-block text-left accordion-btn collapsed"
                                          data-toggle="collapse"
                                          data-target="#examCollapse">
                                      <span>
                                          <i class="fas fa-laptop text-info mr-2"></i>
                                          ONLINE EXAM / VIDEOS
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="examCollapse" class="collapse" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9360644836" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9360644836
                                      </a>
                                      <a href="tel:9361048223" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9361048223
                                      </a>
                                      <a href="tel:9342554546" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9342554546
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="card">
                              <div class="card-header">
                                  <button class="btn btn-link btn-block text-left accordion-btn collapsed"
                                          data-toggle="collapse"
                                          data-target="#boysCollapse">
                                      <span>
                                          <i class="fas fa-bed text-warning mr-2"></i>
                                          BOYS HOSTEL
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="boysCollapse" class="collapse" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9445869995" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9445869995
                                      </a>
                                      <a href="tel:9361088506" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9361088506
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="card">
                              <div class="card-header">
                                  <button class="btn btn-link btn-block text-left accordion-btn collapsed"
                                          data-toggle="collapse"
                                          data-target="#girlsCollapse">
                                      <span>
                                          <i class="fas fa-home text-danger mr-2"></i>
                                          GIRLS HOSTEL
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="girlsCollapse" class="collapse" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9342441936" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9342441936
                                      </a>
                                      <a href="tel:9342440024" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9342440024
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="card">
                              <div class="card-header">
                                  <button class="btn btn-link btn-block text-left accordion-btn collapsed"
                                          data-toggle="collapse"
                                          data-target="#counsellingCollapse">
                                      <span>
                                          <i class="fas fa-user-md text-primary mr-2"></i>
                                          NEET APPLICATION & MBBS COUNSELLING
                                      </span>
                                      <i class="fas fa-chevron-down text-muted small"></i>
                                  </button>
                              </div>

                              <div id="counsellingCollapse" class="collapse" data-parent="#contactAccordion">
                                  <div class="card-body p-3 d-flex flex-wrap">
                                      <a href="tel:9360191382" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 9360191382
                                      </a>
                                      <a href="tel:6380564568" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 6380564568
                                      </a>
                                      <a href="tel:7200057799" class="phone-btn">
                                          <i class="fas fa-phone-alt"></i> 7200057799
                                      </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      </div>
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
        let modules = $('title').text().trim() || 'Communication';
        let action = $(this).data('action');
        if(modules == 'Mark Details' || modules == 'MBBS/BDS Counselling' || modules == 'Student Download') return;
        if (!action || action == '' || action == undefined) return;
            $.post('{{ route("student.logActivity") }}', {
                _token: '{{ csrf_token() }}',
                module: modules,
                action: action,
                student_id : '{{ auth()->user()->student_id }}'
            });
    });
  </script>
  @yield('js')
</body>
</html>