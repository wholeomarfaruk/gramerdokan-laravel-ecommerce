@extends('layouts.admin')

@section('content')
<style>
.od-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:20px; margin-bottom:20px; }
.od-card h5 { font-size:15px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f3f4f6; }
.od-meta-label { font-size:11px; color:#9ca3af; font-weight:500; text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px; }
.od-meta-value { font-size:14px; color:#111827; font-weight:500; }
.od-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.od-badge-pending  { background:#fef3c7; color:#92400e; }
.od-badge-confirmed { background:#dbeafe; color:#1e40af; }
.od-badge-processing { background:#e0f2fe; color:#0369a1; }
.od-badge-in_transit { background:#ede9fe; color:#5b21b6; }
.od-badge-on_hold   { background:#f3f4f6; color:#374151; }
.od-badge-delivered { background:#d1fae5; color:#065f46; }
.od-badge-cancelled { background:#fee2e2; color:#991b1b; }
.od-badge-returned  { background:#fce7f3; color:#9d174d; }
.od-summary-row { display:flex; justify-content:space-between; align-items:center; padding:7px 0; font-size:14px; }
.od-summary-row + .od-summary-row { border-top:1px solid #f3f4f6; }
.od-summary-total { font-weight:700; font-size:16px; color:#111827; }
.od-timeline-item { display:flex; gap:12px; align-items:flex-start; margin-bottom:12px; }
.od-timeline-dot { width:10px; height:10px; border-radius:50%; background:#2377FC; margin-top:4px; flex-shrink:0; }
.od-items-table th { font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase; letter-spacing:.4px; border-top:none; padding:10px 12px; }
.od-items-table td { padding:12px; vertical-align:middle; font-size:13px; border-color:#f3f4f6; }
.od-product-thumb { width:44px; height:44px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; }
.od-pkg-icon { width:44px; height:44px; border-radius:6px; background:#fff3cd; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.od-opt-chip { display:inline-block; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:4px; padding:1px 6px; font-size:11px; margin:2px 2px 0 0; white-space:nowrap; }
.od-stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px; text-align:center; }
.od-stat-card .val { font-size:22px; font-weight:700; color:#111827; }
.od-stat-card .lbl { font-size:11px; color:#9ca3af; font-weight:500; text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }
.od-json-pre { background:#f8fafc; border:1px solid #e5e7eb; border-radius:6px; padding:12px 14px; font-size:11.5px; line-height:1.6; overflow:auto; max-height:240px; color:#374151; margin:0; }
.od-fraud-bar { height:6px; border-radius:3px; background:#e5e7eb; overflow:hidden; margin:4px 0; }
.od-fraud-fill { height:100%; border-radius:3px; }
.od-action-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:6px; font-size:13px; font-weight:500; border:1.5px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer; transition:.15s; text-decoration:none; }
.od-action-btn:hover { background:#f3f4f6; color:#111827; }
.od-action-btn.primary { background:#2377FC; border-color:#2377FC; color:#fff; }
.od-action-btn.primary:hover { background:#1a5fd8; }
</style>

<div class="main-content-inner">
<div class="main-content-wrap">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    @php
        $statusClass = [
            'pending'    => 'pending',
            'confirmed'  => 'confirmed',
            'processing' => 'processing',
            'in_transit' => 'in_transit',
            'on_hold'    => 'on_hold',
            'delivered'  => 'delivered',
            'cancelled'  => 'cancelled',
            'returned'   => 'returned',
        ][$order->status] ?? 'on_hold';
        $statusLabel = ucfirst(str_replace('_', ' ', $order->status));
    @endphp

    <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <div>
            <div class="flex items-center gap10 mb-1">
                <h3 class="mb-0">Order #{{ $order->id }}</h3>
                <span class="od-badge od-badge-{{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.orders') }}"><div class="text-tiny">Orders</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">#{{ $order->id }}</div></li>
            </ul>
        </div>
        <div class="flex gap10 flex-wrap">
            <a class="od-action-btn primary" target="_blank" href="{{ route('order.received_custom', $order->id) }}">
                <i class="icon-zap"></i> Fire Purchase Event
            </a>
            <a class="od-action-btn" href="{{ route('admin.orders') }}">
                <i class="icon-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    {{-- ── Summary Stats ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <div class="od-stat-card">
                <div class="val">#{{ $order->id }}</div>
                <div class="lbl">Order ID</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="od-stat-card">
                <div class="val">{{ $order->Order_Item->count() }}</div>
                <div class="lbl">Items</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="od-stat-card">
                <div class="val">৳{{ number_format($order->total, 0) }}</div>
                <div class="lbl">Total</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="od-stat-card">
                <div class="val"><span class="od-badge od-badge-{{ $statusClass }}" style="font-size:13px;">{{ $statusLabel }}</span></div>
                <div class="lbl">Status</div>
            </div>
        </div>
    </div>

    {{-- ── Two-Column Layout ─────────────────────────────────────────────── --}}
    <div class="row g-4">

        {{-- ════════════════ LEFT COLUMN ════════════════ --}}
        <div class="col-lg-8">

            {{-- ── Ordered Items ─────────────────────────────────────── --}}
            <div class="od-card">
                <div class="flex items-center justify-between" style="margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f3f4f6;">
                    <h5 style="margin:0;padding:0;border:none;">Ordered Items</h5>
                    <button type="button" class="od-action-btn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="icon-edit-3"></i> Edit Items
                    </button>
                </div>

                @if($orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table od-items-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Subtotal</th>
                                    <th>Options</th>
                                    <th class="text-center">Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                    @php
                                        $unitPrice = $item->product
                                            ? ($item->product->discount_price ?? $item->product->price)
                                            : $item->price;
                                        $lineTotal = $unitPrice * $item->quantity;
                                    @endphp
                                    <tr>
                                        {{-- Product --}}
                                        <td>
                                            <div class="flex items-center gap10">
                                                @if($item->product)
                                                    <img src="{{ $item->product->getImageThumbUrl() ?? asset('frontend/img/logo-transparent.png') }}"
                                                         alt="{{ $item->product->name }}"
                                                         class="od-product-thumb">
                                                    <div>
                                                        <a href="{{ route('product.show', ['slug' => $item->product->slug, 'segment' => $item->product->segment]) }}"
                                                           target="_blank" style="font-weight:600;color:#111827;font-size:13px;text-decoration:none;">
                                                            {{ $item->product->name }}
                                                        </a>
                                                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">ID: {{ $item->product->id }}</div>
                                                    </div>
                                                @else
                                                    <div class="od-pkg-icon">
                                                        <i class="icon-package" style="font-size:18px;color:#856404;"></i>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight:600;font-size:13px;color:#111827;">
                                                            {{ $item->options['package_label'] ?? 'Package Item' }}
                                                        </div>
                                                        <span class="od-badge od-badge-pending" style="font-size:10px;margin-top:2px;">Package</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Unit Price --}}
                                        <td class="text-center" style="font-weight:600;">৳{{ number_format($unitPrice, 2) }}</td>

                                        {{-- Qty --}}
                                        <td class="text-center">
                                            <span style="background:#f3f4f6;border-radius:4px;padding:2px 10px;font-weight:600;">{{ $item->quantity }}</span>
                                        </td>

                                        {{-- Subtotal --}}
                                        <td class="text-center" style="font-weight:700;color:#2377FC;">৳{{ number_format($lineTotal, 2) }}</td>

                                        {{-- Options --}}
                                        <td>
                                            @if(!empty($item->options))
                                                @php $opts = array_filter($item->options, fn($v) => $v !== null && $v !== '' && $v !== false); @endphp
                                                @if($opts)
                                                    @foreach($opts as $k => $v)
                                                        <span class="od-opt-chip"><strong>{{ $k }}</strong>: {{ $v }}</span>
                                                    @endforeach
                                                @else
                                                    <span style="color:#9ca3af;">—</span>
                                                @endif
                                            @else
                                                <span style="color:#9ca3af;">—</span>
                                            @endif
                                        </td>

                                        {{-- Return Status --}}
                                        <td class="text-center">
                                            @if($item->return_status)
                                                <span class="od-badge od-badge-returned">Returned</span>
                                            @else
                                                <span class="od-badge od-badge-delivered">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orderItems->hasPages())
                        <div class="divider mt-3"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 mt-3 wgp-pagination">
                            {{ $orderItems->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4" style="color:#9ca3af;">
                        <i class="icon-package" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No items found
                    </div>
                @endif
            </div>

            {{-- ── Update Status ─────────────────────────────────────── --}}
            <div class="od-card">
                <h5>Update Order Status</h5>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="flex gap10 flex-wrap items-center">
                        <select name="status" class="form-control" style="max-width:240px;">
                            @foreach(['pending','confirmed','processing','in_transit','on_hold','delivered','cancelled','returned'] as $st)
                                <option value="{{ $st }}" @selected($order->status == $st)>
                                    {{ ucfirst(str_replace('_', ' ', $st)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="od-action-btn primary">Update Status</button>
                    </div>
                </form>
            </div>

            {{-- ── Extra Data ─────────────────────────────────────────── --}}
            <div class="od-card">
                <h5>Extra Data</h5>

                @if($order->isEventFired)
                    <div class="alert alert-success mb-3" style="font-size:13px;">{{ $order->eventStatus }}</div>
                @else
                    <div class="alert alert-danger mb-3" style="font-size:13px;">{{ $order->eventStatus }}</div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="od-meta-label">IP Address</div>
                        <div class="od-meta-value">{{ $order->ip_address ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="od-meta-label">User Agent</div>
                        <div style="font-size:11px;color:#6b7280;word-break:break-all;line-height:1.5;">{{ $order->user_agent ?? '—' }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="od-meta-label mb-2">Tracking Event</div>
                    <pre class="od-json-pre">{{ json_encode($order->trackingEvent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div>
                    <div class="od-meta-label mb-2">Order JSON Data</div>
                    <pre class="od-json-pre">{!! json_encode($order->json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</pre>
                </div>
            </div>

        </div>{{-- /left --}}

        {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
        <div class="col-lg-4">

            {{-- ── Customer Info ─────────────────────────────────────── --}}
            <div class="od-card">
                <div class="flex items-center justify-between" style="margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f3f4f6;">
                    <h5 style="margin:0;padding:0;border:none;">Customer</h5>
                    <button type="button" class="od-action-btn" data-bs-toggle="modal" data-bs-target="#orderDetails">
                        <i class="icon-edit-3"></i> Edit
                    </button>
                </div>

                <div class="mb-3">
                    <div class="od-meta-label">Name</div>
                    <div class="od-meta-value">{{ $order->name }}</div>
                </div>

                <div class="mb-3">
                    <div class="od-meta-label">Phone</div>
                    <div class="flex items-center gap10 flex-wrap">
                        <div class="od-meta-value">{{ $order->phone }}</div>
                        @if($customer)
                            <a class="od-action-btn" target="_blank" href="{{ route('admin.customers.details', $customer->id) }}" style="font-size:12px;padding:3px 10px;">
                                <i class="icon-user"></i> Profile
                            </a>
                        @else
                            <a class="od-action-btn" target="_blank" href="{{ route('admin.orders.customer.create', $order->id) }}" style="font-size:12px;padding:3px 10px;">
                                <i class="icon-user-plus"></i> Create
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="od-meta-label">Delivery Area</div>
                    <div class="od-meta-value">
                        {{ $order?->delivery_area?->name ?? '—' }}
                        @if($order?->delivery_area?->charge)
                            <span style="color:#9ca3af;font-weight:400;"> · ৳{{ $order->delivery_area->charge }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="od-meta-label">Address</div>
                    <div style="font-size:13px;color:#374151;line-height:1.5;">{{ $order->address ?: '—' }}</div>
                </div>

                @if($order->note)
                    <div>
                        <div class="od-meta-label">Note</div>
                        <div style="font-size:13px;color:#6b7280;background:#f9fafb;border-radius:6px;padding:8px 10px;font-style:italic;">{{ $order->note }}</div>
                    </div>
                @endif
            </div>

            {{-- ── Order Summary ──────────────────────────────────────── --}}
            <div class="od-card">
                <h5>Order Summary</h5>
                <div class="od-summary-row">
                    <span style="color:#6b7280;">Subtotal</span>
                    <span>৳{{ number_format($order->sub_total, 2) }}</span>
                </div>
                <div class="od-summary-row">
                    <span style="color:#6b7280;">Delivery Fee</span>
                    <span>৳{{ number_format($order->fee, 2) }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="od-summary-row">
                        <span style="color:#6b7280;">Discount</span>
                        <span style="color:#dc2626;">−৳{{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                <div class="od-summary-row" style="border-top:2px solid #e5e7eb;margin-top:4px;padding-top:10px;">
                    <span class="od-summary-total">Total</span>
                    <span class="od-summary-total" style="color:#2377FC;">৳{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            {{-- ── Timeline ───────────────────────────────────────────── --}}
            <div class="od-card">
                <h5>Timeline</h5>
                <div class="od-timeline-item">
                    <div class="od-timeline-dot" style="background:#2377FC;"></div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#374151;">Ordered</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
                @if($order->delivery_date)
                    <div class="od-timeline-item">
                        <div class="od-timeline-dot" style="background:#10b981;"></div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#374151;">Delivered</div>
                            <div style="font-size:12px;color:#9ca3af;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                @endif
                @if($order->cancelled_date)
                    <div class="od-timeline-item">
                        <div class="od-timeline-dot" style="background:#ef4444;"></div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#374151;">Cancelled</div>
                            <div style="font-size:12px;color:#9ca3af;">{{ \Carbon\Carbon::parse($order->cancelled_date)->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── Fraud Check ────────────────────────────────────────── --}}
            @if($order->fraud_check_steadfast || $order->fraud_check_pathao)
                <div class="od-card">
                    <h5>Fraud Check</h5>

                    @if($order->fraud_check_steadfast)
                        @php
                            $sf_s = $order->fraud_check_steadfast['success'] ?? 0;
                            $sf_t = $order->fraud_check_steadfast['total'] ?? 0;
                            $sf_c = $order->fraud_check_steadfast['cancel'] ?? 0;
                            $sf_score = $sf_t > 0 ? min(($sf_s / $sf_t) * 100, 100) : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <span style="font-size:12px;font-weight:600;color:#374151;">SteadFast</span>
                                <span style="font-size:12px;font-weight:700;color:{{ $sf_score >= 70 ? '#10b981' : '#ef4444' }};">{{ number_format($sf_score, 1) }}%</span>
                            </div>
                            <div class="od-fraud-bar">
                                <div class="od-fraud-fill" style="width:{{ $sf_score }}%;background:{{ $sf_score >= 70 ? '#10b981' : '#ef4444' }};"></div>
                            </div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">
                                Total: <strong>{{ $sf_t }}</strong> &nbsp;·&nbsp; Received: <strong style="color:#10b981;">{{ $sf_s }}</strong> &nbsp;·&nbsp; Returned: <strong style="color:#ef4444;">{{ $sf_c }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($order->fraud_check_pathao)
                        @php
                            $pt_s = $order->fraud_check_pathao['success'] ?? 0;
                            $pt_t = $order->fraud_check_pathao['total'] ?? 0;
                            $pt_c = $order->fraud_check_pathao['cancel'] ?? 0;
                            $pt_score = $pt_t > 0 ? min(($pt_s / $pt_t) * 100, 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span style="font-size:12px;font-weight:600;color:#374151;">Pathao</span>
                                <span style="font-size:12px;font-weight:700;color:{{ $pt_score >= 70 ? '#10b981' : '#ef4444' }};">{{ number_format($pt_score, 1) }}%</span>
                            </div>
                            <div class="od-fraud-bar">
                                <div class="od-fraud-fill" style="width:{{ $pt_score }}%;background:{{ $pt_score >= 70 ? '#10b981' : '#ef4444' }};"></div>
                            </div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">
                                Total: <strong>{{ $pt_t }}</strong> &nbsp;·&nbsp; Received: <strong style="color:#10b981;">{{ $pt_s }}</strong> &nbsp;·&nbsp; Returned: <strong style="color:#ef4444;">{{ $pt_c }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- /right --}}
    </div>{{-- /row --}}

</div>{{-- /main-content-wrap --}}
</div>{{-- /main-content-inner --}}

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL: Edit Order Items
══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Order Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.orders.editupdate', $order->id) }}" method="POST" id="orderEdit">
                    @csrf
                    @method('PUT')

                    <fieldset class="mb-3">
                        <label for="products" class="form-label fw-semibold">Add / Edit Products</label>
                        <select name="products[]" id="products"
                            class="form-control selectpicker @error('products') is-invalid @enderror"
                            multiple data-live-search="true" title="Choose products...">
                            @foreach($products as $product)
                                @php $isSelected = $order->Order_Item->pluck('product_id')->contains($product->id); @endphp
                                <option value="{{ $product->id }}" data-id="{{ $product->id }}"
                                    {{ $product->stock_status == 'out_of_stock' ? 'disabled' : '' }}
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $product->name }} -
                                    {{ $product->stock_status == 'in_stock' ? 'In Stock' : 'Out of Stock' }} -
                                    {{ $product->discount_price ?? $product->price }} Tk
                                </option>
                            @endforeach
                        </select>
                    </fieldset>

                    <div id="editForm" class="mt-3">
                        @foreach($order->Order_Item as $item)
                            @if(!$item->product)
                                <div class="product-item border rounded p-3 mb-3" style="border-color:#ffc107!important;background:#fffdf0;">
                                    <div class="row align-items-center text-center text-md-start">
                                        <div class="col-12 col-md-2 mb-2 mb-md-0">
                                            <span class="badge bg-warning text-dark">Package</span>
                                            @if($item->options['kg'] ?? false)
                                                <div class="small text-muted mt-1">{{ $item->options['kg'] }} কেজি</div>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                                            <h6 class="mb-1">{{ $item->options['package_label'] ?? '—' }}</h6>
                                            <p class="mb-0 small text-muted">Price: <strong>৳{{ $item->price }}</strong></p>
                                        </div>
                                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                                            <label class="form-label small">Quantity</label>
                                            <p class="mb-0 fw-bold fs-5">{{ $item->quantity }}</p>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <label class="form-label small">Options</label>
                                            <p class="mb-0 small text-muted" style="word-break:break-all;">{{ $item->options ? json_encode($item->options, JSON_UNESCAPED_UNICODE) : '—' }}</p>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <span class="badge bg-secondary">Landing Page</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="product-item-{{ $item->product->id }}" class="product-item border rounded bg-light p-3 mb-3">
                                    <div class="row align-items-center text-center text-md-start">
                                        <div class="col-12 col-md-2 mb-2 mb-md-0">
                                            <img src="{{ $item->product->getImageFullUrl() ?? '' }}"
                                                 alt="{{ $item->product->name }}" class="img-fluid rounded"
                                                 style="max-height:80px;object-fit:cover;">
                                        </div>
                                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <p class="mb-0">Price: <strong class="product-price" data-price="{{ $item->product->discount_price ?? $item->product->price }}">{{ $item->product->discount_price ?? $item->product->price }} Tk</strong></p>
                                        </div>
                                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                                            <label class="form-label small">Quantity</label>
                                            <input type="text" class="edit_product_id" hidden name="order_items[{{ $item->product->id }}][id]" value="{{ $item->product->id }}">
                                            <input type="number" name="order_items[{{ $item->product->id }}][quantity]"
                                                   id="quantity_{{ $item->product->id }}" value="{{ $item->quantity }}"
                                                   min="1" class="form-control quantity-input" data-id="{{ $item->product->id }}">
                                        </div>
                                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                                            <label class="form-label small">Options / Size</label>
                                            <input type="text" name="order_items[{{ $item->product->id }}][size]"
                                                   id="options_{{ $item->product->id }}" class="form-control"
                                                   placeholder="Enter size" value="{{ $item->options['size'] ?? '' }}">
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm w-100 mt-2 mt-md-0"
                                                    onclick="removeProduct({{ $item->product->id }})">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <fieldset class="mt-3">
                        <label for="delivery_charge" class="form-label fw-semibold">Delivery Charge</label>
                        <input type="number" id="delivery_charge" name="delivery_charge" class="form-control"
                               placeholder="Enter delivery charge" value="{{ $order->fee ?? 0 }}" min="0">
                    </fieldset>
                    <fieldset class="mt-3">
                        <label for="discount" class="form-label fw-semibold">Discount Amount</label>
                        <input type="number" id="discount" name="discount" class="form-control"
                               placeholder="Enter discount amount if any" value="{{ $order->discount ?? 0 }}" min="0">
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer flex-column align-items-stretch">
                <table class="table table-bordered mb-3 text-center align-middle">
                    <tr><th width="40%">Sub Total:</th><td><span id="subTotal">0</span> Tk</td></tr>
                    <tr><th>Discount:</th><td><span id="discount_price">0</span> Tk</td></tr>
                    <tr class="table-info"><th>Total:</th><td><strong><span id="total">0</span> Tk</strong></td></tr>
                </table>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('orderEdit').submit();">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL: Edit Order Details
══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="orderDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customer & Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.orders.update.details', $order->id) }}" method="POST" id="orderDetailForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $order->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $order->phone }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Delivery Address</label>
                            <textarea name="address" class="form-control" rows="3">{{ $order->address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="note" class="form-control" rows="3">{{ $order->note }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('orderDetailForm').submit();">Save Changes</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════════ --}}
<script>
var allProducts = @json($products);

const productSelect  = document.getElementById('products');
const editForm       = document.getElementById('editForm');
const discountInput  = document.getElementById('discount');
const subTotalEl     = document.getElementById('subTotal');
const discountEl     = document.getElementById('discount_price');
const fee            = document.getElementById('delivery_charge');
const totalEl        = document.getElementById('total');

document.getElementById('exampleModal').addEventListener('shown.bs.modal', function () {
    attachQuantityListeners();
    calculateTotal();
});

productSelect.addEventListener('change', function () {
    const selectedIds = Array.from(this.selectedOptions).map(opt => opt.value);
    let addedIds      = Array.from(editForm.querySelectorAll('input.edit_product_id')).map(i => i.value);
    let filteredIds   = selectedIds.filter(id => !addedIds.includes(id));
    const selected    = allProducts.filter(p => filteredIds.includes(String(p.id)));

    selected.forEach(product => {
        const price = product.discount_price ?? product.price;
        const html  = `
        <div id="product-item-${product.id}" class="product-item border rounded bg-light p-3 mb-3">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-12 col-md-2 mb-2 mb-md-0">
                    <img src="${product.image && (product.image.startsWith('http') || product.image.startsWith('/')) ? product.image : '/storage/images/products/' + product.image}" alt="${product.name}"
                         class="img-fluid rounded" style="max-height:80px;object-fit:cover;">
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <h6 class="mb-1">${product.name}</h6>
                    <p class="mb-0">Price: <strong class="product-price" data-price="${price}">${price} Tk</strong></p>
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="form-label small">Quantity</label>
                    <input type="text" hidden name="order_items[${product.id}][id]" value="">
                    <input type="number" name="order_items[${product.id}][quantity]" value="1" min="1"
                           class="form-control quantity-input" data-id="${product.id}">
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <label class="form-label small">Size</label>
                    <input type="text" name="order_items[${product.id}][size]" class="form-control" placeholder="Enter size" value="">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100 mt-2 mt-md-0"
                            onclick="removeProduct(${product.id})">Remove</button>
                </div>
            </div>
        </div>`;
        $(editForm).append(html);
    });

    attachQuantityListeners();
    calculateTotal();
});

function attachQuantityListeners() {
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.removeEventListener('input', calculateTotal);
        input.addEventListener('input', calculateTotal);
    });
}

function removeProduct(productId) {
    const el = document.getElementById(`product-item-${productId}`);
    if (el) el.remove();
    const opt = document.querySelector(`#products option[value="${productId}"]`);
    if (opt) opt.selected = false;
    if ($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
    calculateTotal();
}

discountInput.addEventListener('input', calculateTotal);
fee.addEventListener('input', calculateTotal);

function calculateTotal() {
    let subTotal = 0;
    document.querySelectorAll('.product-item').forEach(item => {
        const priceEl = item.querySelector('.product-price');
        const qtyEl   = item.querySelector('.quantity-input');
        if (!priceEl || !qtyEl) return;
        subTotal += (parseFloat(priceEl.dataset.price) || 0) * (parseInt(qtyEl.value) || 0);
    });
    const deliveryCharge = parseFloat(fee.value) || 0;
    const discount       = parseFloat(discountInput.value) || 0;
    const total          = Math.max(subTotal - discount, 0) + deliveryCharge;
    subTotalEl.textContent = subTotal.toFixed(2);
    discountEl.textContent = discount.toFixed(2);
    totalEl.textContent    = total.toFixed(2);
}

attachQuantityListeners();
calculateTotal();
</script>

@endsection
