@volt
<?php

use function Livewire\Volt\{computed, mount, state};

state(['locale', 'query' => '']);

mount(function () {
    $this->locale = app()->getLocale();
    $this->query = request('q', '');
});

$results = computed(function () {
    if (empty($this->query) || strlen($this->query) < 2) {
        return collect();
    }

    $searchTerm = '%' . $this->query . '%';

    return \App\Models\Post::where('status', 'published')
        ->whereHas('translations', function ($q) use ($searchTerm) {
            $q->where('title', 'like', $searchTerm)
                ->orWhere('excerpt', 'like', $searchTerm)
                ->orWhere('content', 'like', $searchTerm);
        })
        ->with('translations')
        ->orderBy('created_at', 'desc')
        ->get();
});

?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ __('search.title') }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('common.home') }}</a></li>
                        <li>{{ __('search.breadcrumb') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content-2">
        <section class="section-search-results tf-spacing-3">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section text-center mb-40">
                            @if($query)
                                <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    {{ __('search.results_for') }}: "{{ $query }}"
                                </h3>
                                <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="200ms">
                                    @if($this->results->count() > 0)
                                        {{ __('search.found_results', ['count' => $this->results->count()]) }}
                                    @else
                                        {{ __('search.no_results') }}
                                    @endif
                                </p>
                            @else
                                <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    {{ __('search.enter_query') }}
                                </h3>
                            @endif
                        </div>

                        @if($this->results->count() > 0)
                            <div class="grid-layout-3 multi-item">
                                @foreach($this->results as $index => $post)
                                    @php
                                        $translation = $post->translation($locale);
                                        $delay = ($index % 3) * 0.1;
                                        $isService = $post->type === 'service';
                                    @endphp
                                    @if($translation)
                                        <div class="service-item style-3 hover-img wow fadeInUp" data-wow-duration="1000" data-wow-delay="{{ $delay }}s">
                                            <div class="content z-5">
                                                <span class="badge bg-secondary mb-2">
                                                    {{ $isService ? __('search.type_service') : __('search.type_blog') }}
                                                </span>
                                                <h5 class="title">
                                                    @if($isService)
                                                        <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $post->slug_base) }}">
                                                            {{ $translation->title }}
                                                        </a>
                                                    @else
                                                        <a href="#">
                                                            {{ $translation->title }}
                                                        </a>
                                                    @endif
                                                </h5>
                                                <p>{{ Str::limit($translation->excerpt ?? '', 120) }}</p>
                                            </div>
                                            @if($post->featured_image)
                                                <div class="image-wrap z-5 relative">
                                                    @if($isService)
                                                        <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $post->slug_base) }}">
                                                            <img class="lazyload" data-src="{{ Storage::url($post->featured_image) }}" src="{{ Storage::url($post->featured_image) }}" alt="{{ $translation->title }}">
                                                        </a>
                                                    @else
                                                        <img class="lazyload" data-src="{{ Storage::url($post->featured_image) }}" src="{{ Storage::url($post->featured_image) }}" alt="{{ $translation->title }}">
                                                    @endif
                                                </div>
                                            @endif
                                            @if($isService)
                                                <a href="{{ route($locale === 'tr' ? 'tr.service.show' : 'service.show', $post->slug_base) }}" class="tf-btn-link z-5">
                                                    <span data-text="{{ __('search.view_details') }}">{{ __('search.view_details') }}</span>
                                                    <i class="icon-arrow-right"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($query)
                            <div class="text-center mt-40 wow fadeInUp" data-wow-duration="1000" data-wow-delay="400ms">
                                <div class="mb-40">
                                    <i class="icon-MagnifyingGlass" style="font-size: 64px; color: #ccc;"></i>
                                </div>
                                <h4 class="mb-20">{{ __('search.no_results_title') }}</h4>
                                <p class="text-1 mb-40">{{ __('search.no_results_description') }}</p>
                                <div class="d-flex justify-content-center gap-3 flex-wrap">
                                    <a href="{{ route($locale === 'tr' ? 'tr.services' : 'services') }}" class="tf-btn style-default btn-color-secondary pd-40">
                                        <span>{{ __('search.browse_services') }}</span>
                                    </a>
                                    <a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}" class="tf-btn style-default btn-color-secondary pd-40">
                                        <span>{{ __('search.back_home') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endvolt
