@volt
<?php

use function Livewire\Volt\state;
use function Livewire\Volt\title;

state(['settings', 'settingTranslation']);

$settings = \App\Models\Setting::first();

if ($settings) {
    $settingTranslation = $settings->translations()
        ->where('locale', app()->getLocale())
        ->first();
}

title(__('contact.title').' - '.config('app.name'));

?>

<div>
    <div class="main-content page-contact">
        <div class="map-box relative">
            <div id="map">
                @if($settings?->google_maps_url)
                    @if(str_starts_with(trim($settings->google_maps_url), '<iframe'))
                        {{-- Tam iframe HTML'i varsa direk göster --}}
                        {!! $settings->google_maps_url !!}
                    @else
                        {{-- Sadece URL varsa iframe oluştur --}}
                        <iframe
                            src="{{ $settings->google_maps_url }}"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    @endif
                @endif
            </div>
        </div>
        <section class="section-contact page-contact">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content">
                            <div class="box-contact">
                                <div class="heading-section text-start">
                                    <p class="text-2 sub">{{ __('contact.subtitle') }}</p>
                                    <h3>{{ __('contact.title') }}</h3>
                                    <p class="description text-1 lh-30">
                                        {{ __('contact.description') }}
                                    </p>
                                </div>
                                <ul class="list-info">
                                    @if($settings?->contact_email)
                                        <li><i class="icon-Envelope"></i> <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></li>
                                    @endif
                                    @if($settings?->contact_phone)
                                        <li><i class="icon-PhoneCall"></i>{{ $settings->contact_phone }}</li>
                                    @endif
                                    @if($settings?->contact_address)
                                        <li><i class="icon-MapPin"></i>{{ $settings->contact_address }}</li>
                                    @endif
                                </ul>

                            </div>
                            <div class="booking-cta-card">
                                <div class="booking-cta-icon">
                                    <i class="icon-CalendarBlank"></i>
                                </div>
                                <h4 class="mb-12 text-center">{{ __('contact.booking_title') }}</h4>
                                <p class="booking-cta-description text-center">{{ __('contact.booking_description') }}</p>
                                <a href="https://iskenderpehlivan.janeapp.com/#staff_member/1" target="_blank" rel="noopener noreferrer" class="tf-btn style-default btn-color-secondary pd-40 boder-8 booking-cta-btn">
                                    <span><i class="icon-CalendarBlank"></i> {{ __('contact.booking_button') }}</span>
                                </a>
                                <p class="booking-cta-badge"><i class="icon-CheckCircle"></i> {{ __('contact.booking_badge') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endvolt
