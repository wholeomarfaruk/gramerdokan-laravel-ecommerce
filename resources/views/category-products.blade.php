@extends('layouts.app')
@section('segment', $segment ?? '')
@section('page_title', ($category->name ?? 'Category') . ' | ' . ($site['site_name'] ?? 'Gramer Dokan'))
@push('meta')
    @php
        $_sn       = $site['site_name'] ?? 'Gramer Dokan';
        $_fb       = !empty($site['favicon']) ? asset('storage/'.$site['favicon']) : asset('frontend/img/seldom-rounded.png');
        $metaDesc  = $category->description ?? 'Shop ' . ($category->name ?? '') . ' collection at ' . $_sn . '. Premium quality clothing at the best price.';
        $metaDesc  = \Illuminate\Support\Str::limit(strip_tags((string) $metaDesc), 155);
        $metaImage = $category->getImageUrl() ?? $_fb;
        $metaUrl   = url()->current();
    @endphp
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $metaUrl }}">

    <meta property="og:type"        content="website">
    <meta property="og:title"       content="{{ ($category->name ?? 'Category') . ' | ' . $_sn }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image"       content="{{ $metaImage }}">
    <meta property="og:url"         content="{{ $metaUrl }}">
    <meta property="og:site_name"   content="{{ $_sn }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ ($category->name ?? 'Category') . ' | ' . $_sn }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image"       content="{{ $metaImage }}">
@endpush

@section('content')


    <section class="sec-style-1 my-3">
        <div class="container">
            {{-- <div class="sec-header">
                @if ($category->image)
                    <img src="{{ $category->getImageUrl() ?? '' }}"
                         alt="{{ $category->name }}"
                         class="w-100 rounded"
                         style="max-height:320px;object-fit:cover;display:block;">
                @endif
      
                <div class="d-none justify-content-center align-items-center bg-primary-color rounded" style="min-height:100px;">
                    <h2 class="sec-title text-white fs-1" style="text-transform:uppercase;">{{ $category->name }}</h2>
                </div>
                <hr class="divider mt-0 text-primary-color bg-primary-color" style="height:2px;">
            </div> --}}
            <div class="sec-body">
                <div class="sec-grid-box">
                    @forelse ($products as $product)
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
                    @empty
                        <div class="text-center d-flex flex-column justify-content-center ">
                            <h1>New Products Coming soon</h1>
                            <a href="/" class="btn btn-primary">Back to Home</a>
                        </div>
                    @endforelse
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
                <li><i class="fa-solid fa-angles-right text-primary-color"></i>সমস্ত এক্সচেঞ্জে উপভোগ করুন সম্পূর্ণ
                    ফ্রি ডেলিভারি — কোন অতিরিক্ত চার্জ নেই, কোন ঝামেলা নেই।
                <li><i class="fa-solid fa-angles-right text-primary-color"></i>আমাদের আছে ডেলিভারির পর ৩ দিন
                    পর্যন্ত
                    এক্সচেঞ্জ সুবিধা।
            </ul>
        </div>
    </section>
@endsection
