<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title', $site['site_name'] ?? 'Gramer Dokan')</title>
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
                <p>আমাদের যে কোন পণ্য অর্ডার করতে কল বা WhatsApp করুন: +8801893-620392</p>
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
        <nav class="navbar navbar-expand-lg bg-primary-color  border-top">

            <div class="container">
                <a class="navbar-brand" href="/">
                    @if(!empty($site['header_logo']))
                    <img src="{{ asset('storage/' . $site['header_logo']) }}" alt="{{ $site['site_name'] ?? '' }}" style="width:50px;">
                    @else
                    <img src="{{ asset('frontend/img/logo-transparent.png') }}" alt="" style="width:50px;">
                    @endif
                    {{ $site['site_name'] ?? 'Seldom Fashion' }}</a>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                        aria-controls="offcanvasRight" role="button"
                        class="text-white position-relative navbar-toggler">
                        <i class="cart_icon fa-solid fa-cart-arrow-down"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart_count"
                            data-cart-count-badge>0</span>
                    </button>
                    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars " style=""></i>
                    </button>
                </div>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav ">
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" aria-current="page"
                                href="/">Home</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('shop') ? 'active' : '' }}" href="/shop">All
                                Products</a>
                        </li>
                        @php
                            $category_menus = \App\Models\Category::where('is_show_in_menu', true)
                                ->where('is_active', true)
                                ->orderBy('display_order')
                                ->get();
                        @endphp
                        @foreach ($category_menus as $menu)
                            <li class="nav-item fs-5">
                                <a class="nav-link"
                                    href="{{ route('category.show', $menu->slug) }}">{{ $menu->name }}</a>
                            </li>
                        @endforeach


                        {{-- <li class="nav-item dropdown fs-5">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Collections
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item fs-5" href="#">Summer Collection</a></li>
                                <li><a class="dropdown-item fs-5" href="#">Premium Collection</a></li>
                                <li><a class="dropdown-item fs-5" href="#">Party Waear</a></li>
                            </ul>
                        </li> --}}


                        {{-- <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('category/combo-offer') ? 'active' : '' }}"
                                href="/category/combo-offer">Combo Offer</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('men') ? 'active' : '' }}" href="/category/men">Men</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('women') ? 'active' : '' }}"
                                href="/category/women">Women</a>
                        </li> --}}

                    </ul>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight" role="button" class="position-relative desktop-cart-btn">
                            <i class="cart_icon fa-solid fa-cart-arrow-down"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart_count"
                                data-cart-count-badge>0</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
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
        {{ $slot }}
    </main>
    <footer id="footer-area" class="border-top">
        <div class="container py-3">
            <h5 class="text-center fw-semibold text-primary-color"> যেকোনো তথ্যের জন্য আমাদের মেসেজ করুন অথবা কল করুন।
            </h5>
            <div class="topbar d-flex justify-content-center">
                <ul class="quick-contact list-inline d-flex justify-content-end gap-3 py-2 mb-0 align-items-center ">
                    <li class="list-inline fw-bold fs-6 "><a href="https://wa.me/8801622351266" target="_blank"
                            class="text-decoration-none text-primary-color text-primary-hover"><i
                                class="fa-brands fa-whatsapp"></i> WhatsApp </a></li>
                    <li class="list-inline fw-bold fs-6"><a href="https://m.me/seldombd" target="_blank"
                            class="text-decoration-none text-primary-color text-primary-hover"> <i
                                class="fa-brands fa-facebook-messenger"></i></i> Messagenger </a></li>
                    <li class="list-inline fw-bold fs-6"><a href="tel:+8801622-351266"
                            class="text-decoration-none text-primary-color text-primary-hover"> <i
                                class="fa-solid fa-phone"></i> Call Us +88 01622-351266</a></li>
                </ul>
            </div>
        </div>
        <section id="sticky_components" class="sticky_components">
            <a href="https://api.whatsapp.com/send?phone=+8801622351266&text=আমার%20কিছু%20প্রশ্ন%20ছিল"
                target="_blank" class="whatsapp_api_btn">
                <img src="https://favouriterange.com/asset/img/icons/whatsapp.png" alt="">
            </a>
        </section>
    </footer>

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
        dataLayer.push(pageViewDataSet);
        @if (session('debugmode'))
            @php
                $pageViewEvent = new \App\CAPI\PageViewEvent();
                $pageViewPayload = $pageViewEvent->browserEventPayload();

            @endphp


            const page_view_server = @json($pageViewPayload);

            if (page_view_server) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(page_view_server);
            }
            fetch("/fb-pixel-capi?event_name=page_view&segment=" + segment, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(pageViewDataSet)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Facebook Pixel PageView tracked:', data);
                })
                .catch(error => {
                    console.error('Error tracking Facebook Pixel PageView:', error);
                });
        @endif
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

</body>

</html>
