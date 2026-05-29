@extends('layouts.app')
@php $_sn = $site['site_name'] ?? 'Gramer Dokan'; $_og = !empty($site['favicon']) ? asset('storage/'.$site['favicon']) : asset('frontend/img/seldom-rounded.png'); @endphp
@section('page_title', $_sn . ' | Premium Quality Clothing in Bangladesh')
@push('meta')
    <meta name="description" content="{{ $_sn }} - আপনার পছন্দের পোশাকের জন্য সেরা গন্তব্য। Shop premium quality sarees, ethnic wear, and fashion clothing at the best price in Bangladesh.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <meta property="og:type"        content="website">
    <meta property="og:title"       content="{{ $_sn }} | Premium Quality Clothing in Bangladesh">
    <meta property="og:description" content="{{ $_sn }} - আপনার পছন্দের পোশাকের জন্য সেরা গন্তব্য। Shop premium quality sarees, ethnic wear, and fashion clothing at the best price in Bangladesh.">
    <meta property="og:image"       content="{{ $_og }}">
    <meta property="og:url"         content="{{ url('/') }}">
    <meta property="og:site_name"   content="{{ $_sn }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $_sn }} | Premium Quality Clothing in Bangladesh">
    <meta name="twitter:description" content="Shop premium quality sarees, ethnic wear, and fashion clothing at the best price in Bangladesh.">
    <meta name="twitter:image"       content="{{ $_og }}">
@endpush

