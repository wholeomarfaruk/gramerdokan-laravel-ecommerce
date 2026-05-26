@extends('layouts.admin')

@push('styles')
<style>
/* ── Tree container ─────────────────────────────── */
.cat-tree { margin: 0; padding: 0; }

/* ── Row ────────────────────────────────────────── */
.cat-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    background: #fff;
    transition: background .15s;
    min-height: 52px;
}
.cat-row:hover { background: #f8f9fb; }

/* depth-specific left accent */
.cat-depth-0 > .cat-row { border-left: 3px solid #2377FC; }
.cat-depth-1 > .cat-row { border-left: 3px solid #7c3aed; }
.cat-depth-2 > .cat-row { border-left: 3px solid #059669; }
.cat-depth-3 > .cat-row { border-left: 3px solid #d97706; }

/* ── Toggle caret ───────────────────────────────── */
.cat-toggle {
    background: none;
    border: none;
    padding: 0;
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: #6c757d;
    cursor: pointer;
    transition: background .15s;
}
.cat-toggle:hover { background: #e9ecef; color: #212529; }
.cat-toggle .cat-caret {
    transition: transform .22s ease;
    display: inline-block;
    font-size: 13px;
}
.cat-toggle:not(.collapsed) .cat-caret { transform: rotate(90deg); }
.cat-no-toggle { width: 24px; flex-shrink: 0; }

/* ── Thumbnail ──────────────────────────────────── */
.cat-thumb {
    width: 38px;
    height: 38px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    flex-shrink: 0;
    background: #f1f3f5;
}
.cat-thumb-placeholder {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: #f1f3f5;
    border: 1px solid #e9ecef;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 16px;
}

/* ── Name block ─────────────────────────────────── */
.cat-info { flex: 1; min-width: 0; }
.cat-name {
    font-weight: 600;
    font-size: 14px;
    color: #212529;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cat-slug {
    font-size: 11px;
    color: #9ca3af;
    display: block;
}

/* ── Badges ─────────────────────────────────────── */
.cat-badges { display: flex; gap: 5px; flex-shrink: 0; align-items: center; }

/* ── Actions ─────────────────────────────────────── */
.cat-actions { display: flex; gap: 5px; flex-shrink: 0; }
.cat-actions .btn {
    padding: 3px 8px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 5px;
}

/* ── Children wrapper ───────────────────────────── */
.cat-children {
    padding-left: 32px;
    padding-top: 6px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

/* ── Root-level spacing ─────────────────────────── */
.cat-tree > .cat-node { margin-bottom: 6px; }

/* ── Expand/Collapse all btn ─────────────────────── */
.tree-toolbar { display: flex; gap: 8px; align-items: center; }
</style>
@endpush

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Categories</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Categories</div></li>
            </ul>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats + toolbar --}}
        <div class="wg-box mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-center">
                        <div style="font-size:22px;font-weight:700;color:#2377FC;">{{ $categories->count() }}</div>
                        <div class="text-tiny text-muted">Root</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e9ecef;"></div>
                    <div class="text-center">
                        <div style="font-size:22px;font-weight:700;color:#7c3aed;">{{ $totalCount }}</div>
                        <div class="text-tiny text-muted">Total</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e9ecef;"></div>
                    <div class="tree-toolbar">
                        <button id="expandAll" class="btn btn-sm btn-outline-secondary">
                            <i class="icon-chevron-down"></i> Expand All
                        </button>
                        <button id="collapseAll" class="btn btn-sm btn-outline-secondary">
                            <i class="icon-chevron-right"></i> Collapse All
                        </button>
                    </div>
                </div>
                <a class="tf-button style-1" href="{{ route('admin.categories.add') }}">
                    <i class="icon-plus"></i> Add Category
                </a>
            </div>
        </div>

        {{-- Tree --}}
        <div class="wg-box">
            @if ($categories->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="icon-layers" style="font-size:40px;opacity:.3;"></i>
                    <p class="mt-2">No categories yet. <a href="{{ route('admin.categories.add') }}">Add one</a>.</p>
                </div>
            @else
                <div class="cat-tree" id="categoryTree">
                    @foreach ($categories as $category)
                        @include('admin.category.tree', [
                            'category' => $category,
                            'depth'    => 0,
                        ])
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Expand / Collapse all
document.getElementById('expandAll')?.addEventListener('click', () => {
    document.querySelectorAll('#categoryTree .collapse').forEach(el => {
        bootstrap.Collapse.getOrCreateInstance(el).show();
    });
    document.querySelectorAll('#categoryTree .cat-toggle').forEach(btn => {
        btn.classList.remove('collapsed');
    });
});
document.getElementById('collapseAll')?.addEventListener('click', () => {
    document.querySelectorAll('#categoryTree .collapse').forEach(el => {
        bootstrap.Collapse.getOrCreateInstance(el).hide();
    });
    document.querySelectorAll('#categoryTree .cat-toggle').forEach(btn => {
        btn.classList.add('collapsed');
    });
});

// Delete confirm
document.querySelectorAll('.cat-delete').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const form = btn.closest('form');
        Swal.fire({
            title: 'Delete category?',
            text: "This cannot be undone. Child categories will also be affected.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#718096',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
