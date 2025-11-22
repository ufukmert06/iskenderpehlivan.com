@volt
<?php

use function Livewire\Volt\{computed, mount, state, rules};

state(['slug', 'service', 'translation', 'locale', 'name', 'phone', 'selected_service', 'message']);

rules([
    'name' => 'required|string|max:255',
    'phone' => 'nullable|string|max:20',
    'selected_service' => 'nullable|string|max:255',
    'message' => 'required|string|max:5000',
]);

mount(function () {
    $this->locale = app()->getLocale();

    // Find service by slug_base (from Post model where type='service')
    $this->service = \App\Models\Post::with('translations')
        ->where('type', 'service')
        ->where('slug_base', $this->slug)
        ->where('status', 'published')
        ->firstOrFail();

    $this->translation = $this->service->translation($this->locale);

    if (! $this->translation) {
        abort(404, 'Service translation not found');
    }

    // Pre-select the current service
    $this->selected_service = $this->translation->title;
});

$settings = computed(fn () => \App\Models\Setting::first());

$otherServices = computed(fn () => \App\Models\Post::where('type', 'service')
    ->where('status', 'published')
    ->where('id', '!=', $this->service->id)
    ->orderBy('sort_order')
    ->limit(6)
    ->get());

$allServices = computed(fn () => \App\Models\Post::where('type', 'service')
    ->where('status', 'published')
    ->with('translations')
    ->orderBy('sort_order')
    ->get());

$submit = function () {
    $this->validate();

    \App\Models\Contact::create([
        'name' => $this->name,
        'email' => null, // Service form doesn't have email
        'service' => $this->selected_service,
        'phone' => $this->phone,
        'message' => $this->message,
        'status' => 'unread',
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    $this->reset(['name', 'phone', 'message']);
    $this->selected_service = $this->translation->title;

    session()->flash('success', __('contact.form_success'));
};

?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ $translation->title }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li>{{ __('common.services') }}</li>
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
                                <div class="lh-30 mb-30">
                                    {!! $translation->content !!}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-siderbar sticky">
                            <form class="form-consultation" wire:submit="submit">
                                <h5 class="mb-16">{{ __('service.consultation_title') }}</h5>

                                @if (session()->has('success'))
                                    <div class="alert alert-success mb-20" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <fieldset class="name">
                                    <input type="text" wire:model="name" class="tf-input style-1" placeholder="{{ __('contact.form_name') }}" tabindex="1" aria-required="true">
                                    @error('name') <span class="error" style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                                </fieldset>

                                <fieldset class="phone">
                                    <input type="text" wire:model="phone" class="tf-input style-1" placeholder="{{ __('service.form_phone') }}" tabindex="2" aria-required="true">
                                    @error('phone') <span class="error" style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                                </fieldset>

                                <div class="select-custom mb-20">
                                    <select wire:model="selected_service" id="service">
                                        <option value="">{{ __('service.form_choose_service') }}</option>
                                        @foreach($this->allServices as $availableService)
                                            @php
                                                $serviceTranslation = $availableService->translation($locale);
                                            @endphp
                                            @if($serviceTranslation)
                                                <option value="{{ $serviceTranslation->title }}">{{ $serviceTranslation->title }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('selected_service') <span class="error" style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                                </div>

                                <fieldset>
                                    <textarea wire:model="message" id="message" class="tf-input" rows="4" placeholder="{{ __('contact.form_message') }}" tabindex="4" aria-required="true"></textarea>
                                    @error('message') <span class="error" style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                                </fieldset>

                                <button class="tf-btn style-default btn-color-secondary pd-40 boder-8 send-wrap" type="submit">
                                    <span wire:loading.remove wire:target="submit">{{ __('contact.form_submit') }}</span>
                                    <span wire:loading.inline wire:target="submit" style="display: none;">{{ __('contact.form_sending') }}</span>
                                </button>

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
                            </form>
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
</div>
@endvolt
