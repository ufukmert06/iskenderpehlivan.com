@volt
<?php
use function Livewire\Volt\{computed};

$services = computed(function() {
    return \App\Models\Post::where('type', 'service')
        ->where('status', 'published')
        ->with('translations')
        ->orderBy('sort_order')
        ->get();
});

$locale = app()->getLocale();
?>

<div class="services-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading">
                    <h2 class="heading">Sunulan Hizmetler</h2>
                    <p class="description">Ruh sağlığınız ve kişisel gelişiminiz için kapsamlı danışmanlık hizmetleri</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($this->services as $service)
                @php
                    $translation = $service->translation($locale);
                @endphp
                <div class="col-md-6 col-lg-4" wire:key="service-{{ $service->id }}">
                    <div class="service-card h-100">
                        @if($service->icon)
                            <div class="service-icon">
                                @if(str_starts_with($service->icon, 'heroicon'))
                                    <i class="{{ $service->icon }}"></i>
                                @else
                                    <span>{{ $service->icon }}</span>
                                @endif
                            </div>
                        @endif
                        <h3 class="service-title">
                            {{ $translation?->title ?? $service->id }}
                        </h3>
                        <p class="service-description">
                            {{ $translation?->excerpt }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Şu anda hizmet bulunmamaktadır.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.services-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.section-heading {
    text-align: center;
    margin-bottom: 60px;
}

.section-heading h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #2d3748;
}

.section-heading .description {
    font-size: 1.1rem;
    color: #718096;
    max-width: 500px;
    margin: 0 auto;
}

.service-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    border-left-color: #667eea;
}

.service-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #667eea;
}

.service-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #2d3748;
}

.service-description {
    color: #718096;
    line-height: 1.6;
    margin: 0;
}

@media (max-width: 768px) {
    .services-section {
        padding: 50px 0;
    }

    .section-heading h2 {
        font-size: 2rem;
    }

    .service-card {
        padding: 20px;
    }
}
</style>
@endvolt
