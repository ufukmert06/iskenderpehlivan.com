<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/odometer.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/swiper-bundle.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/icons/icomoon/style.css"/>
    <link rel="shortcut icon" href="/assets/images/logo/favicon.png"/>
    <link rel="apple-touch-icon-precomposed" href="/assets/images/logo/favicon.png"/>
    <title>{{ $title ?? 'Page Title' }}</title>
</head>
<body>
<div id="wrapper">
    <livewire:components.header/>
    {{ $slot }}
    <livewire:components.footer/>
</div>

<!-- .mobile-nav -->
<div class="offcanvas offcanvas-start mobile-nav-wrap " tabindex="-1" id="menu-mobile"
     aria-labelledby="menu-mobile">
    <div class="offcanvas-header top-nav-mobile">
        <div class="offcanvas-title">
            <a href="index.html"><img src="images/logo/logo@2x.png" alt=""></a>
        </div>
        <div data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="icon-close"></i>
        </div>
    </div>
    <div class="offcanvas-body inner-mobile-nav">
        <div class="mb-body">
            <ul id="menu-mobile-menu">
                <li class="menu-item menu-item-has-children-mobile current-menu-item">
                    <a href="#dropdown-menu-one" class="item-menu-mobile collapsed" data-bs-toggle="collapse"
                       aria-expanded="true" aria-controls="dropdown-menu-one">
                        Home
                    </a>
                    <div id="dropdown-menu-one" class="collapse" data-bs-parent="#menu-mobile-menu">
                        <ul class="sub-mobile ">
                            <li class="menu-item"><a href="index.html">Homepage 01</a></li>
                            <li class="menu-item current-item "><a href="home-02.html">Homepage 02</a></li>
                            <li class="menu-item"><a href="home-03.html">Homepage 03</a></li>
                            <li class="menu-item"><a href="home-04.html">Homepage 04</a></li>
                            <li class="menu-item"><a href="home-silde-text-scroll.html">Home silde text scroll</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="menu-item ">
                    <a href="about.html" class="item-menu-mobile "> About</a>
                </li>
                <li class="menu-item menu-item-has-children-mobile">
                    <a href="#dropdown-menu-two" class="item-menu-mobile collapsed" data-bs-toggle="collapse"
                       aria-expanded="true" aria-controls="dropdown-menu-two">
                        Services
                    </a>
                    <div id="dropdown-menu-two" class="collapse" data-bs-parent="#menu-mobile-menu">
                        <ul class="sub-mobile">
                            <li class="menu-item">
                                <a href="our-service.html">Our Service</a>
                            </li>
                            <li class="menu-item">
                                <a href="service-details.html">Service Details</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="menu-item ">
                    <a href="our-therapists.html" class="item-menu-mobile "> Therapists</a>
                </li>
                <li class="menu-item menu-item-has-children-mobile">
                    <a href="#dropdown-menu-three" class="item-menu-mobile collapsed" data-bs-toggle="collapse"
                       aria-expanded="true" aria-controls="dropdown-menu-three">
                        Pages
                    </a>
                    <div id="dropdown-menu-three" class="collapse" data-bs-parent="#menu-mobile-menu">
                        <ul class="sub-mobile">
                            <li class="menu-item menu-item-has-children-mobile-2">
                                <a href="#sub-product-one" class="item-menu-mobile  collapsed"
                                   data-bs-toggle="collapse" aria-expanded="true"
                                   aria-controls="sub-product-one">Shop</a>
                                <div id="sub-product-one" class="collapse">
                                    <ul class="sub-mobile">
                                        <li class="menu-item ">
                                            <a href="our-product.html" class="item-menu-mobile "> Shop
                                                Product</a>
                                        </li>
                                        <li class="menu-item ">
                                            <a href="shop-cart.html" class="item-menu-mobile "> Shop Cart</a>
                                        </li>
                                        <li class="menu-item ">
                                            <a href="shop-check-out.html" class="item-menu-mobile "> Check
                                                Out</a>
                                        </li>
                                        <li class="menu-item ">
                                            <a href="product-details.html" class="item-menu-mobile "> Shop
                                                Details</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item"><a href="book-appointment.html">Appointment</a></li>
                        </ul>
                    </div>
                </li>
                <li class="menu-item menu-item-has-children-mobile">
                    <a href="#dropdown-menu-four" class="item-menu-mobile collapsed" data-bs-toggle="collapse"
                       aria-expanded="true" aria-controls="dropdown-menu-four">
                        Blogs
                    </a>
                    <div id="dropdown-menu-four" class="collapse" data-bs-parent="#menu-mobile-menu">
                        <ul class="sub-mobile ">
                            <li class="menu-item"><a href="blog-grid.html">Blog Grid</a></li>
                            <li class="menu-item"><a href="blog-details.html">Blog Details 1</a></li>
                            <li class="menu-item"><a href="blog-details-2.html">Blog Details 2</a></li>
                        </ul>
                    </div>
                </li>
                <li class="menu-item ">
                    <a href="contact-us.html" class="tem-menu-mobile "> Contact</a>
                </li>
            </ul>
            <div class="support">
                <a href="#" class="text-need"> Need help?</a>
                <ul class="mb-info">
                    <li>Call Us Now: <span class="number">1-555-678-8888</span></li>
                    <li>Support 24/7: <a href="#">themesflat@gmail.com</a></li>
                </ul>
            </div>
        </div>
    </div>
</div><!-- /.mobile-nav -->


<!-- .open-search -->
<div class="offcanvas offcanvas-top offcanvas-search" id="canvasSearch">
    <button class="btn-close-search" type="button" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="icon-close"></i>
    </button>
    <div class="tf-container">
        <div class="row">
            <div class="col-12">
                <div class="offcanvas-body">
                    <form action="#" class="form-search-courses">
                        <div class="icon">
                            <i class="icon-keyboard"></i>
                        </div>
                        <fieldset>
                            <input class="" type="text" placeholder="Search for anything" name="text" tabindex="2"
                                   value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit">
                                <i class="icon-MagnifyingGlass fs-20"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.open-search -->


<!-- .prograss -->
<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
              style="transition: stroke-dashoffset 10ms linear; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
        </path>
    </svg>
</div><!-- /.prograss -->

<script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/lazysize.min.js"></script>
<script type="text/javascript" src="/assets/js/wow.min.js"></script>
<script type="text/javascript" src="/assets/js/odometer.min.js"></script>
<script type="text/javascript" src="/assets/js/counter.js"></script>
<script type="text/javascript" src="/assets/js/jquery-validate.js"></script>
<script type="text/javascript" src="/assets/js/textanimation.js"></script>
<script type="text/javascript" src="/assets/js/main.js"></script>
</body>
</html>
