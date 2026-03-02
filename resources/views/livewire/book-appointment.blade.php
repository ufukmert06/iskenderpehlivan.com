@volt
<?php

use function Livewire\Volt\{mount, state, title};

state(['settings', 'settingTranslation', 'locale']);

mount(function () {
    $this->locale = app()->getLocale();

    $this->settings = \App\Models\Setting::first();

    if ($this->settings) {
        $this->settingTranslation = $this->settings->translations()
            ->where('locale', $this->locale)
            ->first();
    }
});

title(__('appointment.title') . ' - ' . config('app.name'));

?>

<div>
    <div class="main-content-2 page-appointment bg-1">
        <section class="section-book-appointment">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
                            <div class="booking-cta-card booking-cta-card--wide" style="flex: 1; min-width: 300px;">
                                <div class="heading-section text-start">
                                    <h3>{{ __('appointment.title') }}</h3>
                                    <p class="description text-1">{{ __('appointment.description') }}</p>
                                </div>
                                <div class="booking-cta-icon">
                                    <i class="icon-CalendarBlank"></i>
                                </div>
                                <p class="booking-cta-description">{{ __('appointment.booking_description') }}</p>
                                <a href="https://iskenderpehlivan.janeapp.com/#staff_member/1" target="_blank" rel="noopener noreferrer" class="tf-btn style-default btn-color-secondary pd-40 boder-8 booking-cta-btn">
                                    <span><i class="icon-CalendarBlank"></i> {{ __('appointment.booking_button') }}</span>
                                </a>
                                <p class="booking-cta-badge"><i class="icon-CheckCircle"></i> {{ __('appointment.booking_badge') }}</p>
                            </div>
                            <div class="image-wrap" style="flex: 1; min-width: 300px; overflow: hidden; display: flex;">
                                <img class="lazyload" data-src="{{ asset('images/randevu.jpeg') }}" src="{{ asset('images/randevu.jpeg') }}" alt="{{ __('appointment.title') }}" style="width: 100%; height: auto; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endvolt
