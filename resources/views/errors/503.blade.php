<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/icons/icomoon/style.css"/>
    <link rel="shortcut icon" href="/assets/images/logo/favicon.png"/>
    <title>{{ __('errors.503.title') }}</title>
</head>
<body>
<div id="wrapper">
    <livewire:components.header/>

    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">503</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route(app()->getLocale() === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li>{{ __('errors.503.heading') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content-2">
        <section class="section-box-about">
            <div class="tf-container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="heading-section text-center">
                            <h2 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                {{ __('errors.503.heading') }}
                            </h2>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="200ms">
                                {{ __('errors.503.message') }}
                            </p>
                        </div>

                        <div class="box-about wow fadeInUp" data-wow-duration="1000" data-wow-delay="400ms">
                            <div class="wrap-content">
                                <div class="item">
                                    <h3>{{ __('errors.503.what_to_do') }}</h3>
                                    <p>• {{ __('errors.503.tip_1') }}</p>
                                    <p>• {{ __('errors.503.tip_2') }}</p>
                                    <p>• {{ __('errors.503.tip_3') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-40 wow fadeInUp flex-wrap" data-wow-duration="1000" data-wow-delay="600ms">
                            <a href="javascript:window.location.reload()" class="tf-btn style-default btn-color-secondary pd-40">
                                <span>{{ __('errors.refresh_page') }}</span>
                            </a>
                            <a href="{{ route(app()->getLocale() === 'tr' ? 'tr.contact' : 'contact') }}" class="tf-btn style-default btn-color-secondary pd-40">
                                <span>{{ __('errors.contact_us') }}</span>
                            </a>
                        </div>

                        <div class="text-center mt-40 wow fadeInUp" data-wow-duration="1000" data-wow-delay="800ms">
                            <p class="text-2 mb-10">{{ __('errors.503.thanks') }}</p>
                            <p class="text-1 fw-5">{{ __('errors.503.back_soon') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <livewire:components.footer/>
</div>

<script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/wow.min.js"></script>
<script type="text/javascript" src="/assets/js/main.js"></script>
</body>
</html>
