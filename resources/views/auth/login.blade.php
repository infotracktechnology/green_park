<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/login.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.png')}}' />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
  
  
    .swiper-slide {
      background-size: cover;
      background-position: center;
    }
    
    .swiper-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    

   
    
    /* Add a caption overlay for slides */
    .slide-caption {
      position: absolute;
      bottom: 30px;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 10px;
      text-align: center;
    }
  </style>
</head>
<body>

    <div class="login-13">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-7 col-md-12 bg-img">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                          <div class="swiper-slide">
                            <img src="https://www.gpccnamakkal.com/img/slide2.jpg" alt="Slide 1">
                            
                          </div>
                          <div class="swiper-slide">
                            <img src="https://www.gpccnamakkal.com/img/slide2.jpg" alt="Slide 2">
                            
                          </div>
                          <div class="swiper-slide">
                            <img src="https://www.gpccnamakkal.com/img/slide2.jpg" alt="Slide 3">
                            
                          </div>
                        </div>
                      
                        <!-- Add pagination -->
                        <div class="swiper-pagination"></div>
                        
                        <!-- Add navigation buttons -->
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    
                        <!-- Add scrollbar -->
                        <div class="swiper-scrollbar"></div>
                      </div>
                </div>
                <div class="col-lg-5 col-md-12 form-info">
                    <div class="form-section">
                       
                        <div class="form-section-innner">
                            <div class="logo clearfix">
                                
                                <img alt="image" src="{{asset('img/logo.png')}}" style="height:80px;"  /> 
                                
                               
                            </div>
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger fade show" role="alert">
                                 {{ $message }}
                            </div>
                        @endif
                            <h3>Login</h3>
                        
                            <div class="login-inner-form">
                                <form method="POST" action="{{ route('auth.login') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group form-box clearfix">
                                        <input name="username" type="text" class="form-control" placeholder="Enter Username" aria-label="Username" required>
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
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <script src="{{asset('js/app.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper('.swiper', {
      // Enable auto sliding
      autoplay: {
        delay: 5000, // 5 seconds between slides
        disableOnInteraction: false, // Continue autoplay after user interaction
      },
      effect: 'fade', // Use fade transition between slides
      fadeEffect: {
        crossFade: true
      },
      direction: 'horizontal',
      loop: true,
      // Enable pagination
      pagination: {
        el: '.swiper-pagination',
        clickable: true, // Allow clicking on pagination bullets
      },
      // Enable navigation arrows
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      // Add scrollbar
      scrollbar: {
        el: '.swiper-scrollbar',
        draggable: true, // Make scrollbar draggable
      },
    });
  </script>
</body>
</html>