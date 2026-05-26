@extends('layouts.app')

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
                                    <img src="{{ asset('storage/images/products/' . $product->image) }}" alt="">
                                </a>
                            </div>
                            <div class="p-info">
                                <div class="prices">
                                    @if ($product->discount_price && $product->discount_price > 0)
                                        <del class="old-price">৳ {{ $product->price }}</del>
                                        <span class="price">৳ {{ $product->discount_price }}</span>
                                    @else
                                        <span class="price">Price: ৳ {{ $product->price }}</span>
                                    @endif


                                </div>
                                <a class="visually-hidden" href="{{ $product?->url }}">

                                    <h1 class="p-title">{{ $product->name }}</h1>
                                </a>
                                <a href="{{ $product?->url }}">
                                    <p class="p-description">
                                        বিস্তারিত দেখুন
                                    </p>
                                </a>
                            </div>
                            <div class="p-btn-group">
                                <a class="btn btn-primary w-100 d-block"
                                    href="{{ $product?->url }}">Buy Now</a>
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
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        var screen_size = window.screen.width + 'x' + window.screen.height;
        console.log('screen_size:', screen_size);
        var user_data = document.cookie.split(';').filter(item => item.trim().startsWith('_sfud='))[0]?.split('=')[1];
        user_data = user_data ? JSON.parse(decodeURIComponent(user_data)) : null;
        var device_id = document.cookie.split(';').filter(item => item.trim().startsWith('_sfdid='))[0]?.split('=')[1];
        console.log('user_data', user_data);
        console.log('device_id', device_id);
    });
</script>
@endpush
