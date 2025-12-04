<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $currentLocale;

    public string $nextLocale;

    public string $switchUrl;

    public function mount(): void
    {
        $this->currentLocale = app()->getLocale();
        $this->nextLocale = $this->currentLocale === 'tr' ? 'en' : 'tr';
        $this->switchUrl = $this->getLocaleSwitchUrl();
    }

    protected function getLocaleSwitchUrl(): string
    {
        $route = request()->route();

        if (!$route) {
            // Default to Turkish home if switching from English
            return $this->nextLocale === 'tr' ? route('tr.home') : route('home');
        }

        $routeName = $route->getName();
        $parameters = $route->parameters();

        // Convert between English and Turkish route names
        if ($this->nextLocale === 'tr') {
            // Switching to Turkish: add 'tr.' prefix
            $newRouteName = str_starts_with($routeName, 'tr.') ? $routeName : 'tr.' . $routeName;
        } else {
            // Switching to English: remove 'tr.' prefix
            $newRouteName = str_starts_with($routeName, 'tr.') ? substr($routeName, 3) : $routeName;
        }

        // Check if the target route exists
        if ($newRouteName && \Illuminate\Support\Facades\Route::has($newRouteName)) {
            return route($newRouteName, $parameters);
        }

        // Fallback to home routes
        return $this->nextLocale === 'tr' ? route('tr.home') : route('home');
    }
}; ?>

<div>
    <a href="{{ $switchUrl }}"
        class="btn-locale flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors hover:opacity-80 text-white"
        title="{{ $nextLocale === 'tr' ? 'Türkçe' : 'English' }}" wire:navigate>
        <div class="icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"
                    fill="currentColor" />
            </svg>
        </div>
        <span class="uppercase font-semibold">{{ $nextLocale }}</span>
    </a>
</div>