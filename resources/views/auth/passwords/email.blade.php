<!DOCTYPE html>
<html lang="en">
<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Auto Moblie </title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
  <link rel="stylesheet" href="{{asset('bundles/bootstrap-social/bootstrap-social.css')}}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{asset('css/components.css')}}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel='shortcut icon' type='image/x-icon' href='{{asset('img/favicon.ico')}}' />
</head>
<body>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-lg-6 offset-lg-2">
            <div class="card card-primary">
              <div class="card-header">
                  
                <h4>{{ __('Reset Password') }}</h4>
                
              </div>
  

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row">    
                        <div class="col-12">   
                          <div class="form-group col-12 mb-4">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email"  required autocomplete="email" autofocus>
        
                            @error('email')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
        
                          </div>

                          <div class="form-group col-12 mb-4">
                            <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                {{ __('Send Password Reset Link') }}
                            </button>
                          </div>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

 
 
</section>

</div>
<!-- General JS Scripts -->
<script src="{{asset('js/app.min.js')}}"></script>
<!-- JS Libraies -->
<!-- Page Specific JS File -->
<!-- Template JS File -->
<script src="{{asset('js/scripts.js')}}"></script>
<!-- Custom JS File -->
<script src="{{asset('js/custom.js')}}"></script>
</body>
</html>