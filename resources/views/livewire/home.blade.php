@volt
<?php

use function Livewire\Volt\{computed};

$blogPosts = computed(function () {
    return \App\Models\Post::where('type', 'blog')
        ->where('status', 'published')
        ->with('translations')
        ->latest()
        ->limit(3)
        ->get();
});

$services = computed(function () {
    return \App\Models\Service::with('translations')
        ->orderBy('sort_order')
        ->get();
});

// Fetch settings directly since it's static, non-reactive data
$settings = \App\Models\Setting::with('translations')->first();
?>

<div>
    <!-- Hero Section -->
    <div class="page-title-homepage-2">
        <div class="content-inner">
            <div class="heading">
                <h2 class="title animationtext rotate-1">Sağlıklı ve Dengeli Bir Yaşama Doğru İlk Adımı Atın
                    <span class="tf-text s1 cd-words-wrapper">
<span class="item-text is-visible">Huzurlu</span>
<span class="item-text is-hidden">Mutlu Bir Hayat</span>
</span>
                </h2>
                <p class="description">
                    Kendinizde barış bulabileceğiniz güvenli bir alan sağlıyoruz. Uzman danışmanlarımız sizi kişiselleştirilmiş bakım ile ruh sağlığı zorlukları yaşamınızda üstesinden gelmenize rehberlik eder.
                </p>
            </div>
            <a class="tf-btn style-default btn-color-secondary pd-28" href="#contact-section">
                <span>Danışmanlık Randevusu Al <i class="icon-ArrowRight arr-1"></i></span>
            </a>
        </div>
        <div class="image-wrap">
            <img class="lazyload" data-src="{{asset('images/firstphoto.png')}}" src="{{asset('images/firstphoto.png')}}" alt="Danışmanlık">
        </div>
    </div>

    <div class="main-content home-page-2">
        <!-- About Section -->
        <div class="section-box-about page-home-2">
            <div class="tf-container">
                <div class="wrap-box-about">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="image-wrap wow fadeInLeft effec-overlay" data-wow-duration="1000" data-wow-delay="0s">
                                <img class="lazyload" data-src="{{asset('images/ekip.jpeg')}}" src="{{asset('images/ekip.jpeg')}}" alt="Hakkımızda">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box-about">
                                <div class="icon wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <img src="/assets/images/item/favicon.png" alt="Logo">
                                </div>
                                <div class="heading-section text-start wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Hakkımızda</p>
                                    <h3>Ruh Sağlığınızla Bağlı Güvenilir Uzmanlar</h3>
                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Deneyimli terapistler ekibimiz ve ruh sağlığına kişiselleştirilmiş yaklaşım ile İskender Pehlivan Danışmanlık, hayatın zorlukları üstesinden gelmeniz ve ruh sağlığı iyiliğini sağlamak için gereken araçlar ve desteği
                                        sunuyoruz. Her bireyin dengeli yaşama, iyileşme ve gelişme hakkına inanıyoruz.
                                    </p>
                                </div>
                                <a class="tf-btn style-default btn-color-white has-boder pd-26 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s" href="#about">
                                    <span>Hakkımızda Daha Fazla Bilgi</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="section-benefits page-home-2 tf-spacing-1">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Avantajlar</p>
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Neden İskender Pehlivan Danışmanlık?</h3>
                            <p class="description text-1 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                Kanıtlanmış sonuçlar ve etkili terapi, sizin benzersiz ihtiyaçlarınızı karşılamak için tasarlanmıştır.
                            </p>
                        </div>
                        <div class="grid-layout-3 gap-30">
                            <div class="icons-box effec-icon wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                <div class="heading">
                                    <h5><a href="#">Kişiselleştirilmiş<br>Bakım</a></h5>
                                    <div class="icon">
                                        <i class="icon-HandHeart"></i>
                                    </div>
                                </div>
                                <p>Sizin benzersiz ihtiyaçlarınız ve hedefleriniz için özelleştirilmiş tedavi planları oluşturuyoruz ve iyileşme yolculuğunuza kişisel olarak uygun bir yaklaşım sağlıyoruz.</p>
                            </div>
                            <div class="icons-box effec-icon wow fadeInUp" data-wow-duration="1000" data-wow-delay="0.1s">
                                <div class="heading">
                                    <h5><a href="#">Deneyimli<br>Uzmanlar</a></h5>
                                    <div class="icon">
                                        <i class="icon-SketchLogo"></i>
                                    </div>
                                </div>
                                <p>Terapistlerimiz geniş eğitim, çeşitli uzmanlık ve etkili stratejiler sunar, sizin ihtiyaçlarınıza uygun yüksek kaliteli bakım ve sonuçlar sağlıyoruz.</p>
                            </div>
                            <div class="icons-box effec-icon wow fadeInUp" data-wow-duration="1000" data-wow-delay="0.2s">
                                <div class="heading">
                                    <h5><a href="#">Destekleyici<br>Ortam</a></h5>
                                    <div class="icon">
                                        <i class="icon-Lifebuoy"></i>
                                    </div>
                                </div>
                                <p>Iyileşme süreciniz boyunca rahat ve desteklenen hissedeceğiniz güvenli, şefkatli bir alan sunuyoruz ve olumlu, kalıcı değişim yaratırız.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <section class="section-service page-home-2 tf-spacing-1">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Neler Sunuyoruz</p>
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Danışmanlık Hizmetlerimiz</h3>
                            <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Bireyler, çiftler ve aileler için kişiselleştirilmiş danışmanlık seçenekleri sunarak, hayatın zorlukları üstesinden gelmenize ve ruh sağlığı iyiliğini sağlamanıza yardımcı oluyoruz.
                            </p>
                        </div>
                        <div class="widget-tabs">
                            <ul class="widget-menu-tab overflow-x-auto">
                                @foreach($services as $index => $service)
                                    <li class="item-title @if($index === 0) active @endif" wire:key="service-tab-{{ $service->id }}">
                                        {{ $service->translation()?->name ?? 'Hizmet' }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="widget-content-tab">
                                @foreach($services as $index => $service)
                                    <div class="widget-content-inner @if($index === 0) active @endif" wire:key="service-content-{{ $service->id }}">
                                        <div class="box-service">
                                            <div class="image-wrap wow fadeInLeft effec-overlay" data-wow-duration="1000" data-wow-delay="0s">
                                                <img class="lazyload" data-src="/assets/images/section/section-service.jpg" src="/assets/images/section/section-service.jpg" alt="{{ $service->translation()?->name ?? 'Hizmet' }}">
                                            </div>
                                            <div class="content">
                                                <div class="heading-section text-start">
                                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">{{ $service->translation()?->name ?? 'Hizmet' }}</p>
                                                    <h4 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                        <a href="#service-{{ $service->slug_base }}">
                                                            {{ $service->translation()?->name ?? 'Hizmet' }}
                                                        </a>
                                                    </h4>
                                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                        {{ Str::limit($service->translation()?->description ?? '', 300) }}
                                                    </p>
                                                </div>
                                                <a href="#service-{{ $service->slug_base }}" class="tf-btn-link z-5 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                                    <span data-text="Daha Fazla Oku">Daha Fazla Oku</span>
                                                    <i class="icon-ArrowRight"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section class="section-news home-page-2 style-pagination tf-spacing-1">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="heading-section">
                            <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Popüler Konular</p>
                            <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Blog & Kaynaklar</h3>
                            <p class="description text-1 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                Ruh sağlığı hakkında bilgi, araçlar ve tavsiyelerin kaynağı.
                            </p>
                        </div>
                        <div class="swiper-container slider-layout-3">
                            <div class="swiper-wrapper">
                                @foreach($blogPosts as $post)
                                    <div class="swiper-slide" wire:key="blog-post-{{ $post->id }}">
                                        <div class="article-blog-item hover-img style-2 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                            <div class="image-wrap">
                                                <a href="#blog-{{ $post->slug_base }}">
                                                    <img class="lazyload" data-src="/assets/images/section/resources-2-1.jpg" src="/assets/images/section/resources-2-1.jpg" alt="{{ $post->translation()?->title ?? 'Blog Yazısı' }}">
                                                </a>
                                                <div class="date-time">
                                                    <div class="content">
                                                        <p class="entry-day">{{ $post->created_at->day }}</p>
                                                        <p class="entry-month fw-book">{{ strtoupper($post->created_at->format('M')) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="content">
                                                <p class="sub"><a href="#">Blog</a></p>
                                                <h5 class="title"><a href="#blog-{{ $post->slug_base }}">
                                                        {{ $post->translation()?->title ?? 'Blog Yazısı' }}
                                                    </a></h5>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper-pagination pagination-layout"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="section-contact home-page-2 bg-1 tf-spacing-1" id="contact-section">
            <div class="tf-container">
                <div class="row">
                    <div class="col-12">
                        <div class="wrap-content">
                            <div class="box-contact">
                                <div class="heading-section text-start">
                                    <p class="text-2 sub wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                        Danışmanlık Al</p>
                                    <h3 class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Ücretsiz Danışmanlık - İyileşme Yolculuğunuzu Başlatın</h3>
                                    <p class="description text-1 lh-30 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">Bugün bağlantı kurun ve sağlıklı, daha mutlu bir yaşam yolculuğunun ilk adımını atın.
                                    </p>
                                </div>
                                <ul class="list-info">
                                    @if($settings)
                                        @if($settings->contact_email)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-Envelope"></i> <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></li>
                                        @endif
                                        @if($settings->contact_phone)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-PhoneCall"></i>{{ $settings->contact_phone }}</li>
                                        @endif
                                        @if($settings->contact_address)
                                            <li class="wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s"><i class="icon-MapPin"></i>{{ $settings->contact_address }}</li>
                                        @endif
                                    @endif
                                </ul>
                                <a href="#contact-section" class="tf-btn-link z-5 wow fadeInUp" data-wow-duration="1000" data-wow-delay="0s">
                                    <span data-text="İletişim">İletişim</span>
                                    <i class="icon-ArrowRight"></i>
                                </a>
                            </div>
                            <form class="form-consultation wow fadeInRight" data-wow-duration="1000" data-wow-delay="0s" method="post" id="contactform" action="{{ route('api.contact.store') }}">
                                @csrf
                                <h4 class="mb-20 text-center">Ücretsiz Danışmanlık Alın</h4>
                                <fieldset class="name">
                                    <input type="text" name="name" class="tf-input style-1" placeholder="Adınız*" tabindex="2" aria-required="true" required>
                                </fieldset>
                                <fieldset class="phone">
                                    <input type="email" name="email" class="tf-input style-1" placeholder="E-posta Adresiniz*" tabindex="2" aria-required="true" required>
                                </fieldset>
                                <fieldset class="message">
                                    <textarea id="message" class="tf-input" name="message" rows="4" placeholder="Mesajınız" tabindex="4" aria-required="true" required></textarea>
                                </fieldset>
                                <button class="tf-btn style-default btn-color-secondary pd-40 boder-8 send-wrap" type="submit">
                                    <span>Gönder</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endvolt
