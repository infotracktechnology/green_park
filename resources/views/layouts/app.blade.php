<!DOCTYPE html>
<html lang="en">
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
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.png')}}' />
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    .select2 {
      width: 100% !important;
    }

    .error {
      color: red;
      font-weight: bold;
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
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
            <li>
              <form class="form-inline mr-auto" action="{{ route('admin.home') }}" method="get">
                <select name="academic_year" id="academic_year" onchange="this.form.submit();" class="form-control" required>
                  @foreach (\App\Models\AcademicYear::all() as $row)
                      <option value="{{ $row->academic_year }}" @selected($row->active)>{{ $row->academic_year }}</option>
                  @endforeach
              </select>
                {{-- <div class="search-element">
                  <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="200">
                  <button class="btn" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div> --}}
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
              <div class="dropdown-title">Hi, {{ auth()->user()->username }}</div>
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
              <span class="logo-name">{{ env('APP_NAME') }}</span>
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown">
              <a href="{{ route('admin.home') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
            </li>
            <li class="dropdown">
              <a href="{{ route('academicyear.index') }}" class="nav-link"><i data-feather="calendar"></i><span>Academic Year</span></a>
            </li>

            <li class="dropdown">
              <a href="{{ route('branch.index') }}" class="nav-link"><i data-feather="grid"></i><span>Branches</span></a>
            </li>
            <li class="dropdown">
              <a href="{{ route('users.index') }}" class="nav-link"><i data-feather="user"></i><span>Users</span></a>
            </li>
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="users"></i><span>Students Details </span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('student.index') }}" class="nav-link">Add Students</a></li>
                <li><a href="{{ route('export.student') }}" class="nav-link">Export Students</a></li>
                <li><a href="{{ route('import.student') }}" class="nav-link">Import Students</a></li>
                {{-- <li><a href="{{ route('section.student') }}" class="nav-link">Section Shuffling</a></li> --}}
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="clipboard"></i><span>Student App Menu</span></a>
              <ul class="dropdown-menu">
                {{-- <li><a href="{{ route('studentmenu.branch') }}" class="nav-link"> Branch Assign</a></li> --}}
                <li><a href="{{ route('studentmenu.type') }}" class="nav-link"> Type Assign</a></li>
                <li><a href="{{ route('studentmenu.student') }}" class="nav-link"> Student Assign</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="user-check"></i><span>Staff Profile</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('staff.index') }}" class="nav-link"> Staff</a></li>
                <li><a href="{{ route('staff.class') }}" class="nav-link"> Class Assign</a></li>
               
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="message-square"></i><span>Communication</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('announcement.index') }}" class="nav-link"> Announcement</a></li>
                <li><a href="{{route('chairmanvideo.index')}}"  class="nav-link"> Chairman Video</a></li>
                <li><a href="{{route('classvideo.index')}}"  class="nav-link"> Class Video</a></li>
                <li><a href="{{route('revisionvideo.index')}}"  class="nav-link"> Revision Video</a></li>
               
                {{-- <li><a href="" class="nav-link">Class Video upload</a></li> --}}

                <li><a href="{{route('examportion.index')}}"  class="nav-link"> Exam Portion</a></li>
                <li><a href="{{route('questionkey.index')}}"  class="nav-link"> Question Paper</a></li>
                <li><a href="{{route('answerkey.index')}}"  class="nav-link"> Answer key</a></li>
                <li><a href="{{route('discussionvideo.index')}}"  class="nav-link"> Discussion Video</a></li>
                <li><a href="{{ route('download.index') }}" class="nav-link">Downloads</a></li>
                <li><a href="{{ route('worksheet.index') }}" class="nav-link">Worksheet</a></li>
                <li><a href="{{ route('achievement.index') }}" class="nav-link">NEET Achievement</a></li>
                <li><a href="{{ route('parent_concern') }}" class="nav-link">Parent Concern</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="clipboard"></i><span>Examination</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('exam.index') }}" class="nav-link"> Add Test</a></li>
                <li><a href="{{ route('exam.enable') }}" class="nav-link">Enable Test</a></li>
                <li><a href="{{ route('exam.test') }}" class="nav-link">Test Report</a></li>
                <li><a href="{{route('exam.offline.upload')}}" class="nav-link">Offline/OMR Upload</a></li>
                <li><a href="{{route('exam.answerkey')}}" class="nav-link">Answer key Upload</a></li>
                <li><a href="{{ route('exam.report.dump') }}" class="nav-link">Result Report</a></li>\
                <li><a href="{{ route('report.section_exam') }}" class="nav-link">Exam Section Wise</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="home"></i><span>Hostel</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('hostel.index') }}" class="nav-link">Add Hostel</a></li>
                <li><a  href="{{ route('allocation.hostel') }}" class="nav-link">Hostel Allocation</a></li>
                <li><a href="{{ route('sickroom.index') }}" class="nav-link">Sick Room Entry</a></li>
                <li><a href="{{ route('hostelattendance') }}" class="nav-link">Hostel Attendance </a></li>
                <li><a href="{{ route('studentactivity.index') }}" class="nav-link">Student Activity</a></li>


              </ul>
            </li>

            
            <li class="dropdown">
              <a href="{{ route('timetable.index') }}" class="nav-link"><i data-feather="clock"></i><span>TimeTable</span></a>
            </li>

            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="save"></i><span>Attendance</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('holiday.index') }}" class="nav-link">Add Holiday</a></li>
                <li><a href="{{ route('attendance') }}" class="nav-link">Attendance Entry</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="dollar-sign"></i><span>Finance</span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('feetype') }}" class="nav-link">Fee Types</a></li>
                <li><a href="{{ route('feesplan.index') }}" class="nav-link">Fees Plan</a></li>
                <li><a href="{{ route('fees.collection') }}" class="nav-link">Fees Collection</a></li>

              </ul>
            </li>

            

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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.select').each(function () {
        new TomSelect(this, {
          create: true,
          plugins: ['remove_button'],
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
