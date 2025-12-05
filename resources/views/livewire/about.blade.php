@volt
<?php

use function Livewire\Volt\{computed, mount, state, title};

state(['page', 'translation', 'locale', 'settings', 'settingTranslation']);

mount(function () {
    $this->locale = app()->getLocale();

    // Find the About page by slug_base
    $this->page = \App\Models\Post::with('translations')
        ->where('type', 'page')
        ->where('slug_base', 'about')
        ->where('status', 'published')
        ->firstOrFail();

    $this->translation = $this->page->translation($this->locale);

    if (!$this->translation) {
        abort(404, 'Page translation not found');
    }

    // Get settings with translations
    $this->settings = \App\Models\Setting::with('translations')->first();
    $this->settingTranslation = $this->settings?->translation($this->locale);
});

title(fn () => ($this->translation?->title ?? __('common.about')) . ' - ' . config('app.name'));

?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ $translation->title }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li>{{ $translation->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content-2">

        <div class="section-box-about page-about">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <h2 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                Welcome to IST Counselling Inc.
                            </h2>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                IST Counselling Inc. is a trusted counselling and therapy center, staffed by experienced
                                professionals dedicated to listening, supporting, and guiding you. We believe in
                                everyone’s potential to grow with the right care and support.
                            </p>
                        </div>
                        <div class="box-about">



                            @if($translation->content)
                                <div class="tinymce-content mb-5">
                                    {!! $translation->content !!}
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* TinyMCE Content Styling */
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

        .tinymce-content h1 {
            font-size: 2rem;
        }

        .tinymce-content h2 {
            font-size: 1.75rem;
        }

        .tinymce-content h3 {
            font-size: 1.5rem;
        }

        .tinymce-content h4 {
            font-size: 1.25rem;
        }

        .tinymce-content h5 {
            font-size: 1.1rem;
        }

        .tinymce-content h6 {
            font-size: 1rem;
        }

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

        .tinymce-content img {
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
            border-radius: 8px;
        }
    </style>
</div>
@endvolt
