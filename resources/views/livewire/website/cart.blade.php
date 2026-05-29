<div>
       <section class="py-5">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h1 class="h3 mb-1">Shopping Cart</h1>
                    <p class="text-muted mb-0">Review your selected products before checkout.</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-outline-secondary">Continue Shopping</a>
            </div>

            @if (session('cart_error'))
                <div class="alert alert-warning">{{ session('cart_error') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">Cart Items</h2>
                                <button wire:click="clearCart" type="button" class="btn btn-sm btn-outline-danger" data-clear-cart>Clear cart</button>
                            </div>

                            <div data-cart-page-items>
                                @forelse ($cart->items as $item)
                                    <div class="border rounded-3 p-3 mb-3" data-cart-row="{{ $item->id }}">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-2 col-4">
                                                <img src="{{ $item->product->getImageFullUrl() ?? '' }}"
                                                    alt="{{ $item->product->name ?? 'Product image' }}"
                                                    class="img-fluid rounded-3 w-100">
                                            </div>
                                            <div class="col-md-4 col-8">
                                                <h3 class="h6 mb-1">{{ $item->product->name }}</h3>
                                                <p class="text-muted mb-2">Unit price: ৳{{ number_format($item->price, 2) }}</p>

                                                    <a href="{{ $item->product?->url }}" class="small">View product</a>

                                            </div>
                                            <div class="col-md-3 col-12">
                                                <label class="form-label small text-muted">Quantity</label>
                                                <div class="input-group">
                                                    <button wire:click='decreaseQty({{ $item->id }})' class="btn btn-outline-secondary"
                                                        type="button"
                                                        data-cart-qty-step="decrease"
                                                        data-cart-item-id="{{ $item->id }}">-</button>
                                                    <input type="number"
                                                        min="1"
                                                        value="{{ $item->quantity }}"
                                                        class="form-control text-center"
                                                        data-cart-quantity
                                                        data-cart-item-id="{{ $item->id }}">
                                                    <button wire:click='increaseQty({{ $item->id }})' class="btn btn-outline-secondary"
                                                        type="button"
                                                        data-cart-qty-step="increase"
                                                        data-cart-item-id="{{ $item->id }}">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-12 text-md-end">
                                                <p class="fw-semibold mb-2">৳{{ number_format($item->total, 2) }}</p>
                                                <button wire:click="removeItem({{ $item->id }})" type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    data-remove-cart-item
                                                    data-cart-item-id="{{ $item->id }}">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5 border rounded-3 bg-light">
                                        Your cart is empty.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 2rem;">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Order Summary</h2>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total items</span>
                                <strong data-cart-summary-count>{{ $cart->totalItems() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span>Subtotal</span>
                                <strong data-cart-summary-subtotal>৳{{ number_format($cart->subTotal(), 2) }}</strong>
                            </div>

                            <a href="{{ route('cart.checkout') }}"
                                class="btn btn-primary w-100 {{ empty($cart->items) ? 'disabled' : '' }}"
                                data-cart-page-checkout>
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            // Check if purchaseEventPayload is available
            @if (isset($AddToCartbrowserPayload))
                // Push the purchase event payload to the data layer
                // console.log('Add to Cart event payload:', @json($AddToCartbrowserPayload));
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(@json($AddToCartbrowserPayload));
            @endif
        });

    </script>
@endpush
