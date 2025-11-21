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

<livewire:components.mobile-menu/>


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
