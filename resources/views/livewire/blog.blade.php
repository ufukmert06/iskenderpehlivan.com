@volt
<?php

use function Livewire\Volt\{computed, mount, state, title};

state(['slug', 'locale']);

mount(function (string $slug) {
    $this->slug = $slug;
    $this->locale = app()->getLocale();
});

$blog = computed(function () {
    $blog = \App\Models\Post::where('type', 'blog')
        ->where('slug_base', $this->slug)
        ->where('status', 'published')
        ->with(['translations', 'categories.translations', 'tags.translations', 'user'])
        ->first();

    if (! $blog || ! $blog->translation($this->locale)) {
        abort(404);
    }

    return $blog;
});

$relatedBlogs = computed(function () {
    $categoryIds = $this->blog->categories->pluck('id')->toArray();

    return \App\Models\Post::where('type', 'blog')
        ->where('status', 'published')
        ->where('id', '!=', $this->blog->id)
        ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $categoryIds))
        ->with(['translations', 'categories.translations'])
        ->limit(3)
        ->get()
        ->filter(fn ($post) => $post->translation($this->locale) !== null);
});

$settings = computed(fn () => \App\Models\Setting::with('translations')->first());

title(fn () => ($this->blog->translation($this->locale)?->title ?? __('blog.title')) . ' - ' . config('app.name'));

?>

<div>
    @php
        $translation = $this->blog->translation($this->locale);
        $category = $this->blog->categories->first();
        $categoryTranslation = $category?->translation($this->locale);
    @endphp

    <div class="page-title">
        <div class="tf-container">
            <div class="row">
                <div class="col-12">
                    <h3 class="title">{{ $translation->title }}</h3>
                    <ul class="breadcrumbs">
                        <li><a href="{{ route($locale === 'tr' ? 'tr.home' : 'home') }}">{{ __('blog.breadcrumb_home') }}</a></li>
                        <li><a href="{{ route($locale === 'tr' ? 'tr.blogs' : 'blogs') }}">{{ __('blog.breadcrumb_blog') }}</a></li>
                        <li>{{ Str::limit($translation->title, 50) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content page-blog-details">
        <section class="section-blog-details">
            <div class="tf-container">
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <article class="blog-article">
                            @if($this->blog->featured_image)
                                <div class="image-wrap mb-4">
                                    <img src="{{ Storage::url($this->blog->featured_image) }}" alt="{{ $translation->title }}" class="img-fluid">
                                </div>
                            @endif

                            <div class="blog-meta mb-3">
                                @if($categoryTranslation)
                                    <span class="category">{{ $categoryTranslation->name }}</span>
                                @endif
                                @if($translation->published_at)
                                    <span class="date">
                                        {{ __('blog.published_on') }} {{ $translation->published_at->format('d M Y') }}
                                    </span>
                                @endif
                                @if($this->blog->user)
                                    <span class="author">
                                        {{ __('blog.by') }} {{ $this->blog->user->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="blog-content prose max-w-none">
                                {!! $translation->content !!}
                            </div>

                            @if($this->blog->tags->count() > 0)
                                <div class="blog-tags mt-4">
                                    <h6>{{ __('blog.tags') }}:</h6>
                                    <div class="tags-list">
                                        @foreach($this->blog->tags as $tag)
                                            @php $tagTranslation = $tag->translation($this->locale); @endphp
                                            @if($tagTranslation)
                                                <span class="tag">{{ $tagTranslation->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </article>

                        @if($this->relatedBlogs->count() > 0)
                            <section class="related-posts mt-5">
                                <h4 class="mb-4">{{ __('blog.related_posts') }}</h4>
                                <div class="row">
                                    @foreach($this->relatedBlogs as $relatedBlog)
                                        @php
                                            $relatedTranslation = $relatedBlog->translation($this->locale);
                                            $relatedCategory = $relatedBlog->categories->first();
                                            $relatedCategoryTranslation = $relatedCategory?->translation($this->locale);
                                        @endphp
                                        @if($relatedTranslation)
                                            <div class="col-md-4">
                                                <div class="article-blog-item hover-img">
                                                    <div class="image-wrap">
                                                        <a href="{{ route($locale === 'tr' ? 'tr.blog.show' : 'blog.show', $relatedBlog->slug_base) }}">
                                                            @if($relatedBlog->featured_image)
                                                                <img class="lazyload" data-src="{{ Storage::url($relatedBlog->featured_image) }}"
                                                                     src="{{ Storage::url($relatedBlog->featured_image) }}" alt="{{ $relatedTranslation->title }}">
                                                            @else
                                                                <img class="lazyload" data-src="/assets/images/blog/blog-1.jpg"
                                                                     src="/assets/images/blog/blog-1.jpg" alt="{{ $relatedTranslation->title }}">
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <div class="content">
                                                        @if($relatedCategoryTranslation)
                                                            <p class="sub">{{ $relatedCategoryTranslation->name }}</p>
                                                        @endif
                                                        <h6 class="title">
                                                            <a href="{{ route($locale === 'tr' ? 'tr.blog.show' : 'blog.show', $relatedBlog->slug_base) }}">
                                                                {{ Str::limit($relatedTranslation->title, 50) }}
                                                            </a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </div>

                    <div class="col-lg-4 col-12">
                        <aside class="blog-sidebar">
                            <div class="sidebar-widget">
                                <h5>{{ __('blog.categories') }}</h5>
                                <ul class="category-list">
                                    @foreach(\App\Models\Category::where('type', 'blog')->with('translations')->get() as $cat)
                                        @php $catTranslation = $cat->translation($this->locale); @endphp
                                        @if($catTranslation)
                                            <li>
                                                <a href="{{ route($locale === 'tr' ? 'tr.blogs' : 'blogs') }}?category={{ $cat->slug_base }}">
                                                    {{ $catTranslation->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            <div class="sidebar-widget">
                                <h5>{{ __('blog.latest_posts') }}</h5>
                                <div class="latest-posts-list">
                                    @foreach(\App\Models\Post::where('type', 'blog')->where('status', 'published')->with(['translations'])->orderBy('created_at', 'desc')->limit(5)->get() as $latestPost)
                                        @php $latestTranslation = $latestPost->translation($this->locale); @endphp
                                        @if($latestTranslation)
                                            <div class="latest-post-item">
                                                <a href="{{ route($locale === 'tr' ? 'tr.blog.show' : 'blog.show', $latestPost->slug_base) }}">
                                                    {{ Str::limit($latestTranslation->title, 60) }}
                                                </a>
                                                <span class="date">{{ $latestTranslation->published_at?->format('d M Y') }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endvolt
