@volt
<?php

use function Livewire\Volt\{computed, mount, state, rules};

state(['settings', 'settingTranslation', 'name', 'phone', 'service_id', 'preferred_date', 'preferred_time', 'message', 'locale']);

rules([
    'name' => 'required|string|max:255',
    'phone' => 'required|string|max:20',
    'service_id' => 'required|exists:posts,id',
    'preferred_date' => 'required|date|after_or_equal:today',
    'preferred_time' => 'required|string|max:10',
    'message' => 'nullable|string|max:1000',
]);

mount(function () {
    $this->locale = app()->getLocale();

    $this->settings = \App\Models\Setting::first();

    if ($this->settings) {
        $this->settingTranslation = $this->settings->translations()
            ->where('locale', $this->locale)
            ->first();
    }
});

$services = computed(function () {
    return \App\Models\Post::with('translations')
        ->where('type', 'service')
        ->where('status', 'published')
        ->orderBy('sort_order')
        ->get();
});

$submit = function () {
    $this->validate();

    \App\Models\Appointment::create([
        'name' => $this->name,
        'phone' => $this->phone,
        'service_id' => $this->service_id,
        'preferred_date' => $this->preferred_date,
        'preferred_time' => $this->preferred_time,
        'message' => $this->message,
        'status' => 'pending',
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    $this->reset(['name', 'phone', 'service_id', 'preferred_date', 'preferred_time', 'message']);

    session()->flash('success', __('appointment.form_success'));
};

?>

<div>
    <div class="main-content-2 page-appointment bg-1">
        <section class="section-book-appointment">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
                            <form class="form-appointment" style="flex: 1; min-width: 300px;" wire:submit="submit">
                                <div class="heading-section text-start">
                                    <h3>{{ __('appointment.title') }}</h3>
                                    <p class="description text-1">{{ __('appointment.description') }}</p>
                                </div>

                                @if (session()->has('success'))
                                    <div class="alert alert-success mb-20">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="cols mb-20">
                                    <fieldset class="name">
                                        <input type="text" wire:model="name" class="tf-input style-2" placeholder="{{ __('appointment.form_name') }}" tabindex="1"  >
                                        @error('name') <span class="error text-red-500">{{ $message }}</span> @enderror
                                    </fieldset>
                                    <fieldset class="phone">
                                        <input type="tel" wire:model="phone" class="tf-input style-2" placeholder="{{ __('appointment.form_phone') }}" tabindex="2"  >
                                        @error('phone') <span class="error text-red-500">{{ $message }}</span> @enderror
                                    </fieldset>
                                </div>

                                <div class="cols mb-20">
                                    <div class="select-custom" style="width: 100%;">
                                        <select wire:model="service_id" id="service_id" class="tf-select" data-default="">
                                            <option value="">{{ __('appointment.form_service_placeholder') }}</option>
                                            @foreach ($this->services as $service)
                                                <option value="{{ $service->id }}">
                                                    {{ $service->translation($locale)?->title ?? $service->slug_base }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_id') <span class="error text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="cols mb-20">
                                    <fieldset class="date">
                                        <input type="date" wire:model="preferred_date" class="tf-input style-2"  tabindex="5" >
                                        @error('preferred_date') <span class="error text-red-500">{{ $message }}</span> @enderror
                                    </fieldset>
                                    <fieldset class="time">
                                        <input type="time" wire:model="preferred_time" class="tf-input style-2"  tabindex="6" >
                                        @error('preferred_time') <span class="error text-red-500">{{ $message }}</span> @enderror
                                    </fieldset>
                                </div>

                                <fieldset>
                                    <textarea wire:model="message" id="message" class="tf-input" name="message" rows="4" placeholder="{{ __('appointment.form_message') }}" tabindex="7" ></textarea>
                                    @error('message') <span class="error text-red-500">{{ $message }}</span> @enderror
                                </fieldset>

                                <button class="tf-btn style-default btn-color-secondary pd-40 boder-8" type="submit">
                                    <span wire:loading.remove wire:target="submit">{{ __('appointment.form_submit') }}</span>
                                    <span wire:loading.inline wire:target="submit" style="display: none;">{{ __('appointment.form_sending') }}</span>
                                </button>
                            </form>
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
