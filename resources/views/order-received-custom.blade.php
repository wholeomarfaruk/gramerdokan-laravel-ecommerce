<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site['site_name'] ?? 'Gramer Dokan' }}</title>
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
    <!-- Custom Css  -->
    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            src: url("{{ asset('fonts/SolaimanLipi.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css' . '?v=1.0.2') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        @media (max-width: 992px) {
            .order-info-box {

                padding: 20px 20px;
            }
        }
    </style>
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
        $segment = $segment ?? 'default';
    @endphp

    @if ($segment == 'men')
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
            })(window, document, 'script', 'dataLayer', 'GTM-5XWHPZQH');
        </script>
        <!-- End Google Tag Manager -->
    @elseif($segment == 'women')
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
            })(window, document, 'script', 'dataLayer', 'GTM-KXF3ML6X');
        </script>
        <!-- End Google Tag Manager -->
    @else
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
            })(window, document, 'script', 'dataLayer', 'GTM-5XWHPZQH');
        </script>
        <!-- End Google Tag Manager -->
    @endif
</head>

<body class="bg-white bg-opacity-50">
    @if ($segment == 'men')
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5XWHPZQH" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @elseif($segment == 'women')
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KXF3ML6X" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @else
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5XWHPZQH" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif
    <header id="header-area" class="shadow bg-white">
        <div class="container">
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
                                class="fa-solid fa-phone"></i>Call Us +88 01622-351266</a></li>
                </ul>
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
                    {{ $site['site_name'] ?? 'Gramer Dokan' }}</a>
                <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars" style=""></i>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav ">
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" aria-current="page"
                                href="/">Home</a>
                        </li>


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

                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('shop') ? 'active' : '' }}" href="/shop">All
                                Products</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('category/combo-offer') ? 'active' : '' }}"
                                href="/category/combo-offer">Combo Offer</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('men') ? 'active' : '' }}" href="/category/men">Men</a>
                        </li>
                        <li class="nav-item fs-5">
                            <a class="nav-link {{ Request::is('women') ? 'active' : '' }}"
                                href="/category/women">Women</a>
                        </li>
                    </ul>
                </div>
            </div>


        </nav>
    </header>
    <aside id="sidebar"></aside>
    <main id="Content-body" class="py-3">


    <div class="container">

        <div class="row justify-content-center order-main-box mt-2">
            @if (isset($order) && isset($orderItems) && $orderItems->count() > 0)
                <div class="col-md-8 order-info-box">
                    {{-- <div class="order-logo mt-4">
                    <a class="footer-brand text-decoration-none text-success fw-bolder fs-4" href="#">
                        SELDOM FASHION</a>
                </div> --}}
                    <div class="order-text-site mt-5">
                        <h6 class="thanks">অসংখ্য ধন্যবাদ!</h6>
                        <h2 class="titel text-start text-success">অর্ডার সাকসেসফুল</h2>
                        <p class="order-some-text">
                            আপনার অর্ডারের জন্য কৃতজ্ঞতা জানাচ্ছি, কিছুক্ষনের মধ্যে অর্ডারটি
                            প্রসেস করা হবে। শীঘ্রই আমরা আপনার সাথে যোগাযোগ করব, সাথেই থাকুন।
                        </p>
                    </div>
                    <div class="row date-id mt-4 mb-3">
                        <div class="col">
                            <span class="order-id">Invoice ID:</span>
                            <span class="order-number">{{ $order->id }}</span>
                        </div>
                        <div class="col text-end">
                            <span class="date">Date: </span>
                            <span class="date-time">{{ $order->created_at }}</span>
                        </div>
                    </div>
                    <hr class="m-0">
                    @foreach ($orderItems as $item)
                        <div class="d-flex order-card p-2">
                            <img src="{{ $item->product->getImageThumbUrl() ?? '' }}"
                                alt="" class="me-2" />
                            <div class="">
                                <h5 class="order-product-name">{{ $item->product->name }}</h5>
                                {{-- <p class="order-product-weight">{{$item->product->weight}}</p> --}}
                            </div>
                            <p class="order-product-price ms-auto">
                                {{ $item->product->discount_price ?? $item->product->price }} x {{ $item->quantity }} =
                                {{ $item->subtotal }} টাকা</p>
                        </div>
                        <hr class="m-0">
                    @endforeach


                    <div class="order-price-unit mt-4">
                        {{-- <div class="d-flex justify-content-between">
                        <p class="delivery-info">Subtotal</p>
                        <p class="delivery-price">{{$order->subtotal}} Tk</p>
                    </div> --}}
                        <div class="d-flex justify-content-between">
                            <p class="delivery-info">Delivery fee - {{ $order->delivery_area->name }}</p>
                            <p class="order-price-unit">{{ $order->fee }} Tk</p>
                        </div>
                        {{-- <div class="d-flex justify-content-between">
                        <p class=" delivery-info">COD Charge {{$order->cod_percentage}}%</p>
                        <p class="order-price-unit">{{$order->cod_charge}} Tk</p>
                    </div> --}}
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class=" delivery-info-total">Total</p>
                            <p class="order-price-unit-total">{{ $order->total }} Tk</p>
                        </div>
                    </div>
                    {{-- <p class="i-text" style="font-size: 12px;">
                    <i>বিশেষ দ্রষ্টব্যঃ কাচা পণ্য হওয়ায় ওজনের তারতম্যের কারণে
                        মূল্যমান কিছুটা কম অথবা বেশি হতে পারে।</i>
                </p> --}}
                    <hr style="margin: 30px 0px;">
                    <div class="row person-information">
                        <div class="col-md-6">
                            <h5 class="some-titel">Address</h5>
                            <h6 class="person-name">{{ $order->name }}</h6>
                            <p class="order-address">{{ $order->address }} <br>
                                {{ $order->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="some-titel">Delivery</h5>
                            <h6 class="delivery-type">ক্যাশ অন ডেলিভারী</h6>
                            <p class="delivery-notice">আপনার অর্ডারের জন্য আন্তরিক কৃতজ্ঞতা। ২৪ ঘণ্টার মধ্যে আমাদের টিম
                                আপনার সাথে যোগাযোগ করবে এবং খুব শীঘ্রই আপনার অর্ডারটি প্রসেস করা হবে । Seldom এর সাথেই
                                থাকুন।</p>
                        </div>
                    </div>
                    {{-- <div class="buttom-notice mt-5">
                    <p>সম্মানিত গ্রাহকদের রিভিউ এবং আমাদের এক্সক্লুসিভ ডিসকাউন্ট অফারগুলো পেতে এখনই জয়েন করুন:</p>
                    <button class="btn btn-order justify-content-center"><i
                            class="fa-solid fa-user-group me-2"></i>ইকোইটস ফেসবুক গ্রুপ</button>
                </div> --}}
                </div>
            @else
                <div class="d-flex justify-content-center">
                    <h1 class="text-danger">আপনার অর্ডার টি নেই</h1>
                </div>
            @endif

        </div>
    </div>



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
        <a href="https://api.whatsapp.com/send?phone=+8801622351266&text=আমার%20কিছু%20প্রশ্ন%20ছিল" target="_blank" class="whatsapp_api_btn">
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
    <!-- Custom Js  -->
    <script src="{{ asset('frontend/js/script.js') }}"></script>
    <!-- Initialize Swiper -->
    <!-- Initialize Swiper -->
    <script>
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

    {{-- <script>
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
        let current_url = "{{ $trackingEvent->url }}";
        console.log('current_url:', current_url);
        let referrer_url = "{{ $trackingEvent->referrer }}";
        console.log('referrer:', referrer_url);
        console.log('Segment:', segment);
        let event_id = "{{ $trackingEvent->order_id }}";

        console.log("Event_ID: " + event_id);


        var ip_address = '{{ $trackingEvent->ip_address }}';
        console.log('IP Address:', ip_address);
        var userAgent = "{{ $trackingEvent->user_agent }}";
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
                first_name: "{{ $customer->first_name }}" ?? null,
                last_name: "{{ $customer->last_name }}" ?? null,
                // email_address: null,
                phone_number: "{{ $customer->phone }}" ?? null,
                street: "{{ $customer->street }}" ?? null,
                country: "BD",
                state: "{{ $customer->state }}" ?? null,
                city: "{{ $customer->city }}" ?? null,
                // region: null,
                zipcode: "{{ $customer->zip_code }}" ?? null,
                customer_id: "{{ $customer->id }}",
                // new_customer: 'true' // এটি অনুমান করা কঠিন হবে
                client_ip_address: ip_address || null,
                client_user_agent: userAgent || null,
                fbc: "{{ $trackingEvent->tracking_id ?? null }}",
            }
        }
        console.log(pageViewDataSet);
        //datalayer send to Pixel
        dataLayer = window.dataLayer || [];
        dataLayer.push(pageViewDataSet);
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
     <script>

        $(document).ready(function() {
            var custom_fbc = "{{ $trackingEvent->tracking_id }}" ?? '';
            console.log('custom_fbc:', custom_fbc);

            let event_id = "{{ $trackingEvent->order_id }}";
            console.log("Event_ID: " + event_id);



            var ip_address = '{{ $trackingEvent->ip_address }}';
            console.log('IP Address:', ip_address);
            var userAgent = "{{ $trackingEvent->user_agent }}";
            console.log('User Agent:', userAgent);
            var token = "{{ csrf_token() }}";


            let pamount = "{{ $order->total }}";
            pamount = parseFloat(pamount);
            console.log('dom ready');
            var purchaseDataSet = {

                event: 'purchase',
                event_id: '{{ $trackingEvent->order_id }}',
                event_source_url: current_url,
                referrer_url: referrer_url,
                ecommerce: {
                    value: pamount, // Number, two decimals, required
                    currency: 'BDT', // String, required
                    transaction_id: '{{ $order->id }}', // String, required, unique identifier of order/transaction
                    items: {!! json_encode($contents) !!},
                },

                user_data: {
                    first_name: "{{ $customer?->first_name ?? $order->name }}" ??
                        null, // বা এই লাইনগুলো বাদ দিন
                    last_name: "{{ $customer?->last_name }}" ?? null,
                    // email_address: null,
                    phone_number: "{{ $customer?->phone }}" ?? null,
                    street: "{{ $customer?->street ?? $order->address }}" ?? null,
                    country: "BD", // IP Address থেকে পাওয়া গেলে
                    state: "{{ $customer?->state }}" ?? null,
                    city: "{{ $customer?->city }}" ?? null,
                    zipcode: "{{ $customer?->zip_code }}" ?? null,
                    customer_id: "{{ $customer?->id }}",
                    client_ip_address: ip_address || null,
                    client_user_agent: userAgent || null,
                    fbc: custom_fbc,
                    fbp: "{{ $trackingEvent->tud_id }}"

                }
            };

            //datalayer send to Pixel

            dataLayer = window.dataLayer || [];
            dataLayer.push({
                ecommerce: null
            }); // we want to null out the ecommerce object, so there's no overlap if events happen on the same page
            console.log('purchaseDataSet:', purchaseDataSet);
            dataLayer.push(purchaseDataSet);
            fetch("/fb-pixel-capi?event_name=purchase&segment=" + segment, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(purchaseDataSet)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Facebook Pixel Purchase tracked:', data);
                })
                .catch(error => {
                    console.error('Error tracking Facebook Pixel Purchase:', error);
                });

        });

    </script> --}}
    @stack('scripts')

</body>

</html>

