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
                    'title' => __('home.hero.title'),
                    'animated_text' => [__('home.hero.title_animated_1'), __('home.hero.title_animated_2')],
                    'description' => __('home.hero.description'),
                    'cta_text' => __('home.hero.cta_button'),
                    'cta_link' => 'https://iskenderpehlivan.janeapp.com/#staff_member/1'
                ],
                [
                    'image' => asset('images/slide/slide2.jpeg'),
                    'title' => __('home.about.title'),
                    'animated_text' => [__('home.about.subtitle')],
                    'description' => __('home.about.description'),
                    'cta_text' => __('home.about.cta_button'),
                    'cta_link' => '#about'
                ],
                [
                    'image' => asset('images/slide/slide3.jpeg'),
                    'title' => __('home.services.title'),
                    'animated_text' => [__('home.services.subtitle')],
                    'description' => __('home.services.description'),
                    'cta_text' => __('home.services.read_more'),
                    'cta_link' => '#services'
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
                                                <span class="text-item" :class="{ 'visible': animatedTextIndex === {{ $textIndex }} }">{{ $text }}</span>
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

                    <!-- Grid Tiles -->
                    <div class="grid-container" data-slide-index="{{ $index }}">
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
            animatedTextIndex: 0,
            animatedTextInterval: null,

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

                    // Start animated text rotation
                    this.startAnimatedText();
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

                const tiles = nextSlideEl.querySelectorAll('.grid-tile');
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
                            grid: [4, 6],
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
                            grid: [4, 6],
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
            },

            startAnimatedText() {
                this.animatedTextInterval = setInterval(() => {
                    this.animatedTextIndex = (this.animatedTextIndex + 1) % 2;
                }, 3000);
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
        grid-template-columns: repeat(6, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 0;
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
        background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 100%);
        padding: 2rem;
    }

    .slide-content .content-inner {
        max-width: 800px;
        text-align: center;
        color: #fff;
    }

    .slide-content .heading .title {
        font-size: clamp(2rem, 5vw, 4rem);
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .slide-content .animated-text {
        display: inline-block;
        position: relative;
        min-width: 200px;
    }

    .slide-content .text-item {
        position: absolute;
        left: 0;
        right: 0;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s ease;
    }

    .slide-content .text-item.visible {
        position: relative;
        opacity: 1;
        transform: translateY(0);
    }

    .slide-content .description {
        font-size: clamp(1rem, 2vw, 1.25rem);
        margin-bottom: 2rem;
        line-height: 1.6;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .slide-content .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: #fff;
        color: #000;
        text-decoration: none;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .slide-content .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
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
        }

        .slide-content .heading .title {
            font-size: 2rem;
        }

        .slide-content .description {
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

        .grid-container {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(3, 1fr);
        }
    }
    </style>
</div>
