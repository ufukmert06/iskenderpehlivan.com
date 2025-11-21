<?php

use App\Models\Setting;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Livewire\Volt\Component;

new class extends Component {
    public $settings;

    public $settingTranslation;

    public $menu;

    public $locale;

    public function mount(): void
    {
        $this->locale = app()->getLocale();
        $this->settings = Setting::first();

        if ($this->settings) {
            $this->settingTranslation = $this->settings->translations()
                ->where('locale', $this->locale)
                ->first();
        }

        $menuSlug = 'menu-'.$this->locale;
        $this->menu = Menu::with(['items' => function ($query) {
            $query->whereNull('parent_id')->orderBy('_lft');
        }, 'items.children'])->where('slug', $menuSlug)->first();
    }
}; ?>

<div>
    <div id="loading">
        <div id="loading-center">
            <div class="loader-container">
                <div class="wrap-loader">
                    <div class="loader">
                    </div>
                    <div class="icon">
                        @if($settings && $settings->favicon)
                            <img src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                        @else
                            <img src="images/logo/favicon.png" alt="{{ config('app.name') }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <header id="header-main" class="header style-1 no-boder">
        <div class="header-inner">
            <div class="header-inner-wrap w-1840">
                <div class="mobile-button" data-bs-toggle="offcanvas" data-bs-target="#menu-mobile" aria-controls="menu-mobile">
                    <i class="icon-menu"></i>
                </div>
                <div class="header-left">
                    <div class="header-logo">
                        <a href="{{ url('/') }}" class="site-logo">
                            @if($settings && $settings->logo)
                               <div class="d-flex align-items-center gap-3">
                                   <img style="height: 40px" src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                                   <img id="logo_header" style="height: 30px" alt="{{ $settingTranslation?->site_name ?? config('app.name') }}" src="{{ Storage::url($settings->logo) }}">
                               </div>
                            @else
                                <img src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                            @endif
                        </a>
                    </div>
                    <nav class="main-menu">
                        <ul class="navigation">
                            @if($menu && $menu->items)
                                @foreach($menu->items as $item)
                                    <li class="{{ $item->children->isNotEmpty() ? 'has-child relative' : '' }} {{ $item->wrapper_class }}">
                                        <a href="{{ $item->link }}" target="{{ $item->target }}" class="{{ $item->link_class }}">
                                            {{ $item->menu_name }}
                                        </a>

                                        @if($item->children->isNotEmpty())
                                            <ul class="sub-menu">
                                                @foreach($item->children as $child)
                                                    <li class="{{ $child->wrapper_class }}">
                                                        <a href="{{ $child->link }}" target="{{ $child->target }}" class="{{ $child->link_class }}">
                                                            {{ $child->menu_name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="header-right">
                    <div class="btn-get">
                        <a class="tf-btn style-default btn-color-secondary pd-40" href="book-appointment.html">
<span>
{{ __('common.get_consult') }}
</span>
                        </a>
                    </div>
                    <div class="group-btn">
                        <a class="btn-find" href="#canvasSearch" data-bs-toggle="offcanvas">
                            <div class="icon">
                                <i class="icon-MagnifyingGlass"></i>
                            </div>
                        </a>
                        <livewire:components.language-switcher />
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
