<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title', $site['site_name'] ?? 'Seldom Fashion')</title>
    @yield('meta_data')
    @stack('meta')
    <!-- Favicon -->
    @if(!empty($site['favicon']))
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $site['favicon']) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/seldom-rounded.png') }}">
    @endif
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS  -->
    <link href="{{ asset('frontend/library/bootstrap/bootstrap.min.css') }}" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Swiperjs css -->
    <link rel="stylesheet" href="{{ asset('frontend/library/swiper/swiper-bundle.min.css') }}">
    <!-- Fancy Box css -->
    <link rel="stylesheet" href="{{ asset('frontend/library/fancybox/fancybox.css') }}">
    {{-- notlify --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <!-- Custom Css  -->
    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('lib/plyr/plyr.css') }}" />

    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            src: url("{{ asset('fonts/SolaimanLipi.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css' . '?v=1.0.4') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/style_new.css' . '?v=1.0.0') }}">
    @livewireStyles
    @stack('styles')
    @php
        $fbclid = request()->get('fbclid') ?? '';
        $fbclid_generated = '';
        $fbclid_generated = App\Helper\MetaHelper::format_new_fbc($fbclid);

    @endphp
    @push('scripts')
        <script>
            var custom_fbc = '{{ $fbclid_generated }}';
            console.log('fbclid_generated', custom_fbc);
            if (custom_fbc) {
                localStorage.setItem('custom_fbc', custom_fbc);
            }
        </script>
    @endpush
    @php
        $sections = View::getSections();
        $segment = $sections['segment'] ?? 'default';
    @endphp

    @if (app()->environment('production'))
        <!-- Production Environment -->

        <!-- TikTok Pixel Code Start -->
        <script>
            ! function(w, d, t) {
                w.TiktokAnalyticsObject = t;
                var ttq = w[t] = w[t] || [];
                ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias",
                    "group", "enableCookie", "disableCookie", "holdConsent", "revokeConsent", "grantConsent"
                ], ttq.setAndDefer = function(t, e) {
                    t[e] = function() {
                        t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
                    }
                };
                for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
                ttq.instance = function(t) {
                    for (
                        var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
                    return e
                }, ttq.load = function(e, n) {
                    var r = "https://analytics.tiktok.com/i18n/pixel/events.js",
                        o = n && n.partner;
                    ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = r, ttq._t = ttq._t || {}, ttq._t[e] = +new Date,
                        ttq._o = ttq._o || {}, ttq._o[e] = n || {};
                    n = document.createElement("script");
                    n.type = "text/javascript", n.async = !0, n.src = r + "?sdkid=" + e + "&lib=" + t;
                    e = document.getElementsByTagName("script")[0];
                    e.parentNode.insertBefore(n, e)
                };


                ttq.load('{{ config('conversionapi.tiktok_pixel_id') }}');
                ttq.page();
            }(window, document, 'ttq');
        </script>
        <!-- TikTok Pixel Code End -->
        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ config('conversionapi.gtm_id') }}');
        </script>
        <!-- End Google Tag Manager -->
    @else
        <!-- Development Environment -->
    @endif
</head>

