@volt
<?php

use function Livewire\Volt\{title};

title(__('common.home') . ' - ' . config('app.name'));

// Fetch data directly since it's static, non-reactive data
$blogPosts = \App\Models\Post::where('type', 'blog')
    ->where('status', 'published')
    ->with(['translations', 'categories.translations'])
    ->latest()
    ->limit(3)
    ->get();

$services = \App\Models\Post::where('type', 'service')
    ->where('status', 'published')
    ->with('translations')
    ->orderBy('sort_order')
    ->get();

$settings = \App\Models\Setting::with('translations')->first();
?>

<div>
    <!-- Hero Slider Component -->
    <livewire:components.hero-slider/>

    <div class="main-content home-page-2">
        <!-- About Section -->
        <div class="section-box-about page-home-2">
            <div class="tf-container">
                <div class="wrap-box-about">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="image-wrap wow fadeInLeft effec-overlay" data-wow-duration="1000" data-wow-delay="0s">
                                <img class="lazyload" data-src="{{asset('images/ekip.jpeg')}}" src="{{asset('images/ekip.jpeg')}}" alt="{{ __('home.image_alt.about_us') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box-about">
                                <div class="heading-section text-start wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.about.subtitle') }}</p>
                                    <h3>{{ __('home.about.title') }}</h3>
                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.about.description') }}
                                    </p>
                                </div>
                                <a class="tf-btn style-default btn-color-white has-boder pd-26 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s" href="#about">
                                    <span>{{ __('home.about.cta_button') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="section-benefits page-home-2 tf-spacing-1 mb-0 pb-0">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section gap-0 m-0 p-0" style="margin-bottom: 0!important;">
                            <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.benefits.subtitle') }}</p>
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.benefits.title') }}</h3>
                            <p class="description text-1 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                {!!  __('home.benefits.description') !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <section class="section-service page-home-2 tf-spacing-1">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.services.subtitle') }}</p>
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.services.title') }}</h3>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.services.description') }}
                            </p>
                        </div>
                        <div class="widget-tabs">
                            <ul class="widget-menu-tab overflow-x-auto">
                                @foreach($services as $index => $service)
                                    <li class="item-title @if($index === 0) active @endif" wire:key="service-tab-{{ $service->id }}">
                                        {{ $service->translation()?->title ?? __('home.services.default_service_name') }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="widget-content-tab">
                                @foreach($services as $index => $service)
                                    <div class="widget-content-inner @if($index === 0) active @endif" wire:key="service-content-{{ $service->id }}">
                                        <div class="box-service">
                                            <div class="image-wrap @if($index === 0) wow @endif fadeInLeft effec-overlay" data-wow-duration="1000" data-wow-delay="0s">
                                                @if($service->featured_image)
                                                    <img class="lazyload" data-src="{{ Storage::url($service->featured_image) }}" src="{{ Storage::url($service->featured_image) }}" alt="{{ $service->translation()?->title ?? __('home.services.default_service_name') }}">
                                                @else
                                                    <img class="lazyload" data-src="/assets/images/section/section-service.jpg" src="/assets/images/section/section-service.jpg" alt="{{ $service->translation()?->title ?? __('home.services.default_service_name') }}">
                                                @endif
                                            </div>
                                            <div class="content">
                                                <div class="heading-section text-start">
                                                    <p class="text-2 sub @if($index === 0) wow @endif fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ $service->translation()?->title ?? __('home.services.default_service_name') }}</p>
                                                    <h4 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                        <a href="{{ route(app()->getLocale() === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}">
                                                            {{ $service->translation()?->title ?? __('home.services.default_service_name') }}
                                                        </a>
                                                    </h4>
                                                    <p class="description text-1 lh-30 @if($index === 0) wow @endif fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                        {{ Str::limit($service->translation()?->excerpt ?? '', 300) }}
                                                    </p>
                                                </div>
                                                <a href="{{ route(app()->getLocale() === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}" class="tf-btn-link z-5 @if($index === 0) wow @endif fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                    <span data-text="{{ __('home.services.read_more') }}">{{ __('home.services.read_more') }}</span>
                                                    <i class="icon-ArrowRight"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <div wire:ignore>
            <section class="section-news tf-spacing-1 style-pagination">
                <div class="tf-container">
                    <div class="row">
                        <div class="col-12">
                            <div class="heading-section">
                                <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.blog.subtitle') }}</p>
                                <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.blog.title') }}</h3>
                                <p class="description text-1 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    {{ __('home.blog.description') }}
                                </p>
                            </div>
                            <div class="swiper-container slider-layout-3">
                                <div class="swiper-wrapper">
                                    @foreach($blogPosts as $index => $post)
                                        @php
                                            $translation = $post->translation();
                                            $publishedAt = $translation?->published_at ? \Carbon\Carbon::parse($translation->published_at) : $post->created_at;
                                            $category = $post->categories->first();
                                            $delay = $index * 0.1;
                                        @endphp
                                        <div class="swiper-slide" wire:key="blog-post-{{ $post->id }}">
                                            <div class="article-blog-item hover-img wow fadeInUp" data-wow-duration="1000" data-wow-delay="{{ $delay }}s">
                                                <div class="image-wrap">
                                                    <a href="#blog-{{ $post->slug_base }}">
                                                        @if($post->featured_image)
                                                            <img class="lazyload" data-src="{{ Storage::url($post->featured_image) }}" src="{{ Storage::url($post->featured_image) }}" alt="{{ $translation?->title ?? __('home.blog.default_post_title') }}">
                                                        @else
                                                            <img class="lazyload" data-src="/assets/images/section/resources-2-1.jpg" src="/assets/images/section/resources-2-1.jpg" alt="{{ $translation?->title ?? __('home.blog.default_post_title') }}">
                                                        @endif
                                                    </a>
                                                    <div class="date-time">
                                                        <div class="content">
                                                            <p class="entry-day">{{ $publishedAt->day }}</p>
                                                            <p class="entry-month fw-book">{{ strtoupper($publishedAt->format('M')) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="content">
                                                    @if($category)
                                                        <p class="sub"><a href="#category-{{ $category->slug_base }}">{{ $category->translation()?->name ?? __('home.blog.blog_label') }}</a></p>
                                                    @else
                                                        <p class="sub"><a href="#">{{ __('home.blog.blog_label') }}</a></p>
                                                    @endif
                                                    <h5 class="title"><a href="#blog-{{ $post->slug_base }}">
                                                            {{ $translation?->title ?? __('home.blog.default_post_title') }}
                                                        </a></h5>
                                                    @if($translation?->excerpt)
                                                        <p>{{ Str::limit($translation->excerpt, 120) }}</p>
                                                    @endif
                                                </div>
                                                <a href="#blog-{{ $post->slug_base }}" class="tf-btn-link">
                                                    <span data-text="{{ __('home.blog.read_more') }}">{{ __('home.blog.read_more') }}</span>
                                                    <i class="icon-ArrowRight"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="swiper-pagination pagination-layout"></div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <!-- Contact Section -->
        <section class="section-contact home-page-2 bg-1 tf-spacing-1" id="contact-section">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content">
                            <div class="box-contact">
                                <div class="heading-section text-start">
                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                        {{ __('home.contact.subtitle') }}</p>
                                    <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.contact.title') }}</h3>
                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('home.contact.description') }}
                                    </p>
                                </div>
                                <ul class="list-info">
                                    @if($settings)
                                        @if($settings->contact_email)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-Envelope"></i> <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></li>
                                        @endif
                                        @if($settings->contact_phone)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-PhoneCall"></i>{{ $settings->contact_phone }}</li>
                                        @endif
                                        @if($settings->contact_address)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-MapPin"></i>{{ $settings->contact_address }}</li>
                                        @endif
                                    @endif
                                </ul>
                                <a href="https://iskenderpehlivan.janeapp.com/#staff_member/1" class="tf-btn-link z-5 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <span data-text="{{ __('home.contact.cta_button') }}">{{ __('home.contact.cta_button') }}</span>
                                    <i class="icon-ArrowRight"></i>
                                </a>
                            </div>
                            <form class="form-consultation wow fadeInRight" data-wow-duration="1000" data-wow-delay="0s" method="post" id="contactform" action="{{ route('api.contact.store') }}">
                                @csrf
                                <h4 class="mb-20 text-center">{{ __('home.contact.form_title') }}</h4>
                                <fieldset class="name">
                                    <input type="text" name="name" class="tf-input style-1" placeholder="{{ __('home.contact.form_name_placeholder') }}" tabindex="2" aria-required="true" required>
                                </fieldset>
                                <fieldset class="phone">
                                    <input type="email" name="email" class="tf-input style-1" placeholder="{{ __('home.contact.form_email_placeholder') }}" tabindex="2" aria-required="true" required>
                                </fieldset>
                                <fieldset class="message">
                                    <textarea id="message" class="tf-input" name="message" rows="4" placeholder="{{ __('home.contact.form_message_placeholder') }}" tabindex="4" aria-required="true" required></textarea>
                                </fieldset>
                                <button class="tf-btn style-default btn-color-secondary pd-40 boder-8 send-wrap" type="submit">
                                    <span>{{ __('home.contact.form_submit_button') }}</span>
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endvolt
