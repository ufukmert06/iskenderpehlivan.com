@volt
<?php

use function Livewire\Volt\{computed, mount, state, title};

state(['slug', 'service', 'translation', 'locale']);

mount(function () {
    $this->locale = app()->getLocale();

    // Find service by slug_base or translation slug
    $this->service = \App\Models\Post::with('translations')
        ->where('type', 'service')
        ->where('status', 'published')
        ->where(function ($query) {
            $query->where('slug_base', $this->slug)
                ->orWhereHas('translations', function ($q) {
                    $q->where('slug', $this->slug);
                });
        })
        ->firstOrFail();

    $this->translation = $this->service->translation($this->locale);

    if (! $this->translation) {
        abort(404, 'Service translation not found');
    }
});

title(fn () => ($this->translation?->title ?? __('common.services')) . ' - ' . config('app.name'));

$settings = computed(fn () => \App\Models\Setting::first());

$otherServices = computed(fn () => \App\Models\Post::where('type', 'service')
    ->where('status', 'published')
    ->where('id', '!=', $this->service->id)
    ->orderBy('sort_order')
    ->limit(6)
    ->get());


?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ $translation->title }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li><a href="{{ route($locale === 'tr' ? 'tr.services' : 'services') }}">{{ __('common.services') }}</a></li>
                        <li>{{ $translation->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content-2">
        <section class="section-service-details">
            <div class="tf-container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="content-inner">
                            @if($service->featured_image)
                                <div class="image-wrap">
                                    <img class="lazyload" data-src="{{ Storage::url($service->featured_image) }}" src="{{ Storage::url($service->featured_image) }}" alt="{{ $translation->title }}">
                                </div>
                            @endif
                            <div class="heading">
                                <h4 class="mb-16">{{ __('service.about_title', ['service' => $translation->title]) }}</h4>
                                @if($translation->excerpt)
                                    <p class="text-1 lh-30">{{ $translation->excerpt }}</p>
                                @endif
                            </div>
                            @if($translation->content)
                                <div class="tinymce-content mb-30">
                                    {!! $translation->content !!}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-siderbar sticky">
                            <div class="booking-cta-card booking-cta-card--sidebar">
                                <div class="booking-cta-icon">
                                    <i class="icon-CalendarBlank"></i>
                                </div>
                                <h5 class="mb-12">{{ __('service.consultation_title') }}</h5>
                                <p class="booking-cta-description">{{ __('service.booking_description') }}</p>
                                <a href="https://iskenderpehlivan.janeapp.com/#staff_member/1" target="_blank" rel="noopener noreferrer" class="tf-btn style-default btn-color-secondary pd-40 boder-8 booking-cta-btn">
                                    <span><i class="icon-CalendarBlank"></i> {{ __('service.booking_button') }}</span>
                                </a>
                                <p class="booking-cta-badge"><i class="icon-CheckCircle"></i> {{ __('service.booking_badge') }}</p>

                                @if($this->settings)
                                    <div class="info">
                                        <h5>{{ __('service.information_title') }}</h5>
                                        <ul class="list-info">
                                            @if($this->settings->contact_email)
                                                <li><i class="icon-Envelope"></i> <a href="mailto:{{ $this->settings->contact_email }}">{{ $this->settings->contact_email }}</a></li>
                                            @endif
                                            @if($this->settings->contact_phone)
                                                <li><i class="icon-PhoneCall"></i>{{ $this->settings->contact_phone }}</li>
                                            @endif
                                            @if($this->settings->contact_address)
                                                <li><i class="icon-MapPin"></i>{{ $this->settings->contact_address }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="list-service">
                                <h5>{{ __('service.more_services') }}</h5>
                                <ul>
                                    @foreach($this->otherServices as $otherService)
                                        @php
                                            $otherTranslation = $otherService->translation($locale);
                                        @endphp
                                        @if($otherTranslation)
                                            <li>
                                                <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $otherService->slug_base) }}">
                                                    {{ $otherTranslation->title }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <style>
        /* TinyMCE Content Styling - Preserve editor formatting */
        .tinymce-content {
            line-height: 1.8;
            color: #333;
        }

        .tinymce-content p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .tinymce-content ul,
        .tinymce-content ol {
            margin: 1rem 0;
            padding-left: 2rem;
            line-height: 1.8;
        }

        .tinymce-content ul {
            list-style-type: disc;
        }

        .tinymce-content ol {
            list-style-type: decimal;
        }

        .tinymce-content li {
            margin-bottom: 0.5rem;
            list-style: disc;
        }

        .tinymce-content ul ul,
        .tinymce-content ol ul {
            list-style-type: circle;
        }

        .tinymce-content ol ol,
        .tinymce-content ul ol {
            list-style-type: lower-alpha;
        }

        .tinymce-content h1,
        .tinymce-content h2,
        .tinymce-content h3,
        .tinymce-content h4,
        .tinymce-content h5,
        .tinymce-content h6 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .tinymce-content h1 { font-size: 2rem; }
        .tinymce-content h2 { font-size: 1.75rem; }
        .tinymce-content h3 { font-size: 1.5rem; }
        .tinymce-content h4 { font-size: 1.25rem; }
        .tinymce-content h5 { font-size: 1.1rem; }
        .tinymce-content h6 { font-size: 1rem; }

        .tinymce-content strong,
        .tinymce-content b {
            font-weight: 700;
        }

        .tinymce-content em,
        .tinymce-content i {
            font-style: italic;
        }

        .tinymce-content a {
            color: #667eea;
            text-decoration: underline;
        }

        .tinymce-content a:hover {
            color: #5568d3;
        }

        .tinymce-content blockquote {
            margin: 1.5rem 0;
            padding: 1rem 1.5rem;
            border-left: 4px solid #667eea;
            background-color: #f8f9fa;
            font-style: italic;
        }

        .tinymce-content code {
            padding: 0.2rem 0.4rem;
            background-color: #f8f9fa;
            border-radius: 3px;
            font-family: monospace;
            font-size: 0.9em;
        }

        .tinymce-content pre {
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            overflow-x: auto;
        }

        .tinymce-content pre code {
            padding: 0;
            background-color: transparent;
        }

        .tinymce-content table {
            width: 100%;
            margin: 1.5rem 0;
            border-collapse: collapse;
        }

        .tinymce-content table th,
        .tinymce-content table td {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
        }

        .tinymce-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .tinymce-content img {
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
            border-radius: 8px;
        }

        .tinymce-content hr {
            margin: 2rem 0;
            border: 0;
            border-top: 2px solid #e9ecef;
        }
    </style>

</div>
@endvolt
