<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/odometer.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/swiper-bundle.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/icons/icomoon/style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="/assets/images/logo/favicon.png"/>
    <link rel="apple-touch-icon-precomposed" href="/assets/images/logo/favicon.png"/>
    <title>{{ $title ?? 'Page Title' }}</title>
    <style>
        @media (min-width: 640px) and (max-width: 1800px) {
            body {
                zoom: 0.7;
                -webkit-text-size-adjust: 100%;
            }

            /* Safari fallback */
            @supports not (zoom: 0.7) {
                body {
                    transform: scale(0.7);
                    transform-origin: top left;
                    width: 142.857%;
                }
            }
        }
    </style>
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

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
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
