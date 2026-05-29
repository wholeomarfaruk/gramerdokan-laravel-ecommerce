<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="mb-1">Your cart</h6>
            <small class="text-muted">Items: <span data-cart-total-items>{{ $cart->items->count() }}</span></small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" data-clear-cart wire:click="clearCart">Clear</button>
    </div>


    <div data-mini-cart-items class="d-grid gap-3">
        @forelse ($cart->items as $item)
            <div class="d-flex gap-3 border rounded-3 p-3">
                <img src="{{ $item->product->getImageFullUrl() ?? '' }}"
                    alt="{{ $item->product->name ?? 'Product image' }}" width="64" height="64"
                    class="rounded-3 object-fit-cover" style="width: 64px;">

                <div class="flex-grow-1">
                    <h3 class="h6 mb-1">{{ $item->product->name ?? 'Unnamed Product' }}</h3>
                    <p class="small text-muted mb-1">Qty: {{ $item->quantity }}</p>

                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ number_format($item->total, 2) }}</strong>
                        <div class="d-flex align-items-center gap-2 mt-2">

                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                wire:click="decreaseQty({{ $item->id }})">
                                -
                            </button>

                            <span class="px-2">{{ $item->quantity }}</span>

                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                wire:click="increaseQty({{ $item->id }})">
                                +
                            </button>

                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            wire:click="removeItem({{ $item->id }})">
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
    <div>

    </div>
    <div class="cart-summary mt-4 border-top pt-3">
        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <strong data-cart-subtotal>৳{{ $cart->subTotal() ?? 0.0 }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span>Total items</span>
            <strong data-cart-total-items>{{ $cart->totalItems() ?? 0 }}</strong>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('cart.view') }}" class="btn btn-outline-primary">View Cart</a>
            <a href="{{ route('cart.checkout') }}" class="btn btn-danger">Checkout</a>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        // Listen for add-to-cart events and push the payload to the data layer

        document.addEventListener('livewire:init', () => {
            Livewire.on('add-to-cart-event', (event) => {
                const payload = event.payload;
                console.log('Add to Cart event payload:', payload);
                if (payload) {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push(payload);
                }
            })
        })
    </script>
@endpush
