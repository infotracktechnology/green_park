<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Green Park | Login </title>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-S6Y9QBHDM9"></script>
  <script> window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-S6Y9QBHDM9');
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{asset('css/login.css')}}">
  <link rel="icon" href="{{asset('img/favicon.png')}}" type="image/png">
  <link rel="shortcut icon" href="{{asset('img/favicon.png')}}" type="image/png')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>

  <header class="site-header">
    <div class="logo">
      <!-- use your logo path -->
      <img src="{{asset('img/logo.jpg')}}" alt="Green Park logo">
    </div>
    <button class="toggler pro-toggler contact-btn" id="navToggler">
      <i class="fa fa-phone"></i>
      <span>Contact</span>
    </button>
  </header>


  <!-- ====== SIDEPANEL HTML ====== -->
  <!-- Side panel -->
  <aside id="sidePanel" class="side-panel pro" aria-hidden="true" role="dialog">
    <header class="side-panel__head">
      <button class="side-close" aria-label="Close">&times;</button>
    </header>
    <div class="side-panel__body">
      <!-- OFFLINE ADMISSION -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>OFFLINE ADMISSION</strong>
            <small>Office numbers</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <p></p>
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919361088506">9361088506</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919342441936">9342441936</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919342440024">9342440024</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- ONLINE ADMISSION -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>ONLINE ADMISSION</strong>
            <small>Apply online / Helpdesk</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919080274132">9080274132</a>
            </li>
          </ul>
        </div>
      </div>

       <!-- Regarding Online Exam -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>Regarding Online Exam,Students Data Updation,Online Class Videos</strong>
            <small>Apply online / Helpdesk</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919360644836">93606 44836</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919361048223">93610 48223</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919342554546">93425 54546</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- BOYS HOSTEL -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>BOYS HOSTEL</strong>
            <small>Students currently studying</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919445869995">9445869995</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919361088506">9361088506</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- GIRLS HOSTEL -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>GIRLS HOSTEL</strong>
            <small>Students currently studying</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919342441936">9342441936</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919342440024">9342440024</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- COUNSELLING -->
      <div class="acc-item">
        <button class="acc-btn" aria-expanded="false">
          <span class="acc-text">
            <strong>NEET APPLICATION & MBBS COUNSELLING</strong>
            <small>Expert guidance & counselling</small>
          </span>
          <span class="acc-meta">
            <svg class="call-icon" viewBox="0 0 24 24">
              <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
            </svg>
            <i class="chev"></i>
          </span>
        </button>
        <div class="acc-content">
          <ul class="contact-list">
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+919360191382">9360191382</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+916380564568">6380564568</a>
            </li>
            <li>
              <svg class="phone-icon" viewBox="0 0 24 24">
                <path d="M6.6 2l3.1.6c.4.1.7.3.9.7l1.4 3c.3.6.1 1.3-.4 1.7l-1.8 1.4c1 2 2.7 3.7 4.8 4.8l1.4-1.8c.4-.5 1.1-.7 1.7-.4l3 1.4c.4.2.6.5.7.9l.6 3.1c.2.8-.3 1.6-1.1 1.8-3.3.7-11-2.5-14.3-5.8S5.9 3.3 6.6 2z" />
              </svg>
              <a href="tel:+917200057799">7200057799</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </aside>
  <div id="sideOverlay" class="side-overlay" aria-hidden="true"></div>

  <section class="hero">
    <div class="hero-inner">
      <div class="col-left col">
        <!-- SLIDER + LIGHTBOX (paste into your page where slider is) -->
        <div class="slider-card">
          <div class="slider-frame">
            <div class="slider" id="slider">
              <!-- Slides -->
              <div class="slides" id="slides">
                <!-- Slide -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-5.jpg')}}" alt="slide 1">
                  <div class="slide-info">
                    <div class="slide-text">BRINGING DREAMS TO REALITY</div>

                  </div>
                </div>
                <!-- Slide -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-12.jpeg')}}" alt="slide 1">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2026</div>
                    <!-- data-light contains the image to show in lightbox -->
                    <a href="#" class="slide-btn open-lightbox" data-light="#">College List</a>
                  </div>
                </div>

                <div class="slide">
                  <img src="{{asset('img/silde/slide-11.jpg')}}" alt="slide 1">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2025</div>
                    <!-- data-light contains the image to show in lightbox -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2025b.jpg')}}">College List</a>
                  </div>
                </div>
                <!-- Slide 1 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-2.jpg')}}" alt="slide 1">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2024</div>
                    <!-- data-light contains the image to show in lightbox -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/gpcc24.jpg')}}">College List</a>
                  </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-3.jpg')}}" alt="slide 2">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2023</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2023b.jpg')}}">College List</a>
                  </div>
                </div>
                <!-- Slide 4 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-4.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2022</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2022b.jpg')}}">College List</a>
                  </div>
                </div>
                <!-- Slide 6 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-6.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2021</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2021b.jpg')}}">College List</a>
                  </div>
                </div>

                <!-- Slide 7 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-7.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2020</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2020b.jpg')}}">College List</a>
                  </div>
                </div>

                <!-- Slide 8 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-8.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2019</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2019b.jpg')}}">College List</a>
                  </div>
                </div>

                <!-- Slide 9 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-9.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2018</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2018b.jpg')}}">College List</a>
                  </div>
                </div>

                <!-- Slide 10 -->
                <div class="slide">
                  <img src="{{asset('img/silde/slide-10.jpg')}}" alt="slide 3">
                  <div class="slide-info">
                    <div class="slide-text">Batch 2017</div>
                    <!-- change this path to your real image -->
                    <a href="#" class="slide-btn open-lightbox" data-light="{{asset('img/silde/2017b.jpg')}}">College List</a>
                  </div>
                </div>

              </div>
              <!-- Arrow controls -->
              <div class="slider-controls" aria-hidden="false">
                <button class="btn-ctrl" id="prev" aria-label="Previous">
                  <svg viewBox="0 0 24 24">
                    <path d="M14.7 17.3a1 1 0 0 1-1.4 1.4l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 1 1 1.4 1.4L10.4 12l4.3 5.3z" />
                  </svg>
                </button>
                <button class="btn-ctrl" id="next" aria-label="Next">
                  <svg viewBox="0 0 24 24">
                    <path d="M9.3 6.7a1 1 0 0 1 1.4-1.4l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 1 1-1.4-1.4L13.6 12 9.3 7.7z" />
                  </svg>
                </button>
              </div>
              <!-- Slider dots -->
              <div class="slider-dots" id="sliderDots">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
                <span class="dot" data-index="2"></span>
              </div>
            </div>
          </div>
        </div>
        <!-- LIGHTBOX: simple original-size preview -->
        <div id="lightbox" class="simple-lightbox" aria-hidden="true" role="dialog" aria-label="Image preview">
          <button class="lb-close" aria-label="Close preview">&times;</button>
          <!-- stage acts as a scrollable viewport when image is bigger than viewport -->
          <div class="lb-stage" tabindex="0">
            <img src="" alt="Preview" class="lb-original">
          </div>
        </div>

      </div>
      <div class="col-right col">
        <div class="login-card" role="region" aria-label="Login form">
          <h3>LOGIN</h3>
          <div>
            @if(session('error'))
            <div class="alert alert-danger fade show" role="alert">
              {{ session('error') }}
            </div>
            @endif
          </div>
          <form method="POST" action="{{ route('auth.login') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
              <label for="username">Username</label>
              <input id="username" name="username" type="text" placeholder="Username/Email" required>
            </div>
            <div class="form-row">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" placeholder="Enter Password" required>
            </div>
            <button class="btn-login" type="submit">LOGIN</button>
          </form>
          <div class="dived"></div>
          <hr>
          {{-- <div class="app1">
            <h4 class="app_txt">GPCC</h4>
            <div class="small">Get on your mobile - Download now</div>
            <div class="download-links">
              <a href="https://play.google.com/store/apps/details?id=com.gpcc.gpcc" target="_blank"><img src="{{asset('img/silde/google-play.png')}}" alt="Google Play">
                <a href="https://apps.apple.com/us/app/gpcc/id6748722000" target="_blank"><img src="{{asset('img/silde/app-store.png')}}" alt="App Store"></a>
            </div>
          </div> --}}

          {{-- Admission Details --}}
            <div class="app1 mt-3">
                <h4 class="text-center text-primary fw-bold mb-3">Admission Details</h4>
                <table class="table table-borderless mb-0 text-center align-middle">
                    <tr>
                        <td width="45%">
                            <i class="fas fa-headset fa-2x text-primary "></i><br>
                            <strong class="text-primary">Online Admission</strong><br><br>
                            9080274132
                            <br><br><br>
                        </td>
                        <td width="10%">
                            <div style="width:1px;height:120px;background:#ddd;margin:auto;"></div>
                        </td>
                        <td width="45%">
                            <i class="fas fa-users fa-2x text-primary "></i><br>
                            <strong class="text-primary">Offline Admission</strong><br><br>
                            9361088506<br>
                            9342441936<br>
                            9342440024
                        </td>
                    </tr>
                </table>
            </div>
            
        </div>
      </div>
    </div>
  </section>

  <footer class="gp-footer">
    <div class="gp-footer-container">

      <!-- COLUMN 1 -->
      <div class="gp-footer-col">
        <img src="{{asset('img/logo.jpg')}}" alt="GPCC Logo" class="gp-footer-logo">
        <p class="gp-footer-text font-footer">
          “Bringing Dreams to Reality”, being our tagline, GPCC has set a benchmark for its
          reliability and educational guidance for those who aspire to clear medical entrance
          exams with top ranks. We never rest on our laurels and our march toward the pinnacle
          of glory continues.
        </p>
      </div>

      <!-- COLUMN 2 -->
      <div class="gp-footer-col">
        <h4 class="gp-footer-title">Courses</h4>
        <ul class="gp-footer-list">
          <li>LONGTERM ONE YEAR PROGRAM</li>
          <li>LONGTERM ONLINE RECORDED VIDEO CLASS</li>
          <li>LONGTERM ONLINE LIVE CLASS</li>
          <li>LONGTERM ONLINE TEST SERIES</li>
          <li>TWO YEARS OFFLINE CLASSROOM PROGRAM</li>
          <li>TWO YEARS ONLINE CLASSROOM PROGRAM</li>
          <li>CRASH COURSE PROGRAM</li>
        </ul>
      </div>

      <!-- COLUMN 3 -->
      <div class="gp-footer-col">
        <h4 class="gp-footer-title">Branches</h4>
        <ul class="gp-footer-list">
          <li>GREEN PARK CAREER INSTITUTE, KARUR</li>
          <li>GREEN PARK CAREER INSTITUTE, ERODE</li>
          <li>GP ACADEMY, CHENNAI</li>
          <li>GREEN PARK CAREER ACADEMY, KOVAI</li>
        </ul>
      </div>

      <!-- COLUMN 4 -->
      <div class="gp-footer-col no-bullet">

        <h4 class="gp-footer-title">Communication Address</h4>
        <ul class="gp-footer-list">


          <li>
            <b>Address:</b><br>
            NO 106/3A & 106/2, POSTAL NAGAR,<br> BODHUPATTY, NAMAKKAL-637003
          </li><br>
          <li><b>Email:</b> gpccnkl@gmail.com</li>
        </ul>

        <div class="gp-footer-apps">
          <p class="gp-footer-app-text">Get on your mobile – Download now</p>

          <a href="https://play.google.com/store/apps/details?id=com.gpcc.gpcc" target="_blank">
            <img src="{{asset('img/silde/google-play.png')}}" alt="Google Play" style="width:100px">
          </a>

          <a href="https://apps.apple.com/us/app/gpcc/id6748722000" target="_blank" class="app-img">
            <img src="{{asset('img/silde/app-store.png')}}" alt="App Store" style="width:100px">
          </a>
        </div>
      </div>
    </div>

    <div class="gp-footer-bottom">
      © Copyrights {{ date('Y') }} All rights reserved. Powered by Green Park Coaching Centre
    </div>
  </footer>
  <script src="{{asset('js/login.js')}}"></script>
</body>

</html>