@section('content')

        <!--banner start-->
        @if($slides->count() > 0)
        <section class="hero-slider">
            <div id="heroCarousel" class="carousel slide carousel-fade container" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($slides as $slide)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="d-block w-100 hero-slide">
                            <img src="{{ $slide->getImageUrl() ?? '' }}" alt="{{ $slide->title ?? '' }}">
                        </div>
                    </div>
                    @endforeach
                    {{-- <div class="carousel-item">
                        <div class="d-block w-100 hero-slide"
                            style="background-image: url('{{ asset('frontend/img/banner/main_banner2.jpeg') }}');">
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="d-block w-100 hero-slide"
                            style="background-image: url('{{ asset('frontend/img/banner/main_banner3.jpeg') }}');">
                        </div>
                    </div> --}}
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>
        @endif
        <!--banner end-->
        <!--our Category start-->
        @if($homepage_categories->count() > 0)
        <section class="our_category">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="category_head">
                            <h1>Our Category</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach($homepage_categories as $cat)
                    <div class="col-lg-2 col-6">
                        <div class="category_box">
                            <div class="category_box1">
                                <a href="{{ route('category.show', $cat->slug) }}">
                                    <img src="{{ $cat->getImageUrl() ?? asset('frontend/img/category/default.jpeg') }}"
                                         class="w-100" alt="{{ $cat->name }}">
                                </a>
                            </div>
                            <div class="category_box2">
                                <a href="{{ route('category.show', $cat->slug) }}">
                                    <p>{{ $cat->name }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        <!--our Category end-->
    <section class="sec-style-1 my-3">
        <div class="container">


            <div class="sec-header">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h2 class="sec-title text-primary-color">Latest Products - নতুন পণ্য-</h2>
                    </div>
                    <div class=" text-right">
                        <a href="{{ route('shop') }}" class="sec-title text-primary-color">সব পণ্য দেখুন</a>
                    </div>
                </div>

                <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
            </div>
            <div class="sec-body">
                <div class="sec-grid-box">
                    @foreach ($products->take(6) as $product)
                        <div class="sec-grid-item p-card-1">

                            <div class="p-img-box">
                                <a href="{{ $product->url }}">
                                    <img src="{{ $product->getImageFullUrl() ?? '' }}" alt="">
                                </a>
                            </div>
                            <div class="p-info">
                                <div class="prices">
                                    @if ($product->discount_price > 0)
                                        <del class="old-price">৳ {{ $product->price }}</del>
                                        <span class="price">৳ {{ $product->discount_price }}</span>
                                    @else
                                        <span class="old-price">Price : </span> <span class="price"> ৳
                                            {{ $product->price }}</span>
                                    @endif

                                </div>
                                <a class="visually-hidden" href="{{ $product?->url }}">

                                    <h1 class="p-title">{{ $product->name }}</h1>
                                </a>

                            </div>

                            <div class="p-btn-group d-flex gap-2">
                                <a class="btn btn-primary w-100 d-block" href="{{ $product?->url }}">Buy Now</a>
                                <button type="button" class="btn btn-primary w-100 d-block" x-data
                                    x-on:click="$dispatch('add-to-cart', { productId: {{ $product->id }} })">
                                    Add to Cart
                                </button>
                            </div>


                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('shop') }}" class="btn btn-primary "> See More</a>
                    {{-- {{ $products->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>
        </div>
    </section>
    

  @foreach ($categories->where('is_homepage_show', true) as $category)
        @if ($category->products?->where('status', 1)->count() > 0)
            <section class="sec-style-1 my-3">
                <div class="container">
                    <div class="sec-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow">
                                <h2 class="sec-title text-primary-color">{{ $category->name }}</h2>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('category.show', $category->slug) }}"
                                    class="sec-title text-primary-color">সব পণ্য দেখুন</a>
                            </div>
                        </div>

                        <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
                    </div>
                    <div class="sec-body">
                        <div class="sec-grid-box">
                            @foreach ($category?->products->where('status', 1)->take(6) as $product)
                                <div class="sec-grid-item p-card-1">

                                    <div class="p-img-box">
                                        <a href="{{ $product?->url }}">
                                            <img src="{{ $product->getImageFullUrl() ?? '' }}"
                                                alt="">
                                        </a>
                                    </div>
                                    <div class="p-info">
                                        <div class="prices">
                                            @if ($product->discount_price > 0)
                                                <del class="old-price">৳ {{ $product->price }}</del>
                                                <span class="price">৳ {{ $product->discount_price }}</span>
                                            @else
                                                <span class="old-price">Price : </span> <span class="price"> ৳
                                                    {{ $product->price }}</span>
                                            @endif

                                        </div>
                                        <a class="visually-hidden" href="{{ $product?->url }}">

                                            <h1 class="p-title">{{ $product->name }}</h1>
                                        </a>

                                    </div>
                                   <div class="p-btn-group d-flex  gap-2">
                                <a class="btn btn-primary w-100 d-block" href="{{ $product?->url }}">Buy Now</a>
                                <button type="button" class="btn btn-primary w-100 d-block" x-data
                                    x-on:click="$dispatch('add-to-cart', { productId: {{ $product->id }} })">
                                    Add to Cart
                                </button>
                            </div>


                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('category.show', $category->slug) }}" class="btn btn-primary "> See More -
                                {{ $category->name }}</a>

                            {{-- {{ $products->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    {{-- @if (true) --}}
    {{-- @if (isset($slides) && $slides->count() > 0)
        <section id="reviews">
            <div class="container">
                <div class="sec-header">
                    <h2 class="fs-5 fw-bold bg-primary-color text-center py-3 px-3 text-white">Customer Reviews</h2>
                    <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
                </div>
                <div class="sec-body">

                    <!-- Slider main container -->
                    <div class="swiper mySwiper3">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper">
                            <!-- Slides -->
                            @foreach ($slides as $review)
                                <div class="swiper-slide">
                                    <img src="{{ $review->getImageUrl() ?? '' }}" class="d-block w-100"
                                        alt="...">
                                </div>
                            @endforeach
                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>

                        <!-- If we need navigation buttons -->
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>

                    </div>
                </div>
            </div>
        </section>
    @endif --}}
    <section id="faq" class=" mb-3">
        <div class="container">

            <h1 class="fs-5 fw-bold bg-primary-color text-center py-3 px-3 text-white">সচরাচর জিজ্ঞাস্য প্রশ্নাবলি
            </h1>
            <ul class="list-inline fs-6 fw-medium">
                <li><i class="fa-solid fa-angles-right text-primary-color"></i> সারা বাংলাদেশে ক্যাশ অন ডেলিভারি
                    অ্যাভেইলেবল </li>

                <li><i class="fa-solid fa-angles-right  text-primary-color"></i> আপনি যদি আপনার ক্রয়কৃত পণ্যটি
                    নিয়ে সন্তুষ্ট না হন, তবে শুধু ডেলিভারি চার্জ প্রদান করে ডেলিভারি ম্যানের কাছে সহজেই ফেরত দিতে
                    পারবেন। </li>

                <li><i class="fa-solid fa-angles-right text-primary-color"></i>আমাদের আছে ডেলিভারির পর ৩ দিন
                    পর্যন্ত এক্সচেঞ্জ সুবিধা।
                </li>
            </ul>
        </div>
    </section>
        <!--wholesale program start-->
        <section class="d-block" >
           <img style="width: 100%" src="{{ asset('frontend/img/banner/footer-banner.jpeg') }}" alt="">
        </section>
        <!--wholesale program end-->
        <!--services Section Start-->
        <section class="service">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="service_item">

                            <!--icon-->
                            <div class="service_icon">
                               <i class="fa-solid fa-cart-shopping"></i>
                            </div>

                            <!--text-->
                            <div class="service_text">
                                <h2>Unique Products</h2>
                                <p>Enjoy top quality items for less</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>Online Support</h2>
                                <p>24 hours a day, 7 days a week</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>Free Shipping</h2>
                                <p>Free Shipping for Over 500 taka order</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="service_item">
                            <!--icon-->
                            <div class="service_icon">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <!--text-->
                            <div class="service_text">
                                <h2>secure payment</h2>
                                <p>Enjoy top quality items for less</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--services Section end-->

@endsection
@push('scripts')
    <script>
        var swiper2Review = new Swiper(".mySwiper3", {
            spaceBetween: 10,
            slidesPerView: 3,
            // Optional parameters
            loop: true,

            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
            },

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 2000,
            },
            pauseOnHover: true,
            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
            },

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            // thumbs: {
            //     swiper: swiper,
            // },
        });
    </script>
@endpush