<body class="bg-white bg-opacity-50">
    @if (app()->environment('production'))
        <!-- Production Environment -->
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('conversionapi.gtm_id') }}" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @else
        <!-- Development Environment -->
    @endif


    <header id="header-area" class="shadow bg-white">
        <div class="container-fluid px-0">
            <div class="header_top">
                <p>আমাদের যে কোন পণ্য অর্ডার করতে কল বা WhatsApp করুন: +8801341-696476</p>
            </div>
        </div>
        <style>
            .navbar .navbar-toggler {
                box-shadow: none !important;
                font-size: 25px;
            }

            .navbar .navbar-toggler:focus {
                border-color: #fff;
            }
        </style>
                <div class="container-fluid bg-white px-0 middle-navbar">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand logo" href="/">
                        @if(!empty($site['header_logo']))
                        <img src="{{ asset('storage/' . $site['header_logo']) }}" class="w-100" alt="{{ $site['site_name'] ?? '' }}">
                        @else
                        <img src="{{ asset('frontend/img/logo/logo.png') }}" class="w-100" alt="">
                        @endif
                    </a>

                    <div class="justify-content-center collapse navbar-collapse nav_search" id="navbarSupportedContent">
                        <form class="d-flex" role="search" action="{{ route('search') }}">
                            <input class="form-control me-2" type="search" placeholder="Search" name="search"
                                aria-label="Search" value="{{ request('search') }}" />
                            <button class="btn btn-outline-light" type="submit">Search</button>
                        </form>
                    </div>

                    <div class="icon_box">
                        {{-- <a href="#"><i class="profile_icon fa-regular fa-circle-user"></i></a> --}}
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight" role="button" class="position-relative">
                            <i class="cart_icon fa-solid fa-cart-arrow-down"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                data-cart-count-badge>0</span>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav_c"
                            aria-controls="nav_c" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>

        <div class="container-fluid px-0">
            <nav class="navbar navbar-expand-lg nav">
                <div class="container">
                    <div class="collapse navbar-collapse menu" id="nav_c">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link  {{ Request::is('/') ? 'active' : '' }}" aria-current="page" href="/" >Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Shop</a>
                            </li>

                            @php
                                $category_menus = \App\Models\Category::where('is_show_in_menu', true)
                                    ->where('is_active', true)
                                    ->orderBy('display_order')
                                    ->get();
                            @endphp

                            @foreach ($category_menus as $menu)
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('category/' . $menu->slug) ? 'active' : '' }}"
                                        href="{{ route('category.show', $menu->slug) }}">{{ $menu->name }}</a>
                                </li>
                            @endforeach

                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Shopping Cart</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
           <div class="offcanvas-body">
                @livewire('website.cart-manager')
            </div>
        </div>

    </header>
    <aside id="sidebar"></aside>
    <main id="Content-body" class="py-3">
        @yield('content')
    </main>
    <footer class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="footer_item">
                        @if(!empty($site['footer_logo']))
                        <img class="w-100" src="{{ asset('storage/' . $site['footer_logo']) }}" alt="{{ $site['site_name'] ?? '' }}">
                        @else
                        <img class="w-100" src="{{ asset('frontend/img/logo/logo.png') }}" alt="">
                        @endif
                        <h2>{{ $site['site_name'] ?? 'Seldom Fashion' }}</h2>
                        @if(!empty($site['footer_description']))
                        <p>{{ $site['footer_description'] }}</p>
                        @endif
                        <a href="https://www.facebook.com/greenleavesbd0" class="social_icon"
                            style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-square-facebook" style="color: rgb(24, 119, 242);"></i>
                        </a>
                        <a href="https://www.youtube.com/@greenleaves172" class="social_icon"
                            style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-youtube" style="color: rgb(255, 0, 0);"></i>
                        </a>
                        <a href="#" class="social_icon" style="text-decoration:none;" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/8801341696476" class="social_icon" style="text-decoration:none;"
                            target="_blank">
                            <i class="fa-brands fa-whatsapp" style="color: rgb(37, 211, 102);"></i>
                        </a>
                    </div>
                </div>
                 <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>গুরুত্বপূর্ণ লিংক</h2>
                        <ul>
                            <li><a href="/">হোম </a></li>
                            <li><a href="{{ route('about') }}">আমাদের সম্পর্কে</a></li>
                            <li><a href="{{ route('contact') }}">যোগাযোগ</a></li>
                            <li><a href="{{ route('shop') }}">শপ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>গুরুত্বপূর্ণ লিংক</h2>
                        <ul>
                            <li><a href="/">হোম</a></li>
                            <li><a href="{{ route('about') }}">আমাদের সম্পর্কে</a></li>
                            <li><a href="{{ route('contact') }}">যোগাযোগ</a></li>
                            <li><a href="{{ route('shop') }}">শপ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer_item footer_item1">
                        <h2>Contact Us</h2>
                        <p><span><i class="fa-solid fa-phone"></i></span> +8801341696476</p>
                        <p><span><i class="fa-solid fa-envelope"></i></span>info@gramerdokan.com.bd</p>
                        <p><span><i class="fa-solid fa-location-dot"></i></span>Dhaka, Bangladesh</p>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer_img">
                        <img src="{{ asset('frontend/img/footer/foot.jpg') }}" class="w-100" alt="Green Leaves">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <footer class="footer">
        <div class="container">
            <div class="footer_bottom">
                <p>©Copyright 2026. All right Reserved Developed By <a target="_blank"
                        href="https://www.facebook.com/alimuzahid.dev/">Ali Muzahid</a></p>
                <p class="footer_p2"></p>
            </div>
        </div>
    </footer>
    <section id="sticky_components" class="sticky_components">
        <a href="https://api.whatsapp.com/send?phone=+8801341696476&text=আমার%20কিছু%20প্রশ্ন%20ছিল"
            target="_blank" class="whatsapp_api_btn">
            <img src="https://favouriterange.com/asset/img/icons/whatsapp.png" alt="">
        </a>
    </section>

    <!-- Jquery -->
    <script src="{{ asset('frontend/library/jquery/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Js  -->
    <script src="{{ asset('frontend/library/bootstrap/bootstrap.bundle.min.js') }}"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    <!-- Swiperjs Js  -->
    <script src="{{ asset('frontend/library/swiper/swiper-bundle.min.js') }}"></script>
    <!-- Fancybox js -->
    <script src="{{ asset('frontend/library/fancybox/fancybox.umd.js') }}"></script>
    <!-- Initialize player -->
    <script src="{{ asset('lib/plyr/plyr.polyfilled.js') }}"></script>
    {{-- notlify --}}
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <!-- Custom Js  -->

    <script src="{{ asset('frontend/js/script.js') }}"></script>

    <script>
        window.appNotyf = new Notyf({
            duration: 3200,
            position: {
                x: 'right',
                y: 'top'
            }
        });
    </script>
    <!-- Initialize Swiper -->
    <script>
        //product page
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            autoplay: {
                delay: 2000,
            },
            pauseOnHover: true,

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>

    <script>
        Fancybox.bind("[data-fancybox]", {
            // Optional settings
            Thumbs: {
                autoStart: true,
            },
        });
    </script>

    <script>
        $(document).ready(function() {
            var screen_size = window.screen.width + 'x' + window.screen.height;
            // console.log('screen_size:', screen_size);
            var user_data = document.cookie.split(';').filter(item => item.trim().startsWith('_sfud='))[0]?.split(
                '=')[1];
            user_data = user_data ? JSON.parse(decodeURIComponent(user_data)) : null;
            var device_id = document.cookie.split(';').filter(item => item.trim().startsWith('_sfdid='))[0]?.split(
                '=')[1];
            // console.log('user_data', user_data);
            // console.log('device_id', device_id);

            function getFormData() {
                return {
                    phone: $("input[name='phone']").val() ?? null,
                    name: $("input[name='name']").val() ?? null,
                    address: $("input[name='address']").val() ?? null
                };
            }
            $(window).on('beforeunload', function() {
                // You can call registerDevice here if needed on unload
                registerDevice();
            });

            function registerDevice() {
                var formdata = getFormData();
                console.log('Form Data:', formdata);

                fetch('{{ route('device.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            device_type: navigator.userAgent,
                            device_model: navigator.platform,
                            screen_size: screen_size,
                            phone: formdata.phone || null,
                            name: formdata.name || null,
                            address: formdata.address || null
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Device registered:', data);
                    })
                    .catch(error => {
                        console.error('Error registering device:', error);
                    });

            }

            registerDevice();
        });
    </script>

    <script>
        var segment = "{{ $segment ?? '' }}";
        let current_url = "{{ url()->current() }}";
        console.log('current_url:', current_url);
        let referrer_url = "{{ url()->previous() }}";
        console.log('referrer:', referrer_url);
        console.log('Segment:', segment);
        let event_id = new Date().getTime() + '_' + Math.random().toString(36).substring(2, 9);

        console.log("Event_ID: " + event_id);
        var user_data = document.cookie.split(';').filter(item => item.trim().startsWith('_sfud='))[0]?.split(
            '=')[1];
        user_data = user_data ? JSON.parse(decodeURIComponent(user_data)) : null;
        var device_id = document.cookie.split(';').filter(item => item.trim().startsWith('_sfdid='))[0]?.split(
            '=')[1];
        console.log('user_data', user_data);
        console.log('device_id', device_id);

        var ip_address = '{{ request()->ip() }}';
        console.log('IP Address:', ip_address);
        var userAgent = navigator.userAgent;
        console.log('User Agent:', userAgent);
        var token = "{{ csrf_token() }}";
        console.log('dom ready');
        var pageViewDataSet = {
            XSRF_TOKEN: token,
            event: 'PageView',
            event_id: event_id,
            event_source_url: current_url,
            referrer_url: referrer_url,
            ecommerce: null,

            user_data: {
                first_name: user_data?.first_name ?? null,
                last_name: user_data?.last_name ?? null,
                // email_address: null,
                phone_number: user_data?.phone ?? null,
                street: user_data?.street ?? null,
                country: "BD",
                state: user_data?.state ?? null,
                city: user_data?.city ?? null,
                // region: null,
                zipcode: user_data?.zip_code ?? null,
                customer_id: user_data?.id ||
                    device_id || null,
                // new_customer: 'true' // এটি অনুমান করা কঠিন হবে
                client_ip_address: ip_address || null,
                client_user_agent: userAgent || null,
                fbc: "{{ $fbclid_generated ?? null }}",
            }
        }

        dataLayer = window.dataLayer || [];
        // dataLayer.push(pageViewDataSet);

            @php
                $pageViewEvent = new \App\CAPI\PageViewEvent();
                $pageViewServerPayload = $pageViewEvent->serverPayload();
                $pageViewPayload = $pageViewEvent->browserEventPayload();
                \App\Jobs\SendMetaCapiEventJob::dispatch($pageViewServerPayload)->onQueue(env('META_CAPI_QUEUE', 'metacapi'));
            @endphp


                const page_view_browser = @json($pageViewPayload);

            if (page_view_browser) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(page_view_browser);
            }
            // fetch("/fb-pixel-capi?event_name=page_view&segment=" + segment, {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': token
            //         },
            //         body: JSON.stringify(pageViewDataSet)
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         console.log('Facebook Pixel PageView tracked:', data);
            //     })
            //     .catch(error => {
            //         console.error('Error tracking Facebook Pixel PageView:', error);
            //     });

    </script>
    @if (session('cart_error'))
        <script>
            window.appNotyf.error(@json(session('cart_error')));
        </script>
    @endif

    @if (session('checkout_success'))
        <script>
            window.appNotyf.success(@json(session('checkout_success')));
        </script>
    @endif
    <script>
        function setCartCount(count) {
            $('.cart_count').each(function() {
                $(this).text(count);
            })
        }

        $(document).ready(function() {
            //get cart count from cookie
            var cart_count = parseInt(document.cookie.replace(/(?:(?:^|.*;\s*)_cart_count\s*\=\s*([^;]*).*$)|^.*$/,
                "$1"));
            if (isNaN(cart_count)) {
                cart_count = 0;
            }
            setCartCount(cart_count);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                if (event.type === 'success') {
                    window.appNotyf.success(event.message)
                } else if (event.type === 'error') {
                    window.appNotyf.error(event.message)
                }
            })
        })
        document.addEventListener('livewire:init', () => {
            Livewire.on('cart-updated', (event) => {

                if (event.cartcount > 0) {
                    setCartCount(event.cartcount);
                } else {
                    setCartCount(0);
                }
            })
        })
    </script>
    @livewireScripts
    @stack('scripts')

    {{-- Fixed floating cart button --}}
    <style>
        .sf-float-cart {
            position: fixed;
            bottom: 72px;
            right: 12px;
            z-index: 1050;
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 13px 22px 13px 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,.28), 0 2px 8px rgba(0,0,0,.18);
            cursor: pointer;
            transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s ease, opacity .3s ease;
            text-decoration: none;
            opacity: 0;
            pointer-events: none;
            transform: translateY(12px) scale(.95);
        }
        .sf-float-cart.sf-cart-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .sf-float-cart:hover {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 16px 40px rgba(0,0,0,.32), 0 4px 12px rgba(0,0,0,.2);
            color: #fff;
        }
        .sf-float-cart:active {
            transform: scale(.97);
        }
        .sf-float-cart__icon {
            font-size: 20px;
            line-height: 1;
        }
        .sf-float-cart__label {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .3px;
            line-height: 1;
        }
        .sf-float-cart__badge {
            background: #e63946;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            line-height: 1;
            box-shadow: 0 2px 6px rgba(230,57,70,.5);
            transition: transform .2s cubic-bezier(.34,1.56,.64,1);
        }
        .sf-float-cart.sf-cart-bump .sf-float-cart__badge {
            transform: scale(1.45);
        }
        @media (max-width: 576px) {
            .sf-float-cart {
                bottom: 72px;
                right: 8px;
                padding: 11px 16px 11px 14px;
            }
            .sf-float-cart__label { display: none; }
        }
    </style>

    <button type="button"
            class="sf-float-cart"
            id="sf-float-cart-btn"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasRight"
            aria-controls="offcanvasRight"
            aria-label="Open cart">
        <span class="sf-float-cart__icon"><i class="fa-solid fa-bag-shopping"></i></span>
        <span class="sf-float-cart__label">Cart</span>
        <span class="sf-float-cart__badge cart_count">0</span>
    </button>

    <script>
        (function () {
            var btn   = document.getElementById('sf-float-cart-btn');
            var badge = btn.querySelector('.sf-float-cart__badge');

            function syncVisibility() {
                var count = parseInt(badge.textContent) || 0;
                btn.classList.toggle('sf-cart-visible', count > 0);
            }

            // Watch for DOM changes to the badge (setCartCount updates text)
            new MutationObserver(function () {
                syncVisibility();
                // Bump animation
                badge.classList.remove('sf-cart-bump');
                void badge.offsetWidth; // reflow
                badge.classList.add('sf-cart-bump');
                setTimeout(function () { badge.classList.remove('sf-cart-bump'); }, 300);
            }).observe(badge, { childList: true, characterData: true, subtree: true });

            // Initial sync after cart count is read from cookie
            setTimeout(syncVisibility, 100);
        })();
    </script>

</body>

</html>
