<div>
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
                                            <img lazy
                                                src="{{ $product->getImageFullUrl() ?? '' }}" />

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
                                        <span class="play-button "><img
                                                src="{{ asset('image/youtube_shorts_icon.png') }}" style="width: 100px;"
                                                alt=""></span>
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
                                        <span class="play-button "><img
                                                src="{{ asset('image/youtube_shorts_icon.png') }}" style="width: 30px;"
                                                alt=""></span>

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
                                        <input wire:model.lazy='name' id="name" type="text" name="name"
                                            autocomplete="name" class="form-control" required
                                            placeholder="Type Your Full Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-bold fs-5">আপনার মোবাইল লিখুন
                                        </label>
                                        <input wire:model.lazy='phone' name="phone" id="phone" type="text"
                                            class="form-control" required minlength="11" inputmode="numeric"
                                            autocomplete="tel" placeholder="Type Your Phone Number">


                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label fw-bold fs-5">আপনার ফুল ঠিকানা
                                            লিখুন</label>
                                        <textarea wire:model.lazy="address" autocomplete="address" required name="address" class="form-control"
                                            id="address" placeholder="Type Your Full Delivery Address" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="delivery_area" class="form-label fw-bold fs-5">ডেলিভারি এরিয়া
                                            সিলেক্ট
                                            করুন
                                        </label>
                                        <select wire:model.lazy="delivery_area" name="delivery_area"
                                            class="form-select" id="delivery_area"
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
                                            <!-- <input type="button" value="-"
                                                                                                                                                                                                                                class="button-minus border rounded-circle btn-primary  icon-shape icon-sm mx-1 lh-0"
                                                                                                                                                                                                                                > -->
                                            <input wire:model.lazy="quantity" type="number" step="1"
                                                max="10" min="1" value="1" name="quantity"
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
</div>
@push('scripts')
    <script>
        Livewire.on('initiate-checkout', (payload) => {
            console.log('initiate-checkout', payload);
            @if (session('debugmode'))
                const viewItemEventPayload = payload[0];
                console.log('viewItemEventPayload', viewItemEventPayload);
                if (viewItemEventPayload) {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push(viewItemEventPayload);
                }
            @endif
        });
    </script>
@endpush
