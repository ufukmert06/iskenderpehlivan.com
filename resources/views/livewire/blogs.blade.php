@volt
<?php

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state(['locale']);

$locale = app()->getLocale();

$blogs = computed(fn () => \App\Models\Post::where('type', 'blog')
    ->where('status', 'published')
    ->with(['translations' => function ($query) {
        $query->where('locale', app()->getLocale())
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');
    }, 'categories.translations'])
    ->orderBy('sort_order')
    ->get()
    ->filter(fn ($post) => $post->translation($this->locale) !== null));

$settings = computed(fn () => \App\Models\Setting::with('translations')->first());

?>

<div>
    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ __('blog.title') }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('blog.breadcrumb_home') }}</a></li>
                        <li>{{ __('blog.breadcrumb_blog') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content page-blog">
        <section class="section-blog-grid">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        @if($this->blogs->count() > 0)
                            <div class="grid-layout-3">
                                @foreach($this->blogs as $blog)
                                    @php
                                        $translation = $blog->translation($locale);
                                        $category = $blog->categories->first();
                                        $categoryTranslation = $category?->translation($locale);
                                    @endphp
                                    @if($translation)
                                        <div class="article-blog-item hover-img">
                                            <div class="image-wrap">
                                                <a href="{{ route($locale === 'tr' ? 'tr.blog.show' : 'blog.show', $blog->slug_base) }}">
                                                    @if($blog->featured_image)
                                                        <img class="lazyload" data-src="{{ Storage::url($blog->featured_image) }}"
                                                             src="{{ Storage::url($blog->featured_image) }}" alt="{{ $translation->title }}">
                                                    @else
                                                        <img class="lazyload" data-src="/assets/images/blog/blog-1.jpg"
                                                             src="/assets/images/blog/blog-1.jpg" alt="{{ $translation->title }}">
                                                    @endif
                                                </a>
                                                <div class="date-time">
                                                    <div class="content">
                                                        <p class="entry-day">{{ $translation->published_at->format('d') }}</p>
                                                        <p class="entry-month fw-book">{{ $translation->published_at->format('M') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="content">
                                                @if($categoryTranslation)
                                                    <p class="sub">{{ $categoryTranslation->name }}</p>
                                                @endif
                                                <h5 class="title">
                                                    <a href="{{ route($locale === 'tr' ? 'tr.blog.show' : 'blog.show', $blog->slug_base) }}">
                                                        {{ $translation->title }}
                                                    </a>
                                                </h5>
                                                <p>{{ Str::limit($translation->excerpt ?? strip_tags($translation->content), 150) }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="text-1">{{ __('blog.no_posts') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endvolt
