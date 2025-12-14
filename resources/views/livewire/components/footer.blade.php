<?php

use App\Models\Newsletter;
use App\Models\Post;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component
{
    public ?Setting $settings = null;

    public $services;

    public $locale;

    #[Validate('required|email|unique:newsletters,email')]
    public $newsletter_email = '';

    public function mount(): void
    {
        $this->locale = app()->getLocale();
        $this->settings = Setting::with('translations')->first();

        // Load published services for footer
        $this->services = Post::where('type', 'service')
            ->where('status', 'published')
            ->with('translations')
            ->orderBy('sort_order')
            ->limit(4)
            ->get();
    }

    public function subscribe(): void
    {
        $this->validate();

        Newsletter::create([
            'email' => $this->newsletter_email,
            'status' => 'active',
            'ip_address' => request()->ip(),
            'subscribed_at' => now(),
        ]);

        session()->flash('newsletter_success', __('newsletter.subscribed_successfully'));

        $this->newsletter_email = '';
    }

    public function getTranslation(): mixed
    {
        if (! $this->settings) {
            return null;
        }

        return $this->settings->translation(app()->getLocale());
    }
}; ?>

<div>
    <footer id="footer">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <div class="footer-main">
                        <div class="footer-left">
                            @if($settings?->dark_logo)
                                <div class="footer-logo">
                                    <a href="{{ route('home') }}">
                                        <img id="logo_footer" src="{{ Storage::url($settings->dark_logo) }}" data-retina="{{ Storage::url($settings->dark_logo) }}" alt="{{ $this->getTranslation()?->site_name ?? config('app.name') }}">
                                    </a>
                                </div>
                            @endif
                            @if($this->getTranslation()?->site_description)
                                <p class="description">{{ $this->getTranslation()->site_description }}</p>
                            @endif
                            <ul class="footer-info">
                                @if($settings?->contact_address)
                                    <li>{{ $settings->contact_address }}</li>
                                @endif
                                @if($settings?->contact_email)
                                    <li>{{ __('common.support_247') }}: <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></li>
                                @endif
                                @if($settings?->contact_phone)
                                    <li>{{ __('common.call_us_now') }}: <a href="tel:{{ $settings->contact_phone }}">{{ $settings->contact_phone }}</a></li>
                                @endif
                            </ul>

                            {{-- WhatsApp Button --}}
                            @if($settings?->whatsapp)
                                <div class="whatsapp-section" style="margin: 20px 0;">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}" target="_blank" rel="noopener" style="display: inline-block;">
                                        <img src="/images/whatsapp-en.png" alt="Chat on WhatsApp" style="max-width: 250px; height: auto; border-radius: 8px;">
                                    </a>
                                </div>
                            @endif

                            {{-- Social Media Icons with Psychology Today --}}
                            <ul class="tf-social" style="display: flex; gap: 15px; align-items: center; list-style: none; padding: 0; margin: 20px 0;">
                                @if($settings?->linkedin)
                                    <li>
                                        <a href="{{ $settings->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn" style="display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 50%; transition: background 0.3s;">
                                            <i class="fab fa-linkedin" style="color: white; font-size: 22px;"></i>
                                        </a>
                                    </li>
                                @endif
                                @if($settings?->facebook)
                                    <li>
                                        <a href="{{ $settings->facebook }}" target="_blank" rel="noopener" aria-label="Facebook" style="display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 50%; transition: background 0.3s;">
                                            <i class="fab fa-facebook" style="color: white; font-size: 22px;"></i>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a href="https://www.psychologytoday.com/ca/therapists/iskender-pehlivan-burnaby-bc/1574577" target="_blank" rel="noopener" aria-label="Psychology Today">
                                        <img src="/images/logo-psychology-today-blue.svg" alt="Psychology Today" style="height: 32px; width: auto;">
                                    </a>
                                </li>
							
                            </ul>
							<a href="https://counsellingbc.com/listings/ipehlivan.htm" target="_blank" rel="noopener">
                                                <img src="https://istcounselling.com/images/svgexport-2.png" alt="Verified by Psychology Today" style="width: 100%; height: auto; display: block;">
                                            </a>
                        </div>
                        <div class="footer-right">
                            <div class="wrap-footer-menu-list">
                                <div class="footer-menu-list footer-col-block">
                                    <h6 class="title title-desktop">{{ __('footer.quick_links') }}</h6>
                                    <h6 class="title title-mobile">{{ __('footer.quick_links') }}</h6>
                                    <ul class="tf-collapse-content">
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.about' : 'about') }}">{{ __('common.about') }}</a></li>
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.services' : 'services') }}">{{ __('common.services') }}</a></li>
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.contact' : 'contact') }}">{{ __('common.contact') }}</a></li>
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.terms-of-service' : 'terms-of-service') }}">{{ __('footer.terms_of_service') }}</a></li>
                                        <li><a href="{{ route($locale === 'tr' ? 'tr.privacy-policy' : 'privacy-policy') }}">{{ __('footer.privacy_policy') }}</a></li>
                                    </ul>
                                </div>
                                <div class="footer-menu-list footer-col-block">
                                    <h6 class="title title-desktop">{{ __('common.our_services') }}</h6>
                                    <h6 class="title title-mobile">{{ __('common.our_services') }}</h6>
                                    <ul class="tf-collapse-content">
                                        @forelse($services as $service)
                                            @php
                                                $translation = $service->translation($locale);
                                            @endphp
                                            @if($translation)
                                                <li>
                                                    <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $service->slug_base) }}">
                                                        {{ $translation->title }}
                                                    </a>
                                                </li>
                                            @endif
                                        @empty
                                            <li><a href="{{ route($locale === 'tr' ? 'tr.services' : 'services') }}">{{ __('common.services') }}</a></li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="wrap-form footer-col-block">
                                <h6 class="title title-desktop">{{ __('footer.accreditations') ?? 'Accreditations' }}</h6>
                                <h6 class="title title-mobile">{{ __('footer.accreditations') ?? 'Accreditations' }}</h6>
                                <div class="tf-collapse-content">
                                    {{-- Accreditation Cards --}}
                                    <div class="accreditation-cards" style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                                        {{-- Psychology Today Verified Badge --}}
                                        <div style="background: white; padding: 15px 20px; border-radius: 8px;">
											
                                            <a href="https://www.psychologytoday.com/ca/therapists/iskender-pehlivan-burnaby-bc/1574577" target="_blank" rel="noopener">
                                                <img src="/images/psychology_today.jpg" alt="Verified by Psychology Today" style="width: 100%; height: auto; display: block;">
                                            </a>
											
                                        </div>

                                        {{-- RCC & BCACC Cards --}}
                                        <div style="display: flex; gap: 12px;">
                                            <div style="background: white; padding: 20px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex: 1;">
												
                                                <a href="https://www.crpo.ca" target="_blank" rel="noopener">
                                                    <img src="/images/rcc.png" alt="Registered Clinical Counsellor" style="width: 65px; height: auto;">
                                                </a>
                                            </div>
                                            <div style="background: white; padding: 20px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex: 1;">
                                                <a href="https://bcacc.ca/counsellors/iskender-pehlivan/" target="_blank" rel="noopener">
                                                    <img src="/images/bcacc.png" alt="BC Association of Clinical Counsellors" style="width: 80px; height: auto;">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer-bottom">
                        @if($this->getTranslation()?->footer_text)
                            <p>{{ $this->getTranslation()->footer_text }}</p>
                        @else
                            <p>© {{ date('Y') }} {{ $this->getTranslation()?->site_name ?? config('app.name') }}. {{ __('common.all_rights_reserved') }}.</p>
                        @endif
                        <ul class="content-right">
                            <li><a href="{{ route($locale === 'tr' ? 'tr.terms-of-service' : 'terms-of-service') }}">{{ __('common.terms_of_service') }}</a></li>
                            <li><a href="{{ route($locale === 'tr' ? 'tr.privacy-policy' : 'privacy-policy') }}">{{ __('common.privacy_policy') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
