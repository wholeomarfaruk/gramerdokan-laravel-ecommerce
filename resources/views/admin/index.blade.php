@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Dashboard</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><div class="text-tiny">Dashboard</div></li>
            </ul>
        </div>

        {{-- KPI Cards Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #2377FC;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Total Orders</div>
                    <div style="font-size:26px;font-weight:700;color:#2377FC;">{{ number_format($total_orders) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">৳{{ number_format($total_orders_sum) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #28a745;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">This Month</div>
                    <div style="font-size:26px;font-weight:700;color:#28a745;">{{ number_format($ordersThisMonth) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">৳{{ number_format($revenueThisMonth) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #fd7e14;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Pending</div>
                    <div style="font-size:26px;font-weight:700;color:#fd7e14;">{{ number_format($pending_orders) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">৳{{ number_format($pending_orders_sum) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #20c997;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Delivered</div>
                    <div style="font-size:26px;font-weight:700;color:#20c997;">{{ number_format($delivered_orders) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">৳{{ number_format($delivered_orders_sum) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #dc3545;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Cancelled</div>
                    <div style="font-size:26px;font-weight:700;color:#dc3545;">{{ number_format($cancelled_orders) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">৳{{ number_format($cancelled_orders_sum) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="wg-box h-100" style="border-left:4px solid #6f42c1;">
                    <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Active Now</div>
                    <div style="font-size:26px;font-weight:700;color:#6f42c1;">{{ number_format($active_users) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">Last 5 min</div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row g-3 mb-4">
            {{-- Revenue & Orders Line Chart --}}
            <div class="col-lg-8">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="fw-semibold" style="font-size:15px;">Revenue & Orders</div>
                            <div class="text-muted" style="font-size:12px;">Last 7 months (excluding cancelled)</div>
                        </div>
                        <div class="d-flex gap-3" style="font-size:11px;">
                            <span><span style="display:inline-block;width:10px;height:10px;background:#2377FC;border-radius:50%;"></span> Revenue</span>
                            <span><span style="display:inline-block;width:10px;height:10px;background:#28a745;border-radius:50%;"></span> Orders</span>
                        </div>
                    </div>
                    <div style="position:relative;height:240px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Products Bar Chart --}}
            <div class="col-lg-4">
                <div class="wg-box h-100">
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Top Products</div>
                        <div class="text-muted" style="font-size:12px;">By total revenue</div>
                    </div>
                    <div style="position:relative;height:240px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Orders + Top Customers --}}
        <div class="row g-3 mb-4">

            {{-- Recent Orders --}}
            <div class="col-lg-7">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Recent Orders</div>
                        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Customer</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Total</th>
                                    <th style="width:90px;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="text-muted">{{ $order->id }}</td>
                                        <td>
                                            <div class="fw-semibold" style="line-height:1.2;">{{ $order->name ?: '—' }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $order->phone ?: '' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusColor = match($order->status) {
                                                    'pending'    => '#fd7e14',
                                                    'delivered'  => '#28a745',
                                                    'cancelled'  => '#dc3545',
                                                    'processing' => '#17a2b8',
                                                    default      => '#6c757d',
                                                };
                                            @endphp
                                            <span style="font-size:11px;font-weight:600;color:{{ $statusColor }};">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">৳{{ number_format($order->total) }}</td>
                                        <td class="text-muted">{{ $order->created_at?->format('d M') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Top Customers --}}
            <div class="col-lg-5">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Top Customers</div>
                        <a href="{{ route('admin.customers') }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">View All</a>
                    </div>
                    @forelse ($topCustomers as $i => $c)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($c->first_name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="fw-semibold" style="font-size:13px;line-height:1.2;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: 'ID #' . $c->id }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">{{ $c->orders_count }} orders</div>
                            </div>
                            <div class="fw-semibold text-primary" style="font-size:13px;white-space:nowrap;">
                                ৳{{ number_format($c->orders_sum_total ?? 0) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No customers yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Recent Drafts + Recent Carts --}}
        <div class="row g-3 mb-4">

            {{-- Recent Draft Orders --}}
            <div class="col-lg-6">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Recent Draft Orders</div>
                        <a href="{{ route('admin.order.drafts') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px;">View All</a>
                    </div>
                    @forelse ($recentDrafts as $draft)
                        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid rgba(0,0,0,.05);">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;">{{ $draft->name ?: '—' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $draft->phone ?: 'No phone' }} &bull; {{ $draft->items_count }} item(s)</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold" style="font-size:13px;">৳{{ number_format($draft->total ?? 0) }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $draft->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No draft orders.</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Carts --}}
            <div class="col-lg-6">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Recent Active Carts</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:11px;">Live</span>
                    </div>
                    @forelse ($recentCarts as $cart)
                        <div class="py-2" style="border-bottom:1px solid rgba(0,0,0,.05);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="text-muted" style="font-size:11px;">
                                    Device: {{ \Illuminate\Support\Str::limit($cart->device?->device_id ?? 'unknown', 16) }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">{{ $cart->updated_at?->diffForHumans() }}</div>
                            </div>
                            @if ($cart->items->isNotEmpty())
                                @foreach ($cart->items as $item)
                                    <div class="d-flex align-items-center justify-content-between" style="font-size:12px;padding:2px 0;">
                                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;">
                                            {{ $item->product?->name ?? 'Unknown product' }}
                                        </span>
                                        <span class="text-muted ms-2" style="white-space:nowrap;">
                                            x{{ $item->quantity }}
                                            &nbsp;৳{{ number_format($item->price * $item->quantity) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted" style="font-size:12px;">Empty cart</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No active carts.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Top Products List --}}
        <div class="wg-box mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-semibold" style="font-size:15px;">Top Products by Revenue</div>
                <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Product</th>
                            <th class="text-center">Units Sold</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topProducts as $i => $product)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $img = $product->image
                                                ? ((str_starts_with($product->image, 'http') || str_starts_with($product->image, '/')) ? $product->image : asset('storage/images/products/' . $product->image))
                                                : null;
                                        @endphp
                                        @if ($img)
                                            <img src="{{ $img }}"
                                                 style="width:36px;height:36px;object-fit:cover;border-radius:6px;" alt="">
                                        @else
                                            <div style="width:36px;height:36px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                                                <i class="icon-package" style="font-size:16px;color:#ccc;"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ number_format($product->units) }}</span>
                                </td>
                                <td class="text-end fw-semibold text-primary">৳{{ number_format($product->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No sales data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin-resource/lib/chartjs/chart.min.js') }}"></script>
<script>
(function () {

    // --- Revenue & Orders Line Chart ---
    var mLabels   = @json($monthlyStats->pluck('label'));
    var mRevenue  = @json($monthlyStats->pluck('revenue')->map(fn($v) => round($v)));
    var mOrders   = @json($monthlyStats->pluck('orders_count'));

    var revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: mLabels,
                datasets: [
                    {
                        label: 'Revenue (৳)',
                        data: mRevenue,
                        borderColor: '#2377FC',
                        backgroundColor: 'rgba(35,119,252,0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#2377FC',
                        borderWidth: 2,
                        yAxisID: 'yRevenue',
                    },
                    {
                        label: 'Orders',
                        data: mOrders,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.0)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#28a745',
                        borderWidth: 2,
                        borderDash: [4, 3],
                        yAxisID: 'yOrders',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) return ' ৳' + ctx.raw.toLocaleString();
                                return ' ' + ctx.raw + ' orders';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    yRevenue: {
                        position: 'left',
                        grid: { color: 'rgba(0,0,0,.05)' },
                        ticks: {
                            font: { size: 11 },
                            callback: v => '৳' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                        }
                    },
                    yOrders: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { font: { size: 11 }, stepSize: 1, precision: 0 },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    // --- Top Products Horizontal Bar ---
    var pLabels = @json($topProducts->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20)));
    var pValues = @json($topProducts->pluck('revenue')->map(fn($v) => round($v)));

    var prodCtx = document.getElementById('topProductsChart');
    if (prodCtx) {
        new Chart(prodCtx, {
            type: 'bar',
            data: {
                labels: pLabels,
                datasets: [{
                    label: 'Revenue (৳)',
                    data: pValues,
                    backgroundColor: 'rgba(35,119,252,0.15)',
                    borderColor: '#2377FC',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ৳' + ctx.raw.toLocaleString() } }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,.05)' },
                        ticks: {
                            font: { size: 10 },
                            callback: v => '৳' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                        }
                    },
                    y: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

})();
</script>
@endpush
