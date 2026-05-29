@extends('layouts.admin')

@section('content')
<style>
/* ── Campaign table ── */
.cmp-table { width:100%;border-collapse:collapse; }
.cmp-table thead th {
    font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
    color:#9ca3af;padding:10px 16px;border-bottom:1px solid #f3f4f6;
    background:#fafafa;white-space:nowrap;
}
.cmp-table tbody tr { transition:background .12s; }
.cmp-table tbody tr:hover { background:#f9fafb; }
.cmp-table tbody td { padding:14px 16px;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.cmp-table tbody tr:last-child td { border-bottom:none; }

/* ID chip */
.cmp-id { font-size:11px;font-weight:700;color:#9ca3af;font-variant-numeric:tabular-nums; }

/* Name + slug */
.cmp-name { font-size:13.5px;font-weight:600;color:#111827;text-decoration:none;transition:color .15s; }
.cmp-name:hover { color:#2377FC; }
.cmp-slug { font-size:11px;color:#9ca3af;margin-top:3px;font-family:monospace; }

/* View file badge */
.cmp-tmpl {
    display:inline-block;font-size:10px;font-weight:600;
    background:#eff6ff;color:#2377FC;
    border:1px solid #dbeafe;border-radius:5px;
    padding:2px 8px;white-space:nowrap;
}

/* Status badge */
.cmp-status {
    display:inline-flex;align-items:center;gap:5px;
    font-size:11.5px;font-weight:600;padding:4px 10px;
    border-radius:20px;white-space:nowrap;
}
.cmp-status.published   { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.cmp-status.unpublished { background:#fef9c3;color:#92400e;border:1px solid #fde68a; }
.cmp-status .dot { width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0; }

/* Toggleable status button */
.cmp-status-toggle { cursor:pointer;background:none;transition:opacity .15s,transform .1s; }
.cmp-status-toggle:hover { opacity:.8;transform:scale(1.04); }
.cmp-status-toggle.loading { opacity:.5;pointer-events:none; }

/* Date */
.cmp-date { font-size:12px;color:#6b7280;white-space:nowrap; }

/* Actions */
.cmp-actions { display:flex;align-items:center;gap:6px; }
.cmp-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:32px;height:32px;border-radius:7px;
    border:1.5px solid #e5e7eb;background:#fff;
    color:#6b7280;text-decoration:none;font-size:14px;
    transition:all .15s;cursor:pointer;
}
.cmp-btn:hover { border-color:#2377FC;color:#2377FC;background:#eff6ff; }
.cmp-btn.danger:hover  { border-color:#ef4444;color:#ef4444;background:#fef2f2; }
.cmp-btn.success:hover { border-color:#16a34a;color:#16a34a;background:#f0fdf4; }

/* Dropdown */
.cmp-dd { position:relative; }
.cmp-dd-menu {
    position:absolute;right:0;top:calc(100% + 4px);z-index:200;
    background:#fff;border:1px solid #e5e7eb;border-radius:10px;
    box-shadow:0 10px 28px rgba(0,0,0,.1),0 2px 8px rgba(0,0,0,.06);
    min-width:190px;padding:6px 0;
    opacity:0;pointer-events:none;transform:translateY(-6px);
    transition:opacity .15s,transform .15s;
}
.cmp-dd-menu.show { opacity:1;pointer-events:auto;transform:translateY(0); }
.cmp-dd-item {
    display:flex;align-items:center;gap:10px;
    padding:9px 16px;font-size:13px;color:#374151;
    text-decoration:none;cursor:pointer;background:none;border:none;width:100%;
    transition:background .1s;
}
.cmp-dd-item:hover { background:#f9fafb;color:#111827; }
.cmp-dd-item i { font-size:14px;color:#9ca3af;flex-shrink:0; }
.cmp-dd-item:hover i { color:#2377FC; }
.cmp-dd-item.danger { color:#ef4444; }
.cmp-dd-item.danger i { color:#ef4444; }
.cmp-dd-item.danger:hover { background:#fef2f2; }
.cmp-dd-divider { height:1px;background:#f3f4f6;margin:5px 0; }

/* Add button */
.cmp-add-btn {
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 18px;border-radius:8px;
    background:#2377FC;color:#fff;border:none;
    font-size:13px;font-weight:600;text-decoration:none;
    transition:background .15s,transform .15s;
    white-space:nowrap;
}
.cmp-add-btn:hover { background:#1a5fd8;color:#fff;transform:translateY(-1px); }

/* Empty state */
.cmp-empty { text-align:center;padding:60px 20px; }
.cmp-empty i { font-size:2.5rem;color:#d1d5db;display:block;margin-bottom:12px; }
.cmp-empty p { color:#9ca3af;font-size:13px; }

/* Search */
.cmp-search-wrap { position:relative;flex:1;min-width:200px;max-width:320px; }
.cmp-search-wrap i { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px; }
.cmp-search { width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;color:#111827;outline:none;transition:border-color .15s; }
.cmp-search:focus { border-color:#2377FC; }
</style>

<div class="main-content-inner">
<div class="main-content-wrap">

    {{-- Header --}}
    <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <div>
            <h3 class="mb-1">Campaigns</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Campaigns</div></li>
            </ul>
        </div>
        <a class="cmp-add-btn" href="{{ route('admin.campaigns.add') }}">
            <i class="icon-plus"></i> New Campaign
        </a>
    </div>

    {{-- Flash --}}
    @if(session('status'))
        <div class="alert alert-success mb-20" style="font-size:13px;">
            <i class="icon-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    <div class="wg-box">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap10 flex-wrap mb-20">
            <div class="cmp-search-wrap">
                <form method="GET">
                    <i class="icon-search"></i>
                    <input class="cmp-search" type="text" name="search"
                           value="{{ request('search') }}" placeholder="Search campaigns…">
                </form>
            </div>
            <div style="font-size:12px;color:#9ca3af;">
                {{ $campaigns->total() }} campaign{{ $campaigns->total() != 1 ? 's' : '' }}
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="cmp-table">
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Campaign</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $c)
                    <tr>
                        {{-- ID --}}
                        <td><span class="cmp-id">{{ $c->id }}</span></td>

                        {{-- Name + slug --}}
                        <td>
                            <a class="cmp-name"
                               href="{{ route('campaign.details', $c->slug) }}"
                               target="_blank">
                                {{ $c->name }}
                            </a>
                            <div class="cmp-slug">/{{ $c->slug }}</div>
                        </td>

                        {{-- Template --}}
                        <td>
                            @if($c->view_file)
                                <span class="cmp-tmpl">{{ basename(str_replace('.', '/', $c->view_file)) }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:12px;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <button type="button"
                                    class="cmp-status cmp-status-toggle {{ $c->status == 1 ? 'published' : 'unpublished' }}"
                                    data-id="{{ $c->id }}"
                                    data-url="{{ route('admin.campaigns.toggle-status', $c->id) }}"
                                    title="Click to toggle status">
                                <span class="dot"></span>
                                <span class="cmp-status-label">{{ $c->status == 1 ? 'Published' : 'Draft' }}</span>
                            </button>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span class="cmp-date">{{ $c->created_at?->format('d M Y') }}</span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="cmp-actions">

                               
                                {{-- Dropdown: more --}}
                                <div class="cmp-dd">
                                    <button class="cmp-btn js-cmp-dd-toggle" title="More options" type="button">
                                        <i class="icon-more-vertical"></i>
                                    </button>
                                    <div class="cmp-dd-menu">

                                        <a class="cmp-dd-item"
                                           href="{{ route('campaign.details', $c->slug) }}"
                                           target="_blank" rel="noopener">
                                            <i class="icon-external-link"></i> View Page
                                        </a>

                                        <a class="cmp-dd-item"
                                           href="{{ route('admin.campaigns.edit', $c->id) }}">
                                            <i class="icon-settings"></i> Campaign Settings
                                        </a>

                                        <a class="cmp-dd-item"
                                           href="{{ route('admin.campaigns.landingpage.edit', $c->id) }}">
                                            <i class="icon-layout"></i> Edit Landing Page
                                        </a>

                                        <div class="cmp-dd-divider"></div>

                                        <form action="{{ route('admin.campaigns.copy', $c->id) }}"
                                              method="POST" class="js-copy-form">
                                            @csrf
                                            <button type="submit" class="cmp-dd-item">
                                                <i class="icon-copy"></i> Duplicate
                                            </button>
                                        </form>

                                        <div class="cmp-dd-divider"></div>

                                        <form action="{{ route('admin.campaigns.delete', $c->id) }}"
                                              method="POST" class="js-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="cmp-dd-item danger js-delete-btn"
                                                    data-name="{{ $c->name }}">
                                                <i class="icon-trash-2"></i> Delete
                                            </button>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="cmp-empty">
                                <i class="icon-inbox"></i>
                                <p>No campaigns yet. <a href="{{ route('admin.campaigns.add') }}">Create one →</a></p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($campaigns->hasPages())
        <div class="divider"></div>
        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
            {{ $campaigns->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
// ── Dropdown toggle ─────────────────────────────────────────────────────────
document.querySelectorAll('.js-cmp-dd-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        const menu = btn.nextElementSibling;
        const isOpen = menu.classList.contains('show');
        // close all
        document.querySelectorAll('.cmp-dd-menu.show').forEach(m => m.classList.remove('show'));
        if (!isOpen) menu.classList.add('show');
    });
});
document.addEventListener('click', () => {
    document.querySelectorAll('.cmp-dd-menu.show').forEach(m => m.classList.remove('show'));
});

// ── Status toggle ───────────────────────────────────────────────────────────
document.querySelectorAll('.cmp-status-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.classList.add('loading');
        fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            const isPublished = data.status == 1;
            btn.classList.remove('published', 'unpublished');
            btn.classList.add(isPublished ? 'published' : 'unpublished');
            btn.querySelector('.cmp-status-label').textContent = isPublished ? 'Published' : 'Draft';
        })
        .catch(() => {})
        .finally(() => btn.classList.remove('loading'));
    });
});

// ── Delete confirm ───────────────────────────────────────────────────────────
document.querySelectorAll('.js-delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const name = btn.dataset.name;
        if (confirm('Delete "' + name + '"? This cannot be undone.')) {
            btn.closest('.js-delete-form').submit();
        }
    });
});
</script>
@endpush
