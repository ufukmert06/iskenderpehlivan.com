@volt
<?php

use function Livewire\Volt\{computed, mount, state};

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
                                {{ $settingTranslation?->about_welcome_title ?? 'Welcome The Healingy' }}
                            </h2>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                {{ $settingTranslation?->about_welcome_description ?? 'It is a trusted counseling and therapy center, staffed by experienced professionals dedicated to listening, supporting, and guiding you. We believe in everyone\'s potential to heal and grow with the right care.' }}
                            </p>
                        </div>
                        <div class="box-about">
                            @if($page->featured_image)
                                <div class="image-wrap mb-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <img class="lazyload" data-src="{{ Storage::url($page->featured_image) }}" src="{{ Storage::url($page->featured_image) }}" alt="{{ $translation->title }}">
                                </div>
                            @endif
                            <div class="wrap-content">
                                <div class="item wow fadeInLeft" data-wow-duration="1000" data-wow-delay="0s">
                                    <h3>{{ $settingTranslation?->about_mission_title ?? 'Our Mission' }}</h3>
                                    @if($settingTranslation?->about_mission_content)
                                        {!! nl2br(e($settingTranslation->about_mission_content)) !!}
                                    @else
                                        <p>At Healingy, our mission is to provide compassionate, personalized mental
                                            health care that helps individuals and families overcome challenges. We are
                                            committed to creating a safe, supportive space for healing and growth, using
                                            evidence-based therapy to guide clients toward mental well-being.</p>
                                        <p>Our team of experienced therapists works with each client to develop tailored
                                            strategies for resilience and self-awareness. We aim to equip individuals
                                            with the tools they need to build healthier relationships, manage stress,
                                            and create lasting change.</p>
                                    @endif
                                </div>
                                <div class="item wow fadeInRight" data-wow-duration="1000" data-wow-delay="0s">
                                    <h3>{{ $settingTranslation?->about_vision_title ?? 'Our Vision' }}</h3>
                                    @if($settingTranslation?->about_vision_content)
                                        {!! nl2br(e($settingTranslation->about_vision_content)) !!}
                                    @else
                                        <p>Our vision is to be a leading center for mental health, where everyone has
                                            access to the care and support they need. We strive to create a world where
                                            mental health is prioritized, free from stigma, and where people feel
                                            empowered to seek help.</p>
                                        <p>We aim to build a community where seeking therapy is encouraged and
                                            supported. By promoting mental health awareness and providing high-quality
                                            care, we hope to make a lasting positive impact on the lives of our clients.
                                        </p>
                                    @endif
                                </div>
                            </div>


                            @if($translation->content)
                                <div class="tinymce-content mb-5">
                                    {!! $translation->content !!}
                                </div>
                            @endif


                            <div class="wrap-counter">
                                <div class="counter-item has-icon">
                                    <div class="icon">
                                        <i class="icon-SketchLogo"></i>
                                    </div>
                                    <div class="count">
                                        <div class="counter-number">
                                            <div class="odometer style-1-1">{{ $settings?->years_of_experience ?? 0 }}
                                            </div>
                                            <span class="sub">Years</span>
                                        </div>
                                        <p>{{ $settingTranslation?->counter_years_label ?? 'Years of Experience' }}</p>
                                    </div>
                                </div>
                                <div class="counter-item has-icon">
                                    <div class="icon">
                                        <i class="icon-Smiley"></i>
                                    </div>
                                    <div class="count">
                                        <div class="counter-number">
                                            <div class="odometer style-1-2">{{ $settings?->happy_customers ?? 0 }}
                                            </div>
                                            <span class="sub">k</span>
                                        </div>
                                        <p>{{ $settingTranslation?->counter_customers_label ?? 'Happy customers' }}</p>
                                    </div>
                                </div>
                                <div class="counter-item has-icon">
                                    <div class="icon">
                                        <i class="icon-HandHeart"></i>
                                    </div>
                                    <div class="count">
                                        <div class="counter-number">
                                            <div class="odometer style-1-3">{{ $settings?->therapy_sessions ?? 10 }}
                                            </div>
                                        </div>
                                        <p>{{ $settingTranslation?->counter_sessions_label ?? 'Therapy Sessions' }}</p>
                                    </div>
                                </div>
                                <div class="counter-item has-icon">
                                    <div class="icon">
                                        <i class="icon-Certificate"></i>
                                    </div>
                                    <div class="count">
                                        <div class="counter-number">
                                            <div class="odometer style-1-4">{{ $settings?->certifications_awards ?? 0 }}
                                            </div>
                                        </div>
                                        <p>{{ $settingTranslation?->counter_certifications_label ?? 'Certifications/Awards' }}</p>
                                    </div>
                                </div>
                            </div>
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
