<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <!-- Fullscreen Image Slider with Grid Slice Reveal -->
    <div class="fullscreen-slider" x-data="imageSlider()" x-init="initSlider()">
        @php
            $slides = [
                [
                    'image' => asset('images/slide/slide1.png'),
                    'image_mobile' => asset('images/slide/slide1-mobile.jpeg'),
                    'title' => __('home.hero.title'),
                    'animated_text' => [__('home.hero.title_animated_1'), __('home.hero.title_animated_2')],
                    'description' => __('home.hero.description'),
                    'cta_text' => __('home.hero.cta_button'),
                    'cta_link' => route(__('home.hero.cta_link'))
                ],
                [
                    'image' => asset('images/slide/slide2.jpeg'),
                    'image_mobile' => asset('images/slide/slide2-mobile.jpeg'),
                    'title' => __('home.about.title'),
                    'animated_text' => [__('home.about.subtitle')],
                    'description' => __('home.about.description'),
                    'cta_text' => __('home.about.cta_button'),
                    'cta_link' => route(__('home.about.cta_link'))
                ],
                [
                    'image' => asset('images/slide/slide3.jpeg'),
                    'image_mobile' => asset('images/slide/slide3-mobile.jpeg'),
                    'title' => __('home.services.title'),
                    'animated_text' => [__('home.services.subtitle')],
                    'description' => __('home.services.description'),
                    'cta_text' => __('home.services.cta_button'),
                    'cta_link' => route(__('home.services.cta_link'))
                ],
            ];
            $gridRows = 4;
            $gridCols = 6;
        @endphp

        <!-- Slides Container -->
        <div class="slides-wrapper">
            @foreach($slides as $index => $slide)
                <div class="slide" data-slide="{{ $index }}" :class="{ 'active': currentSlide === {{ $index }} }">
                    <!-- Content Overlay -->
                    <div class="slide-content">
                        <div class="content-inner">
                            <div class="heading">
                                <h2 class="title">
                                    {{ $slide['title'] }}
                                    @if(count($slide['animated_text']) > 0)
                                        <span class="animated-text">
                                            @foreach($slide['animated_text'] as $textIndex => $text)
                                                <span class="text-item">{{ $text }}</span>
                                            @endforeach
                                        </span>
                                    @endif
                                </h2>
                                <p class="description">{{ $slide['description'] }}</p>
                            </div>
                            <a class="cta-button" href="{{ $slide['cta_link'] }}">
                                <span>{{ $slide['cta_text'] }} <i class="icon-ArrowRight"></i></span>
                            </a>
                        </div>
                    </div>

                    <!-- Grid Tiles - Desktop -->
                    <div class="grid-container grid-desktop" data-slide-index="{{ $index }}">
                        @for($row = 0; $row < $gridRows; $row++)
                            @for($col = 0; $col < $gridCols; $col++)
                                <div class="grid-tile"
                                     data-row="{{ $row }}"
                                     data-col="{{ $col }}"
                                     style="
                                         background-image: url('{{ $slide['image'] }}');
                                         background-size: {{ $gridCols * 100 }}% {{ $gridRows * 100 }}%;
                                         background-position: {{ ($col / max(1, $gridCols - 1)) * 100 }}% {{ ($row / max(1, $gridRows - 1)) * 100 }}%;
                                     ">
                                </div>
                            @endfor
                        @endfor
                    </div>

                    <!-- Grid Tiles - Mobile -->
                    @php
                        $mobileGridRows = 3;
                        $mobileGridCols = 4;
                    @endphp
                    <div class="grid-container grid-mobile" data-slide-index="{{ $index }}">
                        @for($row = 0; $row < $mobileGridRows; $row++)
                            @for($col = 0; $col < $mobileGridCols; $col++)
                                <div class="grid-tile"
                                     data-row="{{ $row }}"
                                     data-col="{{ $col }}"
                                     style="
                                         background-image: url('{{ $slide['image_mobile'] }}');
                                         background-size: {{ $mobileGridCols * 100 }}% {{ $mobileGridRows * 100 }}%;
                                         background-position: {{ ($col / max(1, $mobileGridCols - 1)) * 100 }}% {{ ($row / max(1, $mobileGridRows - 1)) * 100 }}%;
                                     ">
                                </div>
                            @endfor
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Navigation Controls -->
        <div class="slider-controls">
            <!-- Previous Button -->
            <button class="nav-btn prev-btn" @click="previousSlide()" aria-label="Previous Slide">
                <i class="icon-ArrowRight arrow-left"></i>
            </button>

            <!-- Next Button -->
            <button class="nav-btn next-btn" @click="nextSlide()" aria-label="Next Slide">
                <i class="icon-ArrowRight"></i>
            </button>

            <!-- Pagination Dots -->
            <div class="pagination-dots">
                @foreach($slides as $index => $slide)
                    <button class="dot"
                            :class="{ 'active': currentSlide === {{ $index }} }"
                            @click="goToSlide({{ $index }})"
                            aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageSlider', () => ({
            currentSlide: 0,
            totalSlides: 3,
            isAnimating: false,
            autoplayInterval: null,

            initSlider() {
                // Dynamically set slider height based on header height
                this.setSliderHeight();

                // Re-calculate on window resize
                window.addEventListener('resize', () => {
                    this.setSliderHeight();
                });

                // Wait for GSAP to be available
                const init = () => {
                    if (typeof gsap === 'undefined') {
                        setTimeout(init, 100);
                        return;
                    }

                    // Show first slide
                    this.showSlide(0, 'init');

                    // Start autoplay after 5 seconds
                    setTimeout(() => {
                        this.startAutoplay();
                    }, 5000);
                };

                init();
            },

            setSliderHeight() {
                const header = document.querySelector('#header-main, header, .header');
                const slider = document.querySelector('.fullscreen-slider');

                if (header && slider) {
                    // Get the actual computed height of the header
                    const headerHeight = header.getBoundingClientRect().height;

                    // Apply the calculated height
                    slider.style.height = `calc(100vh - ${headerHeight}px)`;

                    console.log('Header yüksekliği:', headerHeight + 'px');
                } else {
                    // Retry after a short delay if elements not found
                    setTimeout(() => this.setSliderHeight(), 100);
                }
            },

            showSlide(slideIndex, direction = 'next') {
                if (this.isAnimating) return;
                this.isAnimating = true;

                const slides = document.querySelectorAll('.slide');
                const currentSlideEl = slides[this.currentSlide];
                const nextSlideEl = slides[slideIndex];

                if (!nextSlideEl) return;

                // Detect which grid to animate based on screen size
                const isMobile = window.innerWidth <= 768;
                const gridSelector = isMobile ? '.grid-mobile' : '.grid-desktop';
                const gridDimensions = isMobile ? [3, 4] : [4, 6]; // [rows, cols]

                const tiles = nextSlideEl.querySelectorAll(`${gridSelector} .grid-tile`);
                const content = nextSlideEl.querySelector('.slide-content');

                // Hide current slide if not init
                if (direction !== 'init' && currentSlideEl) {
                    gsap.to(currentSlideEl.querySelector('.slide-content'), {
                        opacity: 0,
                        duration: 0.3,
                    });
                }

                // Update current slide
                this.currentSlide = slideIndex;

                // Set initial state for tiles based on direction
                const getInitialState = () => {
                    if (direction === 'init') {
                        return { opacity: 0, x: 0, scaleY: 0, transformOrigin: 'top' };
                    }
                    return { opacity: 0, x: direction === 'next' ? 100 : -100 };
                };

                gsap.set(tiles, getInitialState());
                gsap.set(content, { opacity: 0, y: 30 });

                // Animate tiles in with stagger - Grid Slice Reveal Effect
                const timeline = gsap.timeline({
                    onComplete: () => {
                        this.isAnimating = false;
                    }
                });

                if (direction === 'init') {
                    // Initial load: diagonal from bottom-right to top-left
                    timeline.to(tiles, {
                        scaleY: 1,
                        opacity: 1,
                        duration: 0.8,
                        ease: 'power3.inOut',
                        stagger: {
                            amount: 1.0,
                            from: 'end', // Start from bottom-right (end of grid)
                            grid: gridDimensions,
                            each: 0.04 // Smooth diagonal flow
                        }
                    });
                } else {
                    // Slide transitions: diagonal from bottom-right to top-left
                    timeline.to(tiles, {
                        x: 0,
                        opacity: 1,
                        duration: 0.7,
                        ease: 'power2.out',
                        stagger: {
                            amount: 0.8,
                            from: 'end', // Always from bottom-right to top-left
                            grid: gridDimensions,
                            each: 0.035 // Smooth diagonal flow
                        }
                    });
                }

                // Animate content in
                timeline.to(content, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power2.out'
                }, '-=0.3');
            },

            nextSlide() {
                const next = (this.currentSlide + 1) % this.totalSlides;
                this.showSlide(next, 'next');
                this.resetAutoplay();
            },

            previousSlide() {
                const prev = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                this.showSlide(prev, 'prev');
                this.resetAutoplay();
            },

            goToSlide(index) {
                if (index === this.currentSlide) return;
                const direction = index > this.currentSlide ? 'next' : 'prev';
                this.showSlide(index, direction);
                this.resetAutoplay();
            },

            startAutoplay() {
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, 6000);
            },

            resetAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                    this.startAutoplay();
                }
            }
        }));
    });
    </script>

    <style>
    /* Fullscreen Slider Styles - Dynamically Adjusted for Header */
    .fullscreen-slider {
        position: relative;
        width: 100%;
        height: 100vh; /* Default, will be overridden by JavaScript */
        min-height: 500px;
        overflow: hidden;
        background: #000;
    }

    .slides-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        pointer-events: none;
        z-index: 1;
    }

    .slide.active {
        opacity: 1;
        pointer-events: auto;
        z-index: 2;
    }

    /* Grid Container */
    .grid-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: grid;
        gap: 0;
    }

    /* Desktop Grid - 6 columns x 4 rows */
    .grid-desktop {
        grid-template-columns: repeat(6, 1fr);
        grid-template-rows: repeat(4, 1fr);
    }

    /* Mobile Grid - 4 columns x 3 rows */
    .grid-mobile {
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(3, 1fr);
        display: none; /* Hidden by default on desktop */
    }

    /* Show/hide grids based on viewport */
    @media (max-width: 768px) {
        .grid-desktop {
            display: none;
        }

        .grid-mobile {
            display: grid;
        }
    }

    .grid-tile {
        width: 100%;
        height: 100%;
        background-repeat: no-repeat;
        will-change: transform, opacity;
        backface-visibility: hidden;
        overflow: hidden;
    }

    /* Content Overlay */
    .slide-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        background: linear-gradient(135deg, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.5) 100%);
        padding: 2rem;
    }

    .slide-content .content-inner {
        max-width: 900px;
        text-align: center;
        color: #fff;
        background: rgba(0,0,0,0.4);
        padding: 3rem 2rem;
        border-radius: 20px;
    }

    .slide-content .heading .title {
        font-size: clamp(2.5rem, 6vw, 5rem);
        font-weight: 800;
        margin-bottom: 2rem;
        line-height: 1.1;
        text-shadow: 3px 3px 8px rgba(0,0,0,0.8), 0 0 20px rgba(0,0,0,0.5);
        color: #ffffff;
        letter-spacing: -0.02em;
    }

    .slide-content .animated-text {
        display: inline-block;
        color: #ffd700;
        font-weight: 900;
        text-shadow:
            2px 2px 0 rgba(0,0,0,0.3),
            4px 4px 8px rgba(0,0,0,0.5),
            0 0 15px rgba(255,215,0,0.3);
    }

    .slide-content .text-item {
        display: inline;
        opacity: 1;
        margin: 0 0.5rem;
    }

    .slide-content .description {
        font-size: clamp(1.125rem, 2.5vw, 1.5rem);
        margin-bottom: 2.5rem;
        line-height: 1.7;
        text-shadow: 2px 2px 6px rgba(0,0,0,0.8), 0 0 15px rgba(0,0,0,0.5);
        color: #f5f5f5;
        font-weight: 400;
    }

    .slide-content .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1.25rem 2.5rem;
        background: #fff;
        color: #000;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.125rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        border: 2px solid transparent;
    }

    .slide-content .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        background: #000;
        color: #fff;
        border-color: #fff;
    }

    /* Navigation Controls */
    .slider-controls {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 20;
        pointer-events: none; /* Allow clicks to pass through to content */
    }

    .nav-btn {
        position: absolute;
        top: 50%; /* Vertically center within slider */
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
        color: #fff;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.5rem;
        z-index: 30;
        pointer-events: all; /* Re-enable clicks on buttons */
    }

    .nav-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-50%) scale(1.1);
    }

    .prev-btn {
        left: 2rem;
    }

    .next-btn {
        right: 2rem;
    }

    /* Rotate arrow for left button */
    .arrow-left {
        transform: rotate(180deg);
        display: inline-block;
    }

    /* Pagination Dots */
    .pagination-dots {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: center;
        gap: 1rem;
        pointer-events: all;
    }

    .pagination-dots .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.4);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
    }

    .pagination-dots .dot:hover {
        background: rgba(255,255,255,0.6);
        transform: scale(1.2);
    }

    .pagination-dots .dot.active {
        background: #fff;
        width: 40px;
        border-radius: 6px;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .fullscreen-slider {
            min-height: 400px;
        }

        .slide-content {
            padding: 1rem;
            background: linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.6) 100%);
        }

        .slide-content .content-inner {
            padding: 2rem 1.5rem;
            background: rgba(0,0,0,0.4);
        }

        .slide-content .heading .title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.9);
        }

        .slide-content .description {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.9);
        }

        .slide-content .cta-button {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        .nav-btn {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .prev-btn {
            left: 1rem;
        }

        .next-btn {
            right: 1rem;
        }
    }
    </style>
</div>
