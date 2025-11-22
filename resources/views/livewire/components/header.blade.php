<?php

use App\Models\Setting;
use App\Models\Post;
use Biostate\FilamentMenuBuilder\Models\Menu;
use Livewire\Volt\Component;

new class extends Component
{
    public $settings;

    public $settingTranslation;

    public $menu;

    public $locale;

    public $recentPosts;

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

        // Load recent blog posts for megamenu
        $this->recentPosts = Post::with(['translations' => function ($query) {
            $query->where('locale', $this->locale)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }])
            ->where('type', 'blog')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
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
    <div wire:ignore>
        <header id="header-main" class="header style-1 no-boder">
            <div class="header-inner">
                <div class="header-inner-wrap">
                    <div class="mobile-button" data-bs-toggle="offcanvas" data-bs-target="#menu-mobile" aria-controls="menu-mobile">
                        <i class="icon-menu"></i>
                    </div>
                    <div class="header-left">
                        <div class="header-logo">
                            <a href="{{ url('/') }}" class="site-logo">
                                @if($settings && $settings->logo)
                                    <div class="d-flex align-items-center gap-3 logo-container">
                                        <img class="logo-favicon" src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                                        <img id="logo_header" class="logo-main" alt="{{ $settingTranslation?->site_name ?? config('app.name') }}" src="{{ Storage::url($settings->logo) }}">
                                    </div>
                                @else
                                    <img class="logo-fallback" src="{{ Storage::url($settings->favicon) }}" alt="{{ config('app.name') }}">
                                @endif
                            </a>
                        </div>
                        <nav class="main-menu">
                            <ul class="navigation">
                                @if($menu && $menu->items)
                                    @foreach($menu->items as $item)
                                        @php
                                            $isMegaMenu = str_contains($item->wrapper_class ?? '', 'mega-menu');
                                            $hasChildren = $item->children->isNotEmpty();
                                            $classes = [];

                                            if ($hasChildren || $isMegaMenu) {
                                                $classes[] = 'has-child';
                                            }

                                            if ($hasChildren && !$isMegaMenu) {
                                                $classes[] = 'relative';
                                            }
                                        @endphp

                                        <li class="{{ implode(' ', $classes) }} {{ $item->wrapper_class }}">
                                            <a href="{{ $item->link }}" target="{{ $item->target }}" class="{{ $item->link_class }}">
                                                {{ $item->menu_name }}
                                            </a>

                                            @if($isMegaMenu && $item->children->isNotEmpty())
                                                {{-- Megamenu --}}
                                                <div class="sub-menu service-link">
                                                    <div class="tf-container">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="wrap-cta">
                                                                    <div class="left">
                                                                        <h5 class="wg-title">{{ __('common.services_therapy_title') }}</h5>
                                                                        <div class="wrap-service">
                                                                            @foreach($item->children as $child)
                                                                                <div class="service-item-list">
                                                                                    <h6>
                                                                                        <a href="{{ $child->link }}">
                                                                                            {{ $child->menu_name }}
                                                                                        </a>
                                                                                    </h6>
                                                                                    @if($child->parameters)
                                                                                        @php
                                                                                            $childParams = is_string($child->parameters) ? json_decode($child->parameters, true) : $child->parameters;
                                                                                        @endphp
                                                                                        @if(is_array($childParams) && isset($childParams['description']))
                                                                                            <p class="text-2">
                                                                                                {{ $childParams['description'] }}
                                                                                            </p>
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <div class="right">
                                                                        <h5 class="wg-title">{{ __('common.whats_new') }}</h5>
                                                                        <div class="wrap-list">
                                                                            @foreach($recentPosts as $post)
                                                                                @php
                                                                                    $postTranslation = $post->translation($locale);
                                                                                @endphp
                                                                                @if($postTranslation)
                                                                                    <div class="box-listings">
                                                                                        @if($post->featured_image)
                                                                                            <div class="image-wrap">
                                                                                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $postTranslation->title }}">
                                                                                            </div>
                                                                                        @endif
                                                                                        <div class="content">
                                                                                            <ul class="meta">
                                                                                                <li class="text-2">{{ $postTranslation->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</li>
                                                                                            </ul>
                                                                                            <div class="title text-1 fw-5">
                                                                                                <a class="line-clamp-2" href="{{ url('/blog/' . $postTranslation->slug) }}">
                                                                                                    {{ $postTranslation->title }}
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($item->children->isNotEmpty())
                                                {{-- Regular submenu --}}
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
                            <a class="tf-btn style-default btn-color-secondary pd-40" href="{{ route($locale === 'tr' ? 'tr.book-appointment' : 'book-appointment') }}">
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
    <style>
        /* Desktop - Logo boyutları */
        .logo-container {
            max-width: none;
        }

        .logo-favicon {
            height: 40px;
            width: 40px;
            object-fit: contain;
        }

        .logo-main {
            height: 30px;
            max-width: 200px;
            object-fit: contain;
        }

        .logo-fallback {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        /* Mobile - Küçük ekranlar için */
        @media (max-width: 991px) {
            .logo-container {
                max-width: 150px;
                gap: 0.5rem !important;
            }

            .logo-favicon {
                height: 30px;
                width: 30px;
            }

            .logo-main {
                max-height: 25px;
                max-width: 110px;
            }

            .logo-fallback {
                max-width: 150px;
                height: auto;
            }
        }
    </style>

</div>

