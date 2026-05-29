@extends('layouts.app')
@section('segment', $segment)

@section('page_title', ($product->name ?? 'Product') . ' | ' . ($site['site_name'] ?? 'Gramer Dokan'))
@push('meta')
    @php
        $_sn       = $site['site_name'] ?? 'Gramer Dokan';
        $metaName  = $product->name ?? 'Product';
        $metaDesc  = $product->short_description ?? $product->description ?? 'Buy ' . $metaName . ' from ' . $_sn . '. Premium quality clothing at the best price.';
        $metaDesc  = \Illuminate\Support\Str::limit(strip_tags((string) $metaDesc), 155);
        $metaFb    = !empty($site['favicon']) ? asset('storage/'.$site['favicon']) : asset('frontend/img/seldom-rounded.png');
        $metaImage = $product->image ? asset('storage/images/products/' . $product->image) : $metaFb;
        $metaUrl   = url()->current();
        $metaPrice = $product->discount_price ?? $product->price ?? null;
    @endphp
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $metaUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="product">
    <meta property="og:title"       content="{{ $metaName }} | {{ $_sn }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image"       content="{{ $metaImage }}">
    <meta property="og:url"         content="{{ $metaUrl }}">
    <meta property="og:site_name"   content="{{ $_sn }}">
    @if ($metaPrice)
        <meta property="product:price:amount"   content="{{ $metaPrice }}">
        <meta property="product:price:currency" content="BDT">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $metaName }} | {{ $_sn }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image"       content="{{ $metaImage }}">
@endpush
{{-- @if ($product->id == 3)
@dd($product,$segment);

@endif --}}
@push('styles')
    <style>
        .mySwiper2 .swiper-slide {
            height: 576px;

            text-align: center;
        }

        @media screen and (max-width:768px) {
            .mySwiper2 .swiper-slide {
                height: 500px;

                text-align: center;
            }
        }

        .mySwiper2 .swiper-slide img {
            height: 100%;
            width: auto;
            margin: 0 auto;
            object-fit: contain;

        }

        .mySwiper2 .swiper-slide img a {
            display: block;
            text-align: center;
        }

        .navigation .swiper-slide {
            height: 100px;

        }

        .navigation .swiper-slide img {
            height: 100%;
            width: cover;
        }

        .swiper-button-next,
        .swiper-button-prev {

            background: #48484887;
            padding: 27px 25px;
            border-radius: 5px;
            margin: 3px;
        }
    </style>
@endpush

@section('content')
    <section id="product" class="mb-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 left ">
                    <!-- Swiper -->
                    <div style="max-width: 650px;" class="shadow">
                        <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                            class="swiper mySwiper2">
                            <div class="swiper-wrapper">


                                @if ($product?->image)
                                    <div class="swiper-slide">
                                        <a href="{{ $product->getImageFullUrl() ?? '' }}"
                                            data-fancybox="gallery">
                                            <img lazy src="{{ $product->getImageFullUrl() ?? '' }}" />

                                        </a>
                                    </div>
                                @endif
                                @if ($product?->media?->where('category', 'product_images')->count() > 0)
                                    @foreach ($product->media->where('category', 'product_images') as $pimage)
                                        <div class="swiper-slide">
                                            <a href="{{ $pimage->getUrl() }}" data-fancybox="gallery">
                                                <img src="{{ $pimage->getUrl() }}" />
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                                @if ($product?->yt_video_url)
                                    <div class="swiper-slide">
                                        <a href="https://www.youtube.com/shorts/{{ $product?->yt_video_url }}"
                                            data-fancybox="gallery">
                                            <img style="object-fit: cover; width: 100%;"
                                                src="https://img.youtube.com/vi/{{ $product?->yt_video_url }}/maxresdefault.jpg" />

                                        </a>
                                        <span class="play-button "><img src="{{ asset('image/youtube_shorts_icon.png') }}"
                                                style="width: 100px;" alt=""></span>
                                    </div>
                                @endif
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>

                        <div class="swiper mySwiper navigation">
                            <div class="swiper-wrapper">



                                @if ($product?->image)
                                    <div class="swiper-slide">
                                        <img lazy src="{{ $product->getImageFullUrl() ?? '' }}" />


                                    </div>
                                @endif

                                @if ($product?->media?->where('category', 'product_images')->count() > 0)
                                    @foreach ($product->media->where('category', 'product_images') as $pimage)
                                        <div class="swiper-slide">

                                            <img src="{{ $pimage->getUrl() }}" />

                                        </div>
                                    @endforeach
                                @endif
                                @if ($product?->yt_video_url)
                                    <div class="swiper-slide">


                                        <img
                                            src="https://img.youtube.com/vi/{{ $product?->yt_video_url }}/maxresdefault.jpg" />
                                        <span class="play-button "><img src="{{ asset('image/youtube_shorts_icon.png') }}"
                                                style="width: 30px;" alt=""></span>

                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- Swiper JS -->
                </div>
                <div class="col-lg-7 right details ">

                    <h1 class="title text-primary-color fw-bolder mt-3">{{ $product?->name }}</h1>
                    {{-- <p class="text-secondary"> <strong>{{ $product?->id }}</strong> </p> --}}


                    <div class="row price-details align-items-center justify-content-between">
                        <div class="col-lg-6 Price text-start">
                            @if ($product?->discount_price && $product?->discount_price > 0)
                                <strong class="fw-bold fs-4"></strong>
                                <span class="regular-price fs-5"><del>৳ {{ $product?->price }} </del></span>
                                <strong class="fw-bold fs-4"> </strong>
                                <span class="discount-price fs-2 fw-bold "> ৳ {{ $product?->discount_price }}</span>
                            @else
                                <strong class="fw-bold fs-4">Price: </strong>
                                <span class="discount-price fs-2 fw-bold ">৳ {{ $product?->price }}</span>
                            @endif

                        </div>
                        @if ($product?->stock_status == 'out_of_stock')
                            <div class="col-lg-6">
                                <h4 class="stock-in text-danger text-end"> Stock Out </h4>
                            </div>
                        @endif
                    </div>
                    <hr>
                    <p class="fs-6 fw-bold">
                        <strong class="text-danger ">বিদ্রঃ</strong> আপনার অর্ডার নিশ্চিত করতে নিচের ফর্মটি পূরণ করে
                        অর্ডার
                        বাটন ক্লিক করুন।
                    </p>
                    <hr>
                    <div class="order-form-box">
                        <style>
                            input,
                            select,
                            textarea {
                                border-color: 1px solid var(--primary-accent-color) !important;

                            }
                        </style>
                        <h4 class="fw-bold fs-4 text-center mb-3 text-decoration-underline"
                            style="text-underline-offset: 5px">অর্ডার ফর্ম</h4>

                        <form id="order-form" action="{{ route('cart.order.place') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" name="product_id" value="{{ $product?->id }}">
                                    <input type="hidden" name="product_price" id="product_price"
                                        value="{{ $product->discount_price && $product->discount_price > 0 ? $product->discount_price : $product->price }}">
                                </div>

                                @if ($product?->sizes->count() > 0)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold fs-5 d-block">সাইজ সিলেক্ট করুন</label>
                                            <style>
                                                .size-option {
                                                    display: inline-block;
                                                    margin-right: 8px;
                                                }

                                                .size-option input[type="radio"] {
                                                    display: none;
                                                    /* hide real radio */
                                                }

                                                .size-option label {
                                                    border: 2px solid #ccc;
                                                    padding: 8px 15px;
                                                    border-radius: 8px;
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                    user-select: none;
                                                    font-size: 20px;
                                                }

                                                .size-option input[type="radio"]:checked+label {
                                                    background-color: var(--primary-color);
                                                    /* Bootstrap primary */
                                                    color: #fff;
                                                    border-color: var(--primary-color);
                                                }

                                                .size-option label:hover {
                                                    border-color: var(--primary-color);
                                                }
                                            </style>

                                            <div class="d-flex flex-wrap">
                                                @foreach ($product?->sizes as $size)
                                                    <div class="size-option">
                                                        <input class="form-check-input" type="radio" name="size"
                                                            value="{{ $size->name }}" id="size-{{ $size->id }}"
                                                            required>
                                                        <label class="form-check-label" for="size-{{ $size->id }}">
                                                            {{ $size->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold fs-5">আপনার নাম লিখুন</label>
                                        <input id="name" type="text" name="name" autocomplete="name"
                                            class="form-control" required placeholder="Type Your Full Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-bold fs-5">আপনার মোবাইল লিখুন
                                        </label>
                                        <input name="phone" id="phone" type="text" class="form-control" required
                                            minlength="11" inputmode="numeric" autocomplete="tel"
                                            placeholder="Type Your Phone Number">


                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label fw-bold fs-5">আপনার ফুল ঠিকানা
                                            লিখুন</label>
                                        <textarea autocomplete="address" required name="address" class="form-control" id="address"
                                            placeholder="Type Your Full Delivery Address" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="delivery_area" class="form-label fw-bold fs-5">ডেলিভারি এরিয়া
                                            সিলেক্ট
                                            করুন
                                        </label>
                                        <select name="delivery_area" class="form-select" id="delivery_area"
                                            aria-label="Default select example">

                                            @foreach ($deliveryAreas as $deliveryArea)
                                                <option value="{{ $deliveryArea?->id }}"
                                                    data-charge="{{ $deliveryArea?->charge }}">
                                                    {{ $deliveryArea?->name }} - TK {{ $deliveryArea?->charge }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold fs-5">পরিমাণ</label>
                                        <div class="input-group w-auto justify-content-end align-items-center">
                                            <button type="button"
                                                class="fs-2 button-minus border rounded-circle icon-shape icon-sm mx-1 lh-0"
                                                data-field="quantity">
                                                <i class="fa-solid fa-circle-minus text-primary-color"></i>
                                            </button>
                                            <input type="number" step="1" max="10" min="1"
                                                value="1" name="quantity"
                                                class="quantity-field border rounded text-center w-25 form-control "
                                                style="border-color: var(--primary-accent-color) !important;">
                                            <button type="button"
                                                class="fs-2 button-plus border rounded-circle btn-primary  icon-shape icon-sm mx-1 lh-0"
                                                data-field="quantity">
                                                <i class="fa-solid fa-circle-plus text-primary-color"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold fs-5">মোট দাম</label>
                                        <p class="rounded border p-2 fw-bold fs-4" id="total">0</p>
                                        {{-- <input name="total" class="form-control fw-bold fs-5" type="text" value="1950"
                                            aria-label="Disabled input example" readonly> --}}
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <button wire:click="submit" id="order-button" type="submit"
                                        {{ $product?->stock_status == 'out_of_stock' ? 'disabled' : '' }}
                                        class="btn btn-primary bg-primary-color mb-3 w-100 fw-bold fs-5 py-2 ">অর্ডার
                                        করতে
                                        ক্লিক
                                        করুন
                                        {{ $product?->stock_status == 'out_of_stock' ? '(স্টক শেষ)' : '' }}</button>
                                </div>
                                <div class="col-sm-6">
                                    <buttont type="button"
                                        {{ $product?->stock_status == 'out_of_stock' ? 'disabled' : '' }}
                                        class="btn btn-primary bg-primary-color mb-3 w-100 fw-bold fs-5 py-2 " x-data
                                        x-on:click="$dispatch('add-to-cart', { productId: {{ $product->id }} })">
                                        কার্টে যোগ
                                        করুন{{ $product?->stock_status == 'out_of_stock' ? '(স্টক শেষ)' : '' }}
                                    </buttont>
                                </div>

                            </div>
                        </form>
                    </div>
                    <hr>
                    <div>
                        {!! $product?->description !!}
                    </div>

                    <div class="delivery-charge border rounded overflow-hidden mb-3">
                        <table class="table ">

                            <tbody class="fw-bold fs-6 ">
                                @if ($deliveryAreas->isEmpty())
                                    <tr>
                                        <td colspan="2" class="text-center">ডেলিভারি এরিয়া সেট করা নেই</td>
                                    </tr>
                                @else
                                    @foreach ($deliveryAreas as $deliveryArea)
                                        <tr>
                                            <td>{{ $deliveryArea?->name }}</td>
                                            <td>৳{{ $deliveryArea?->charge }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>

    @if ($products->count() > 0)
        <section class="sec-style-2 my-3">
            <div class="container">
                <div class="sec-header">
                    <h2 class="sec-title text-primary-color">More Products - আরো দেখুন</h2>
                    <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
                </div>
                <div class="sec-body">
                    <div class="sec-grid-box">
                        @foreach ($products as $pitem)
                            <div class="sec-grid-item p-card-1">

                                <div class="p-img-box">
                                    <a href="{{ $pitem?->url }}">
                                        <img src="{{ $pitem->getImageFullUrl() ?? '' }}"
                                            alt="">
                                    </a>
                                </div>
                                <div class="p-info">
                                    <div class="prices">
                                        @if ($pitem->discount_price && $pitem->discount_price > 0)
                                            <del class="old-price">৳ {{ $pitem->price }}</del>
                                            <span class="price">৳ {{ $pitem->discount_price }}</span>
                                        @else
                                            <span class="price">Price: ৳ {{ $pitem->price }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ $pitem?->url }}">

                                        <h1 class="p-title">{{ $pitem->name }}</h1>
                                    </a>
                                    <a href="{{ $pitem?->url }}">
                                        <p class="p-description">
                                            বিস্তারিত দেখুন
                                        </p>
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
                </div>
            </div>
        </section>
    @endif
    @livewire('website.components.faq-section')
@endsection
@push('scripts')
    @if (isset($dataLayerService))
        {!! $dataLayerService !!}
    @else
        <!-- convertkit -->
    @endif
    @if (session('status') == 'error')
        <script>
            Swal.fire({
                icon: "{{ session('status') == 'error' ? 'error' : 'success' }}",
                title: "{{ session('status') == 'error' ? 'দুঃখিত!' : 'সফল!' }}",
                text: "{{ session('message') }}",
                confirmButtonText: 'ঠিক আছে',
                timer: 4000, // Auto close after 4 seconds
                timerProgressBar: true,
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {

            function calculateTotal() {
                // Get product price and convert to float
                let price = parseFloat($('#product_price').val()) || 0;

                // Get quantity
                let quantity = parseInt($('input[name="quantity"]').val()) || 1;

                // Get selected delivery charge
                let deliveryCharge = parseFloat($('select[name="delivery_area"] option:selected').data('charge')) ||
                    0;

                // Calculate total
                let total = (price * quantity) + deliveryCharge;

                // Set formatted total in the total input field
                $('#total').text(total.toFixed(2));
            }

            // Initial calculation on page load
            calculateTotal();

            // Recalculate when quantity changes
            $('input[name="quantity"]').on('input change', function() {
                calculateTotal();
            });

            // Recalculate when delivery area changes
            $('select[name="delivery_area"]').on('change', function() {
                calculateTotal();
            });

            // Optional: plus and minus buttons
            $('.button-plus').click(function() {
                let $input = $(this).siblings('input[name="quantity"]');
                let val = parseInt($input.val()) || 1;
                if (val < parseInt($input.attr('max'))) {
                    $input.val(val + 1).trigger('change');
                }
            });

            $('.button-minus').click(function() {
                let $input = $(this).siblings('input[name="quantity"]');
                let val = parseInt($input.val()) || 1;
                if (val > parseInt($input.attr('min'))) {
                    $input.val(val - 1).trigger('change');
                }
            });

        });
    </script>
    <script>
        // Only digits allowed; also enforces max length = 11
        const phone = document.getElementById('phone');

        // Strip non-digits on input & cap at 11
        phone.addEventListener('input', () => {
            phone.value = phone.value.replace(/\D/g, '');
        });

        // Block non-digit keypress (still keep Backspace, Delete, arrows, Tab)
        phone.addEventListener('keydown', (e) => {
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
            if (allowedKeys.includes(e.key)) return;
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    </script>

    <script>
        let is_sent = false;

        function AutoSave() {
            var name = $("input[name='name']").val();
            var phone = $("input[name='phone']").val();
            var address = $("textarea[name='address']").val();
            var size = $("input[name='size']:checked").val() || '';
            var product_id = $("input[name='product_id']").val();
            var quantity = $("input[name='quantity']").val();
            var delivery_area = $("select[name='delivery_area']").val();
            var token = "{{ csrf_token() }}";
            var order_data = {
                name: name,
                phone: phone,
                address: address,
                size: size,
                product_id: product_id,
                quantity: quantity,
                delivery_area: delivery_area,
                XSRF_TOKEN: token,

            }
            fetch('/cart/autosave', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },

                body: JSON.stringify(order_data)
            })

        }

        $(document).ready(function() {
            var custom_fbc = localStorage.getItem('custom_fbc') ?? '';

            let initiatecheckout_event_id = new Date().getTime() + '_' + Math.random().toString(36).substring(2, 9);

            let pamount = "{{ $product->discount_price ?? $product->price }}";
            pamount = parseFloat(pamount);


            dataLayer = window.dataLayer || [];
            dataLayer.push({
                ecommerce: null
            }); // we want to null out the ecommerce object, so there's no overlap if events happen on the same page

            const viewItemEventPayload = @json($viewItemEventPayload);
            if (viewItemEventPayload) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(viewItemEventPayload);
            }

            function sentInitialCheckout() {
                let value = parseFloat($("#total").text());
                let quantity = parseFloat($(".quantity-field").val());
                let name = $("input[name='name']").val();
                let phone = $("input[name='phone']").val();
                let address = $("input[name='address']").val();
                let size = $("input[name='size']").val();
                let deliveryCharge = parseFloat($('select[name="delivery_area"] option:selected').data('charge')) ||
                    0;

                let initiateCheckoutEvent = @json($initiateCheckoutEventPayload);
                console.log("viewItemEventPayload: ", viewItemEventPayload);

                initiateCheckoutEvent.ecommerce.value = value;
                initiateCheckoutEvent.ecommerce.contents[0].quantity = quantity;
                initiateCheckoutEvent.custom_data.value = value;
                initiateCheckoutEvent.custom_data.shipping_cost = deliveryCharge;



                dataLayer.push(initiateCheckoutEvent);

                let result;
                fetch("/fb-pixel-capi?event_name=initiate_checkout&segment=" + segment, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            'payload': initiateCheckoutEvent
                        })
                    })
                    .then(response => response.json())
                    .then(data => {

                        result = data;
                        is_sent = true;
                    })
                    .catch(error => {
                        console.error('Error tracking Facebook Pixel InitiateCheckout:', error);
                    });
                return result;

            }
            $('#order-button').on('click', async function(e) {
                // e.preventDefault();

                sentInitialCheckout();
                AutoSave();
            });

            $('#order-form').on('submit', async function(e) {
                e.preventDefault();
                if (!$("#order-form")[0].checkValidity()) {

                }
                swal.fire({
                    title: "Loading...",
                    html: `
                        <div class="d-flex justify-content-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                // sentInitialCheckout()
                var name = $("input[name='name']").val();
                var phone = $("input[name='phone']").val();
                var address = $("textarea[name='address']").val();
                var size = $("input[name='size']").val();
                var product_id = $("input[name='product_id']").val();
                var quantity = $("input[name='quantity']").val();
                var delivery_area = $("select[name='delivery_area']").val();
                var token = "{{ csrf_token() }}";

                var order_data = {
                    name: name,
                    phone: phone,
                    address: address,
                    size: size,
                    product_id: product_id,
                    quantity: quantity,
                    delivery_area: delivery_area,
                    XSRF_TOKEN: token,

                }
                // console.log(order_data);
                fetch('/cart/ordernow', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },

                    body: JSON.stringify(order_data)
                }).then(response => response.json()).then(data => {
                    // console.log('Order Now tracked:', data);
                    setTimeout(() => {

                    }, 2000);
                    if (data.status == 'error') {


                        Swal.fire({
                            icon: data.status,
                            title: data.status === 'error' ? 'দুঃখিত!' : 'সফল!',
                            text: data.message,
                            confirmButtonText: 'ঠিক আছে',
                            // timer: 4000, // Auto close after 4 seconds
                            timerProgressBar: true,
                        });
                    } else if (data.status == 'success') {

                        Swal.fire({
                            icon: data.status,
                            title: data.status === 'error' ? 'দুঃখিত!' : 'সফল!',
                            text: data.message,
                            confirmButtonText: 'ঠিক আছে',
                            timer: 4000, // Auto close after 4 seconds
                            timerProgressBar: true,
                        });
                        window.location.href = data.redirect_url;
                    }
                }).catch(error => {
                    console.error('Error tracking Facebook Pixel ViewContent:', error);
                });


            });

            // dataLayer.push(viewContentDataSet);
            // fetch("/fb-pixel-capi?event_name=view_content&segment=" + segment, {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': token
            //         },
            //         body: JSON.stringify(viewContentDataSet)
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         console.log('Facebook Pixel ViewContent tracked:', data);
            //     })
            //     .catch(error => {
            //         console.error('Error tracking Facebook Pixel ViewContent:', error);
            //     });

        });
    </script>

    <script>
        $('#order-form').on('change', function() {

            AutoSave();
        });

    </script>
@endpush
