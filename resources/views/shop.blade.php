@extends('layouts.app')
@php $_sn = $site['site_name'] ?? 'Gramer Dokan'; $_og = !empty($site['favicon']) ? asset('storage/'.$site['favicon']) : asset('frontend/img/seldom-rounded.png'); @endphp
@section('page_title', 'Shop | ' . $_sn)
@push('meta')
    <meta name="description" content="Shop the latest fashion collection at {{ $_sn }}. Premium quality clothing, sarees, and ethnic wear at the best price in Bangladesh.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type"        content="website">
    <meta property="og:title"       content="Shop | {{ $_sn }}">
    <meta property="og:description" content="Shop the latest fashion collection at {{ $_sn }}. Premium quality clothing, sarees, and ethnic wear at the best price in Bangladesh.">
    <meta property="og:image"       content="{{ $_og }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:site_name"   content="{{ $_sn }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="Shop | {{ $_sn }}">
    <meta name="twitter:description" content="Shop the latest fashion collection at {{ $_sn }}. Premium quality clothing, sarees, and ethnic wear at the best price in Bangladesh.">
    <meta name="twitter:image"       content="{{ $_og }}">
@endpush

@section('content')

    <section class="sec-style-1 my-3">
        <div class="container">


            <div class="sec-header">
                <h2 class="sec-title text-primary-color">Latest Products - নতুন পণ্য</h2>
                <hr class="divider mt-0 text-primary-color bg-primary-color " style="height: 2px;">
            </div>
            <div class="sec-body">
                <div class="sec-grid-box">
                    @foreach ($products as $product)
                        <div class="sec-grid-item p-card-1">

                            <div class="p-img-box">
                                <a href="{{ $product?->url }}">
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
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
      <section id="faq" class=" mb-3">
        <div class="container">

            <h1 class="fs-5 fw-bold bg-primary-color text-center py-3 px-3 text-white">সচরাচর জিজ্ঞাস্য প্রশ্নাবলি
            </h1>
            <ul class="list-inline fs-6 fw-medium">
                <li><i class="fa-solid fa-angles-right text-primary-color"></i> সারা বাংলাদেশে ক্যাশ অন ডেলিভারি
                    এভেইলেবল </li>
                <li><i class="fa-solid fa-angles-right  text-primary-color"></i> আপনি যদি আপনার ক্রয়কৃত ড্রেসটি
                    নিয়ে সন্তুষ্ট না হন, তবে শুধু ডেলিভারি চার্জ প্রদান করে ডেলিভারি ম্যানের কাছে সহজেই ফেরত দিতে
                    পারবেন। </li>
                <li><i class="fa-solid fa-angles-right  text-primary-color"></i> ডেলিভারি ম্যান থাকা অবস্তায় ডেলিভারি চার্জ দিয়ে রিটার্ন করতে হবে| ডেলিভারি ম্যান চলে আসার পর কোন ভাবেই রিটার্ন গ্রহণযোগ্য হবে না|</li>
                <li><i class="fa-solid fa-angles-right text-primary-color"></i>
                    প্রোডাক্ট পাওয়ার ২৪ ঘণ্টার মধ্যে সাইজের অথবা যেকোন সমস্যার জন্যে জানাতে হবে | ২৪ ঘণ্টা পর কোন সমস্যা গ্রহণযোগ্য হবে না | এক্সচেঞ্জ এ পুনরায় ডেলিভারি চার্জ যোগ হবে |
                </li>
            </ul>
        </div>
    </section>
@endsection
