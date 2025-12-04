@volt
<?php

use function Livewire\Volt\state;

state(['settings', 'settingTranslation', 'menu', 'locale']);

$locale = app()->getLocale();
$settings = \App\Models\Setting::first();

if ($settings) {
    $settingTranslation = $settings->translations()
        ->where('locale', $locale)
        ->first();
}

$menuSlug = 'menu-' . $locale;
$menu = \Biostate\FilamentMenuBuilder\Models\Menu::with([
    'items' => function ($query) {
        $query->whereNull('parent_id')->orderBy('_lft');
    },
    'items.children'
])->where('slug', $menuSlug)->first();

?>

<div>
    <!-- Mobile Navigation -->
    <div class="offcanvas offcanvas-start mobile-nav-wrap" tabindex="-1" id="menu-mobile" aria-labelledby="menu-mobile">
        <div class="offcanvas-header top-nav-mobile">
            <div class="offcanvas-title">
                <a href="{{ route('home') }}" class="mobile-logo-link">
                    @if($settings && $settings->logo)
                        <img class="mobile-menu-logo" src="{{ Storage::url($settings->logo) }}"
                            alt="{{ $settingTranslation?->site_name ?? config('app.name') }}">
                    @else
                        <img class="mobile-menu-logo-fallback" src="/images/logo/logo@2x.png"
                            alt="{{ config('app.name') }}">
                    @endif
                </a>
            </div>
            <div data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="icon-close"></i>
            </div>
        </div>
        <div class="offcanvas-body inner-mobile-nav">
            <div class="mb-body">
                <ul id="menu-mobile-menu">
                    @if($menu && $menu->items)
                        @foreach($menu->items as $index => $item)
                            @if($item->children->isNotEmpty())
                                <li class="menu-item menu-item-has-children-mobile {{ $item->wrapper_class }}">
                                    <a href="#dropdown-menu-{{ $index }}" class="item-menu-mobile collapsed"
                                        data-bs-toggle="collapse" aria-expanded="false" aria-controls="dropdown-menu-{{ $index }}">
                                        {{ $item->menu_name }}
                                    </a>
                                    <div id="dropdown-menu-{{ $index }}" class="collapse" data-bs-parent="#menu-mobile-menu">
                                        <ul class="sub-mobile">
                                            @foreach($item->children as $child)
                                                <li class="menu-item {{ $child->wrapper_class }}">
                                                    <a href="{{ $child->link }}" target="{{ $child->target }}">
                                                        {{ $child->menu_name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @else
                                <li class="menu-item {{ $item->wrapper_class }}">
                                    <a href="{{ $item->link }}" class="item-menu-mobile" target="{{ $item->target }}">
                                        {{ $item->menu_name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
                <div class="mobile-quote-card">
                    <p class="quote-text">
                        "Compassionate, evidence-based care for ADHD, OCD, anxiety, emotional dysregulation, and
                        childhood behavioural challenges."
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- /.mobile-nav -->

    <!-- Search Offcanvas -->
    <div class="offcanvas offcanvas-top offcanvas-search" id="canvasSearch">
        <button class="btn-close-search" type="button" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="icon-close"></i>
        </button>
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <div class="offcanvas-body">
                        <form action="{{ route($locale === 'tr' ? 'tr.search' : 'search') }}" method="GET"
                            class="form-search-courses">
                            <div class="icon">
                                <i class="icon-keyboard"></i>
                            </div>
                            <fieldset>
                                <input class="" type="text" placeholder="{{ __('common.search_placeholder') }}" name="q"
                                    tabindex="2" value="{{ request('q') }}" aria-required="true" required="">
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
    </div>
    <!-- /.open-search -->
    <style>
        /* Mobile menu arka plan rengi */
        .mobile-nav-wrap {
            background-color: #3C5250 !important;
        }

        .mobile-nav-wrap .offcanvas-header,
        .mobile-nav-wrap .offcanvas-body {
            background-color: #3C5250 !important;
        }

        /* Menü öğeleri beyaz renk */
        .mobile-nav-wrap .item-menu-mobile,
        .mobile-nav-wrap .menu-item a,
        .mobile-nav-wrap .sub-mobile a,
        .mobile-nav-wrap .support,
        .mobile-nav-wrap .text-need,
        .mobile-nav-wrap .mb-info,
        .mobile-nav-wrap .mb-info a,
        .mobile-nav-wrap .mb-info li,
        .mobile-nav-wrap .number {
            color: #fff !important;
        }

        /* Close ikonu beyaz */
        .mobile-nav-wrap .icon-close {
            color: #fff !important;
        }

        /* Menü ayraç çizgileri */
        .mobile-nav-wrap .menu-item {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* Mobile menu logo boyutları */
        .offcanvas-header.top-nav-mobile {
            padding: 15px !important;
        }

        .offcanvas-title {
            flex: 1 !important;
            max-width: calc(100% - 40px) !important;
        }

        .mobile-logo-link {
            display: block;
            width: 100%;
        }

        .mobile-menu-logo {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain;
        }

        .mobile-menu-logo-fallback {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain;
        }

        /* Quote Card Styles */
        .mobile-quote-card {
            background-color: #f5f3f0;
            padding: 24px 20px;
            margin-top: 30px;
            border-radius: 8px;
        }

        .mobile-quote-card .quote-text {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 18px;
            line-height: 1.6;
            color: #1a1a1a;
            text-align: center;
            margin: 0;
            font-weight: 400;
        }
    </style>

</div>


@endvolt