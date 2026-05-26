@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Customers</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Customers</div></li>
            </ul>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="wg-box h-100" style="border-left:4px solid #2377FC;">
                    <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Total Customers</div>
                    <div style="font-size:28px;font-weight:700;color:#2377FC;">{{ number_format($totalCustomers) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">All time</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wg-box h-100" style="border-left:4px solid #28a745;">
                    <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">New This Month</div>
                    <div style="font-size:28px;font-weight:700;color:#28a745;">{{ number_format($newThisMonth) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">
                        vs {{ $newLastMonth }} last month
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wg-box h-100" style="border-left:4px solid #fd7e14;">
                    <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Have Orders</div>
                    <div style="font-size:28px;font-weight:700;color:#fd7e14;">{{ number_format($activeCustomers) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">
                        {{ $totalCustomers > 0 ? round($activeCustomers / $totalCustomers * 100) : 0 }}% conversion
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wg-box h-100" style="border-left:4px solid #6f42c1;">
                    <div class="text-muted mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">No Orders Yet</div>
                    <div style="font-size:28px;font-weight:700;color:#6f42c1;">{{ number_format($totalCustomers - $activeCustomers) }}</div>
                    <div class="text-muted mt-1" style="font-size:11px;">Inactive customers</div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row g-3 mb-4">

            {{-- Top Customers Bar Chart --}}
            <div class="col-lg-7">
                <div class="wg-box h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="fw-semibold" style="font-size:15px;">Top 10 Customers</div>
                            <div class="text-muted" style="font-size:12px;">by total spend</div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:11px;">৳ Revenue</span>
                    </div>
                    <div style="position:relative;height:260px;">
                        <canvas id="topCustomersChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Monthly Growth Line Chart --}}
            <div class="col-lg-5">
                <div class="wg-box h-100">
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:15px;">Monthly Growth</div>
                        <div class="text-muted" style="font-size:12px;">New customers — last 7 months</div>
                    </div>
                    <div style="position:relative;height:260px;">
                        <canvas id="monthlyGrowthChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        {{-- Customer List --}}
        <div class="wg-box">
            <div class="mb-3">
                <div class="fw-semibold" style="font-size:15px;">All Customers</div>
            </div>
            @livewire('admin.customers.customer-list')
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin-resource/lib/chartjs/chart.min.js') }}"></script>
<script>
(function () {

    // --- Top Customers Chart ---
    var topLabels = @json($topCustomers->map(fn($c) => trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: 'ID #' . $c->id));
    var topValues = @json($topCustomers->pluck('orders_sum_total')->map(fn($v) => round($v)));

    new Chart(document.getElementById('topCustomersChart'), {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Total Spent (৳)',
                data: topValues,
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
                tooltip: {
                    callbacks: {
                        label: ctx => ' ৳' + ctx.raw.toLocaleString()
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: {
                        font: { size: 11 },
                        callback: v => '৳' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });

    // --- Monthly Growth Chart ---
    var mLabels = @json($monthlyGrowth->pluck('label'));
    var mValues = @json($monthlyGrowth->pluck('count'));

    new Chart(document.getElementById('monthlyGrowthChart'), {
        type: 'line',
        data: {
            labels: mLabels,
            datasets: [{
                label: 'New Customers',
                data: mValues,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#28a745',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.raw + ' customers' } }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: { font: { size: 11 }, stepSize: 1, precision: 0 },
                    beginAtZero: true,
                }
            }
        }
    });

})();
</script>
@endpush
