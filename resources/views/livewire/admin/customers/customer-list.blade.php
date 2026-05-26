<div>
    {{-- Toolbar --}}
    <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
        <div class="flex-grow-1" style="max-width:320px;">
            <input type="text"
                   wire:model.live.debounce.400ms="search"
                   placeholder="Search name, phone, email…"
                   class="form-control form-control-sm">
        </div>
        <div wire:loading wire:target="search" class="text-muted" style="font-size:12px;">
            Searching…
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <label class="text-muted mb-0" style="font-size:12px;white-space:nowrap;">Show</label>
            <select wire:model.live="perPage" class="form-select form-select-sm" style="width:70px;">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>
                        <button wire:click="sort('first_name')" class="btn btn-link btn-sm p-0 text-dark text-decoration-none fw-semibold">
                            Customer
                            @if($sortBy === 'first_name')
                                <i class="icon-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }}" style="font-size:10px;"></i>
                            @endif
                        </button>
                    </th>
                    <th>Phone</th>
                    <th class="text-center">
                        <button wire:click="sort('orders_count')" class="btn btn-link btn-sm p-0 text-dark text-decoration-none fw-semibold">
                            Orders
                            @if($sortBy === 'orders_count')
                                <i class="icon-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }}" style="font-size:10px;"></i>
                            @endif
                        </button>
                    </th>
                    <th class="text-end">
                        <button wire:click="sort('orders_sum_total')" class="btn btn-link btn-sm p-0 text-dark text-decoration-none fw-semibold">
                            Total Spent
                            @if($sortBy === 'orders_sum_total')
                                <i class="icon-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }}" style="font-size:10px;"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sort('created_at')" class="btn btn-link btn-sm p-0 text-dark text-decoration-none fw-semibold">
                            Joined
                            @if($sortBy === 'created_at')
                                <i class="icon-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }}" style="font-size:10px;"></i>
                            @endif
                        </button>
                    </th>
                    <th>Last Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td class="text-muted">{{ $customer->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($customer->first_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="line-height:1.2;">
                                        {{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: '—' }}
                                    </div>
                                    @if($customer->email)
                                        <div class="text-muted" style="font-size:11px;">{{ $customer->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $customer->phone ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border" style="font-size:11px;">
                                {{ $customer->orders_count }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold">
                            @if($customer->orders_sum_total)
                                ৳{{ number_format($customer->orders_sum_total, 0) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:12px;">
                            {{ $customer->created_at?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="text-muted" style="font-size:12px;">
                            @php
                                $lastActive = $customer->devices()->latest('last_activity')->value('last_activity');
                            @endphp
                            {{ $lastActive ? \Carbon\Carbon::parse($lastActive)->diffForHumans() : 'Never' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.customers.details', $customer->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.delete', $customer->id) }}"
                                   class="btn btn-sm btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete this customer?')">
                                    <i class="icon-trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="icon-users" style="font-size:32px;opacity:.3;display:block;margin-bottom:8px;"></i>
                            No customers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $customers->links() }}
        </div>
    @endif
</div>
