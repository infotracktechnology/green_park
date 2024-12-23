<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>ERP System Student Login</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/login.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.png')}}' />
</head>
<body>

    <div class="login-13">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-12 bg-img">
                    <div class="bg-img-inner">
                        <div class="info">
                            <div class="center">
                                <h1>Welcome To GPCC</h1>
                            </div>
                            <p>Green Park Group of Educational Institutions are always known for their Academic Accomplishments in securing admissions into Professional courses. With the Central government making NEET mandatory for MBBS admissions, we have started Green Park Coaching Centre in 2017 to provide the students with extensive, exceptional, and efficacious coaching to crack NEET with effortless ease.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 form-info">
                    <div class="form-section">
                        <div class="form-section-innner">
                            <div class="logo clearfix">
                                
                                <img alt="image" src="{{asset('img/logo.png')}}" style="height:80px;"  /> 
                                
                               
                            </div>
                            <h3>Student Login</h3>
                        
                            <div class="login-inner-form">
                                <form action="#" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group form-box clearfix">
                                        <input name="username" type="text" class="form-control" placeholder="Enter UserID" aria-label="Username" required>
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="form-group form-box clearfix">
                                        <input name="password" type="password" class="form-control" autocomplete="off" placeholder="Enter Password" aria-label="Password" required>
                                        <i class="fa fa-lock"></i>
                                    </div>
                                   
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-theme">Login</button>
                                    </div>
                                </form>
                            </div>
                            {{-- <ul class="social-list">
                                <li><a href="#" class="facebook-color"><i class="fa fa-facebook facebook-i"></i><span>Facebook</span></a></li>
                                <li><a href="#" class="twitter-color"><i class="fa fa-twitter twitter-i"></i><span>Twitter</span></a></li>
                                <li><a href="#" class="google-color"><i class="fa fa-google google-i"></i><span>Google</span></a></li>
                            </ul> --}}
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <script src="{{asset('js/app.min.js')}}"></script>
</body>
</html>