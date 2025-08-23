<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login</title>

  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
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

  <div class="container-fluid" style="border-bottom: none;padding-top: 10px;padding-bottom: 10px;">
        <div class="row">
            <div class="col-md-9">
                   <img alt="image" src="{{asset('img/logo.png')}}" style="height:80px;"  />
            </div>

            <div class="col-md-3">
                    <h6 class="mb-1 text-center">Support<br>(+91) 91883 99999</h6>
            </div>
        </div>
    </div>

  
        <div class="container-fluid" style="background-image: url('{{asset('img/home-bg.png')}}'); background-size: 100% 100%;">
            <div class="row" style="padding-bottom: 140px;">
                <div class="col-md-7">
                    <div class="mt-3">
                        <div class="container">
                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <h1 class="text-white head-text mt-5">
                                        GREEN PARK<br>
                                        {{-- COACHING CENTRE<br>
                                        WHEN YOU CAN<br>
                                        DO MORE? --}}
                                    </h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-white">
                                        Get on your mobile - Download now
                                    </p>
                                    <p>
                                        <a href="https://play.google.com/store/apps/details?id=com.gpcc.gpcc" target="_blank"><img src="https://cdndataicici.myclassboard.com/Assets/img/ICICI-googleapp.png" class="img-fluid"></a>
                                        <a href="https://apps.apple.com/app/id6748722000" target="_blank"><img src="https://cdndataicici.myclassboard.com/Assets/img/ICICI-Iosapp.png" class="img-fluid"></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                                        
                </div>

                <div class="col-md-4" style="margin-top: 60px;">
                    <div class="card" style="border-radius:12px;padding: 30px 0px;">
                        <div class="card-body">
                            <h4 class="title text-center" style="color: #0b3c6d;">LOGIN</h4>
                        </div>
                         <form method="POST" action="{{ route('auth.login') }}" enctype="multipart/form-data">
                        @csrf

                                   <div class="col s12 mt-3">
                                      @if ($message = Session::get('error'))
                                    <div class="alert alert-danger fade show" role="alert">
                                            {{ $message }}
                                        </div>
                                    @endif
                                   </div>


                              <div class="col s12 mt-3">
                                    <div class="w-100">
                                        <label for="email" style="color: #0b3c6d;">Username</label>
                                         <input name="username" type="text" class="form-control" placeholder="Username/Email" aria-label="Username" required>
                                    </div>
                                </div>

                                  <div class="col s12 mt-3">
                                    <div class="w-100">
                                        <label for="email" style="color: #0b3c6d;">Password</label>
                                        <input name="password" type="password" class="form-control" autocomplete="off" placeholder="Enter Password" aria-label="Password" required>
                                    </div>
                                </div>


                                  <div class="col s12" style="margin-top: 20px;">
                                    <div class="w-100">
                                       <button class="btn btn-block btn-success text-uppercase" type="submit" style="border-radius: 12px !important;padding: 10px 0px;">
                                            Login
                                        </button>
                                    </div>
                                </div>
                            </form>

                    </div>

                </div>
            </div>
        </div>

 
        <div class="footer" style="background: #f0f0f0;padding: 20px 0px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <p>
                        © Copyrights {{ date('Y') }} All rights reserved.  Powered by  <a class="link" href="#" target="_blank">Green Park Coaching Centre</a>
                    </p>
                </div>
                
            </div>
        </div>
    </div>

    

  <script src="{{asset('js/app.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper('.swiper', {
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      direction: 'horizontal',
      loop: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      scrollbar: {
        el: '.swiper-scrollbar',
        draggable: true,
      },
    });
  </script>
</body>
</html>