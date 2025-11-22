@volt
<?php

use function Livewire\Volt\{computed, state};

state(['locale']);

$locale = app()->getLocale();

$services = computed(fn () => \App\Models\Post::where('type', 'service')
    ->where('status', 'published')
    ->with('translations')
    ->orderBy('sort_order')
    ->get());

$settings = computed(fn () => \App\Models\Setting::with('translations')->first());

?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ __('services.title') }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li>{{ __('common.services') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content-2">
        <section class="section-hero">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <p class="text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                            {{ __('services.hero_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="section-service page-our-service tf-spacing-3">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ __('services.section_title') }}</h3>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                {{ __('services.section_description') }}
                            </p>
                        </div>
                        <div class="grid-layout-3 multi-item">
                            @foreach($this->services as $index => $service)
                                @php
                                    $translation = $service->translation($locale);
                                    $delay = ($index % 3) * 0.1;
                                @endphp
                                @if($translation)
                                    <div class="service-item style-3 hover-img wow fadeInUp" data-wow-duration="1000" data-wow-delay="{{ $delay }}s">
                                        <div class="content z-5">
                                            <h5 class="title">
                                                <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}">
                                                    {{ $translation->title }}
                                                </a>
                                            </h5>
                                            <p>{{ Str::limit($translation->excerpt ?? '', 100) }}</p>
                                        </div>
                                        @if($service->featured_image)
                                            <div class="image-wrap z-5 relative">
                                                <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}">
                                                    <img class="lazyload" data-src="{{ Storage::url($service->featured_image) }}" src="{{ Storage::url($service->featured_image) }}" alt="{{ $translation->title }}">
                                                </a>
                                            </div>
                                        @endif
                                        <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}" class="tf-btn-link z-5">
                                            <span data-text="{{ __('services.read_more') }}">{{ __('services.read_more') }}</span>
                                            <i class="icon-ArrowRight"></i>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section-contact">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content">
                            <div class="box-contact">
                                <div class="heading-section text-start">
                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                        {{ __('services.consultation_subtitle') }}
                                    </p>
                                    <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                        {{ __('services.consultation_title') }}
                                    </h3>
                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                        {{ __('services.consultation_description') }}
                                    </p>
                                </div>
                                @if($this->settings)
                                    <ul class="list-info">
                                        @if($this->settings->contact_email)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                <i class="icon-Envelope"></i>
                                                <a href="mailto:{{ $this->settings->contact_email }}">{{ $this->settings->contact_email }}</a>
                                            </li>
                                        @endif
                                        @if($this->settings->contact_phone)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                <i class="icon-PhoneCall"></i>{{ $this->settings->contact_phone }}
                                            </li>
                                        @endif
                                        @if($this->settings->contact_address)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                <i class="icon-MapPin"></i>{{ $this->settings->contact_address }}
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                                <a href="{{ route($locale === 'tr' ? 'tr.contact' : 'contact') }}" class="tf-btn-link z-5 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <span data-text="{{ __('services.contact_us') }}">{{ __('services.contact_us') }}</span>
                                    <i class="icon-ArrowRight"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endvolt
