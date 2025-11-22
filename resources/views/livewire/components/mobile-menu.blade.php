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

$menuSlug = 'menu-'.$locale;
$menu = \Biostate\FilamentMenuBuilder\Models\Menu::with(['items' => function ($query) {
    $query->whereNull('parent_id')->orderBy('_lft');
}, 'items.children'])->where('slug', $menuSlug)->first();

?>

<div>
    <!-- Mobile Navigation -->
    <div class="offcanvas offcanvas-start mobile-nav-wrap" tabindex="-1" id="menu-mobile" aria-labelledby="menu-mobile">
        <div class="offcanvas-header top-nav-mobile">
            <div class="offcanvas-title">
                <a href="{{ route('home') }}">
                    @if($settings && $settings->logo)
                        <div class="d-flex align-items-center gap-2">
                            <img class="mobile-menu-favicon" src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                            <img class="mobile-menu-logo" src="{{ Storage::url($settings->logo) }}" alt="{{ $settingTranslation?->site_name ?? config('app.name') }}">
                        </div>
                    @else
                        <img class="mobile-menu-logo-fallback" src="/images/logo/logo@2x.png" alt="{{ config('app.name') }}">
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
                <div class="support">
                    <a href="#" class="text-need">{{ __('common.need_help') }}</a>
                    <ul class="mb-info">
                        @if($settings?->contact_phone)
                            <li>{{ __('common.call_us_now') }}: <span class="number">{{ $settings->contact_phone }}</span></li>
                        @endif
                        @if($settings?->contact_email)
                            <li>{{ __('common.support_247') }}: <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></li>
                        @endif
                    </ul>
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
                        <form action="{{ route($locale === 'tr' ? 'tr.search' : 'search') }}" method="GET" class="form-search-courses">
                            <div class="icon">
                                <i class="icon-keyboard"></i>
                            </div>
                            <fieldset>
                                <input class="" type="text" placeholder="{{ __('common.search_placeholder') }}" name="q" tabindex="2" value="{{ request('q') }}" aria-required="true" required="">
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
        /* Mobile menu logo boyutları */
        .mobile-menu-favicon {
            height: 30px;
            width: 30px;
            object-fit: contain;
        }

        .mobile-menu-logo {
            max-height: 28px;
            max-width: 120px;
            object-fit: contain;
        }

        .mobile-menu-logo-fallback {
            max-height: 30px;
            max-width: 150px;
            object-fit: contain;
        }

        /* Çok küçük ekranlar için (< 360px) */
        @media (max-width: 360px) {
            .mobile-menu-favicon {
                height: 25px;
                width: 25px;
            }

            .mobile-menu-logo {
                max-height: 22px;
                max-width: 100px;
            }

            .mobile-menu-logo-fallback {
                max-height: 25px;
                max-width: 120px;
            }
        }
    </style>

</div>


@endvolt
