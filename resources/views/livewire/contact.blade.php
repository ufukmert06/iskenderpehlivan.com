@volt
<?php

use function Livewire\Volt\rules;
use function Livewire\Volt\state;
use function Livewire\Volt\title;

state(['settings', 'settingTranslation', 'name', 'email', 'service', 'message']);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'service' => 'nullable|string|max:255',
    'message' => 'required|string|max:5000',
]);

$settings = \App\Models\Setting::first();

if ($settings) {
    $settingTranslation = $settings->translations()
        ->where('locale', app()->getLocale())
        ->first();
}

title(__('contact.title').' - '.config('app.name'));

$submit = function () {
    $this->validate();

    \App\Models\Contact::create([
        'name' => $this->name,
        'email' => $this->email,
        'service' => $this->service,
        'message' => $this->message,
        'status' => 'unread',
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    $this->reset(['name', 'email', 'service', 'message']);

    session()->flash('success', __('contact.form_success'));
};

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
                            <form class="form-consultation" wire:submit="submit">
                                <h4 class="mb-20 text-center">{{ __('contact.form_title') }}</h4>

                                @if (session()->has('success'))
                                    <div class="alert alert-success mb-20">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <fieldset class="name">
                                    <input type="text" wire:model="name" class="tf-input style-1" placeholder="{{ __('contact.form_name') }}" tabindex="1" aria-required="true">
                                    @error('name') <span class="error text-red-500">{{ $message }}</span> @enderror
                                </fieldset>

                                <fieldset class="email">
                                    <input type="email" wire:model="email" class="tf-input style-1" placeholder="{{ __('contact.form_email') }}" tabindex="2" aria-required="true">
                                    @error('email') <span class="error text-red-500">{{ $message }}</span> @enderror
                                </fieldset>

                                <div class="select-custom mb-20">
                                    <select wire:model="service" id="service" data-default="">
                                        <option value="">{{ __('contact.form_service_placeholder') }}</option>
                                        <option value="Individual Therapy">{{ __('contact.service_individual') }}</option>
                                        <option value="Couples Counseling">{{ __('contact.service_couples') }}</option>
                                        <option value="Child and Family Therapy">{{ __('contact.service_family') }}</option>
                                        <option value="Migration and Cultural Adaptation">{{ __('contact.service_migration') }}</option>
                                    </select>
                                    @error('service') <span class="error text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <fieldset class="message">
                                    <textarea wire:model="message" id="message" class="tf-input" rows="4" placeholder="{{ __('contact.form_message') }}" tabindex="4" aria-required="true"></textarea>
                                    @error('message') <span class="error text-red-500">{{ $message }}</span> @enderror
                                </fieldset>

                                <button class="tf-btn style-default btn-color-secondary pd-40 boder-8 send-wrap" type="submit">
                                    <span wire:loading.remove wire:target="submit">{{ __('contact.form_submit') }}</span>
                                    <span wire:loading.inline wire:target="submit" style="display: none;">{{ __('contact.form_sending') }}</span>
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
