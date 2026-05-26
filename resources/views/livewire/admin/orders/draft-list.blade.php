<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Drafts</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Order Drafts</div></li>
            </ul>
        </div>

        {{-- Stats --}}
        <div class="wg-box mb-3">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="text-center">
                    <div style="font-size:22px;font-weight:700;color:#2377FC;">{{ $totalDrafts }}</div>
                    <div class="text-tiny text-muted">Total Drafts</div>
                </div>
                <div style="width:1px;height:32px;background:#e9ecef;"></div>
                <div class="text-center">
                    <div style="font-size:22px;font-weight:700;color:#dc3545;">{{ $expiredDrafts }}</div>
                    <div class="text-tiny text-muted">Expired</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="wg-box mb-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="flex-grow-1" style="max-width:320px;">
                    <input type="text"
                           wire:model.live.debounce.400ms="search"
                           placeholder="Search by name or phone…"
                           class="form-control form-control-sm">
                </div>
                <div wire:loading wire:target="search" class="text-muted" style="font-size:12px;">
                    Searching…
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="wg-box">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name / Phone</th>
                            <th>Address</th>
                            <th>Payment</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Delivery</th>
                            <th class="text-end">Total</th>
                            <th>Items</th>
                            <th>Expires</th>
                            <th>Saved</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drafts as $draft)
                            <tr>
                                <td class="text-muted" style="font-size:12px;">{{ $draft->id }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $draft->name ?: '—' }}</div>
                                    <div class="text-muted" style="font-size:12px;">{{ $draft->phone ?: '—' }}</div>
                                </td>
                                <td style="max-width:160px;">
                                    <div class="text-truncate text-muted" style="font-size:12px;">
                                        {{ $draft->address ?: '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size:11px;">
                                        {{ $draft->payment_method }}
                                    </span>
                                </td>
                                <td class="text-end">৳{{ number_format($draft->subtotal, 2) }}</td>
                                <td class="text-end">৳{{ number_format($draft->delivery_charge, 2) }}</td>
                                <td class="text-end fw-bold">৳{{ number_format($draft->total, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary" style="font-size:11px;">
                                        {{ $draft->items->count() }}
                                    </span>
                                </td>
                                <td style="font-size:12px;">
                                    @if ($draft->expires_at)
                                        @if ($draft->expires_at->isPast())
                                            <span class="text-danger">Expired</span>
                                        @else
                                            {{ $draft->expires_at->diffForHumans() }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="font-size:12px;" class="text-muted">
                                    {{ $draft->created_at->format('d M y, H:i') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button"
                                                wire:click="createOrder({{ $draft->id }})"
                                                wire:confirm="Create a pending order from this draft?"
                                                class="btn btn-outline-success btn-sm"
                                                title="Create order">
                                            <i class="icon-shopping-cart"></i>
                                        </button>
                                        <button type="button"
                                                wire:click="deleteDraft({{ $draft->id }})"
                                                wire:confirm="Delete this draft permanently?"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Delete draft">
                                            <i class="icon-trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Inline items --}}
                            @if ($draft->items->isNotEmpty())
                                <tr class="table-secondary">
                                    <td colspan="11" class="py-2 px-4">
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($draft->items as $item)
                                                <div class="d-flex align-items-center gap-2"
                                                     style="font-size:12px;">
                                                    @if ($item->product_image)
                                                        <img src="{{ asset('storage/images/products/thumbnails/' . $item->product_image) }}"
                                                             style="width:28px;height:28px;object-fit:cover;border-radius:4px;">
                                                    @endif
                                                    <span>{{ $item->product_name ?? 'Product #'.$item->product_id }}</span>
                                                    <span class="badge bg-light text-dark border">×{{ $item->quantity }}</span>
                                                    @if (!empty($item->options) && is_array($item->options))
                                                        @foreach ($item->options as $key => $val)
                                                            @if ($val)
                                                                <span class="badge bg-secondary" style="font-size:11px;">{{ ucfirst($key) }}: {{ $val }}</span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <span class="text-muted">৳{{ number_format($item->total, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="icon-file-text" style="font-size:32px;opacity:.3;display:block;margin-bottom:8px;"></i>
                                    No order drafts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($drafts->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $drafts->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
