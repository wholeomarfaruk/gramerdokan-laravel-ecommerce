@extends('layouts.admin')

@section('content')
<style>
/* ─── Design tokens ─────────────────────────────────────────────────── */
.lpe-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:7px;font-size:13px;font-weight:600;border:1.5px solid transparent;cursor:pointer;text-decoration:none;transition:.15s; }
.lpe-btn.primary  { background:#2377FC;border-color:#2377FC;color:#fff; }
.lpe-btn.primary:hover  { background:#1a5fd8;color:#fff; }
.lpe-btn.secondary{ background:#fff;border-color:#e5e7eb;color:#374151; }
.lpe-btn.secondary:hover{ background:#f3f4f6;color:#111827; }
.lpe-btn.sm { padding:5px 12px;font-size:12px; }

/* ─── Left Nav ──────────────────────────────────────────────────────── */
.lpe-nav { background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden; }
.lpe-nav-head { padding:12px 16px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.6px;border-bottom:1px solid #f3f4f6; }
.lpe-nav-item { display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;color:#374151;text-decoration:none;border-left:3px solid transparent;transition:.12s; }
.lpe-nav-item:hover { background:#f9fafb;color:#111827; }
.lpe-nav-item.active { background:#eff6ff;color:#2377FC;border-left-color:#2377FC;font-weight:600; }
.lpe-nav-item i { font-size:14px;color:#9ca3af;flex-shrink:0; }
.lpe-nav-item.active i { color:#2377FC; }
.lpe-nav-badge { margin-left:auto;background:#f3f4f6;color:#6b7280;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:600; }

/* ─── Section Cards ─────────────────────────────────────────────────── */
.lpe-card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:20px;overflow:hidden; }
.lpe-card-header { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;cursor:pointer;border-bottom:1px solid #f3f4f6;user-select:none; }
.lpe-card-header:hover { background:#fafafa; }
.lpe-card-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0; }
.lpe-card-title { font-size:14px;font-weight:700;color:#111827;margin-bottom:1px; }
.lpe-card-sub { font-size:11px;color:#9ca3af; }
.lpe-chevron { transition:transform .2s;color:#9ca3af; }
.lpe-chevron.rotated { transform:rotate(-90deg); }
.lpe-card-body { padding:20px; }

/* ─── Form fields ───────────────────────────────────────────────────── */
.lpe-field { margin-bottom:0; }
.lpe-label { font-size:11.5px;font-weight:600;color:#374151;margin-bottom:5px;display:block; }
.lpe-input, .lpe-textarea { width:100%;border:1.5px solid #e5e7eb;border-radius:6px;padding:7px 11px;font-size:13px;color:#111827;background:#fff;transition:.12s;outline:none; }
.lpe-input:focus, .lpe-textarea:focus { border-color:#2377FC;box-shadow:0 0 0 3px rgba(35,119,252,.08); }
.lpe-textarea { resize:vertical;min-height:72px; }
.lpe-input::placeholder, .lpe-textarea::placeholder { color:#c4c9d4; }
.lpe-img-preview { display:block;margin-top:6px;width:100%;max-height:80px;object-fit:cover;border-radius:5px;border:1px solid #e5e7eb; }

/* ─── Item mini-cards (for array sections) ──────────────────────────── */
.lpe-item-card { border:1.5px solid #e5e7eb;border-radius:8px;padding:14px;background:#fafafa;position:relative; }
.lpe-item-num { position:absolute;top:-1px;left:-1px;background:#2377FC;color:#fff;font-size:10px;font-weight:700;border-radius:7px 0 6px 0;padding:2px 8px;line-height:1.6; }
.lpe-item-card .row { margin-top:4px; }

/* ─── Color accents per section ─────────────────────────────────────── */
.lpe-accent-seo       { background:#eff6ff; color:#2377FC; }
.lpe-accent-nav       { background:#f0fdf4; color:#059669; }
.lpe-accent-features  { background:#fff7ed; color:#d97706; }
.lpe-accent-hero      { background:#fdf4ff; color:#9333ea; }
.lpe-accent-contact   { background:#f0fdf4; color:#16a34a; }
.lpe-accent-videos    { background:#fff7ed; color:#ea580c; }
.lpe-accent-strip_images { background:#fff1f2; color:#e11d48; }
.lpe-accent-testimonials { background:#fefce8; color:#ca8a04; }
.lpe-accent-packages  { background:#f0f9ff; color:#0284c7; }
.lpe-accent-footer    { background:#f8fafc; color:#475569; }

/* ─── Misc ──────────────────────────────────────────────────────────── */
.lpe-divider { height:1px;background:#f3f4f6;margin:16px 0; }
.lpe-save-bar { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px; }
</style>

<div class="main-content-inner">
<div class="main-content-wrap">

    @php
        $sectionIcons = [
            'seo'          => 'icon-search',
            'nav'          => 'icon-menu',
            'hero'         => 'icon-layout',
            'contact'      => 'icon-phone',
            'features'     => 'icon-star',
            'videos'       => 'icon-play-circle',
            'strip_images' => 'icon-image',
            'testimonials' => 'icon-star',
            'packages'     => 'icon-package',
            'footer'       => 'icon-sidebar',
        ];
        $imageKeys = ['src','logo_url','bg_url','meta_image','favicon_url','image_url','photo_url','img_url','banner_url','image','thumbnail','thumbnail2'];
    @endphp

    {{-- ── Page Header ───────────────────────────────────────────────── --}}
    <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <div>
            <h3 class="mb-1">Edit Landing Page</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Campaigns</div></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">{{ $campaign->name ?? 'Edit Landing Page' }}</div></li>
            </ul>
        </div>
        <div class="flex gap10 flex-wrap">
            <a href="{{ route('admin.campaigns') }}" class="lpe-btn secondary">
                <i class="icon-arrow-left"></i> Back
            </a>
            <form action="{{ route('admin.campaigns.landingpage.sync', $campaign->id) }}"
                  method="POST" style="display:inline;"
                  onsubmit="return confirm('Sync fields from the source template?\n\nYour existing data will be preserved. New blank fields will be added.');">
                @csrf
                <button type="submit" class="lpe-btn secondary"
                        title="Pull new fields from the source template (preserves existing data)">
                    <i class="icon-refresh-cw"></i> Sync Template
                </button>
            </form>
            <button form="landingPageForm" type="submit" class="lpe-btn primary">
                <i class="icon-save"></i> Save Changes
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li style="font-size:13px;">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('sync_error'))
        <div class="alert alert-danger mb-4" style="font-size:13px;">
            <i class="icon-alert-circle" style="margin-right:6px;"></i>{{ session('sync_error') }}
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success mb-4" style="font-size:13px;">
            <i class="icon-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    <form id="landingPageForm" action="{{ route('admin.campaigns.landingpage.update', $campaign->id) }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ════ Left: Sticky Section Nav ════ --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="lpe-nav" style="position:sticky;top:76px;">
                    <div class="lpe-nav-head">Sections</div>
                    @foreach($page->edit_sections as $sectionKey => $section)
                        @php
                            $fieldCount = is_array($section->fields) ? count($section->fields) : count((array)$section->fields);
                        @endphp
                        <a href="#section-{{ $sectionKey }}" class="lpe-nav-item js-nav-link">
                            <i class="{{ $sectionIcons[$sectionKey] ?? 'icon-settings' }}"></i>
                            {{ $section->label ?? ucfirst($sectionKey) }}
                            <span class="lpe-nav-badge">{{ $fieldCount }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ════ Right: Section Forms ════ --}}
            <div class="col-lg-9">

                @foreach($page->edit_sections as $sectionKey => $section)
                    @php
                        $accentClass  = 'lpe-accent-' . $sectionKey;
                        $iconClass    = $sectionIcons[$sectionKey] ?? 'icon-settings';
                        $isArrayFields = is_array($section->fields);
                        $fieldCount   = $isArrayFields ? count($section->fields) : count((array)$section->fields);
                        $startOpen    = in_array($sectionKey, ['seo','hero','contact']);
                    @endphp

                    <div id="section-{{ $sectionKey }}" class="lpe-card lpe-section-anchor">

                        {{-- Section Header --}}
                        <div class="lpe-card-header" data-bs-toggle="collapse"
                             data-bs-target="#body-{{ $sectionKey }}"
                             aria-expanded="{{ $startOpen ? 'true' : 'false' }}">
                            <div class="flex items-center gap10">
                                <div class="lpe-card-icon {{ $accentClass }}">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                <div>
                                    <div class="lpe-card-title">{{ $section->label ?? ucfirst($sectionKey) }}</div>
                                    <div class="lpe-card-sub">{{ $fieldCount }} {{ $isArrayFields ? 'items' : 'fields' }}</div>
                                </div>
                            </div>
                            <i class="icon-chevron-down lpe-chevron {{ $startOpen ? '' : 'rotated' }}"></i>
                        </div>

                        {{-- Section Body --}}
                        <div class="collapse {{ $startOpen ? 'show' : '' }}" id="body-{{ $sectionKey }}">
                            <div class="lpe-card-body">

                                @if($isArrayFields)
                                    {{-- ── Array section: group fields by item index ─── --}}
                                    @php
                                        $groups = [];
                                        foreach($section->fields as $field) {
                                            preg_match('/\.(\d+)\./', $field->key ?? '', $m);
                                            $idx = isset($m[1]) ? (int)$m[1] : 0;
                                            $groups[$idx][] = $field;
                                        }
                                        $fieldsPerGroup = count(reset($groups) ?: []);
                                        $colClass = $fieldsPerGroup <= 2 ? 'col-sm-6 col-md-4' : 'col-sm-6';
                                    @endphp

                                    <div class="row g-3">
                                        @foreach($groups as $idx => $groupFields)
                                            <div class="{{ $colClass }}">
                                                <div class="lpe-item-card">
                                                    <div class="lpe-item-num">{{ $idx + 1 }}</div>
                                                    @foreach($groupFields as $field)
                                                        @php
                                                            $value      = getSectionValue($page->sections, $field->key);
                                                            $lastSeg    = last(explode('.', $field->key ?? ''));
                                                            $isImgField = in_array($lastSeg, $imageKeys);
                                                            $cleanLabel = preg_replace('/^[^—]+— /', '', $field->label ?? '') ?: ($field->label ?? $lastSeg);
                                                        @endphp
                                                        <div class="lpe-field mt-2">
                                                            <label class="lpe-label">{{ $cleanLabel }}</label>
                                                            @if(($field->type ?? 'text') === 'textarea')
                                                                <textarea name="fields[{{ $field->key }} ]"
                                                                          class="lpe-textarea"
                                                                          rows="2"
                                                                          placeholder="{{ $field->placeholder ?? '' }}">{{ old('fields.' . $field->key, $value) }}</textarea>
                                                            @else
                                                                <input type="text"
                                                                       name="fields[{{ $field->key }} ]"
                                                                       class="lpe-input"
                                                                       value="{{ old('fields.' . $field->key, $value) }}"
                                                                       placeholder="{{ $field->placeholder ?? '' }}"
                                                                       @if($isImgField) oninput="lpePreview(this)" @endif>
                                                                @if($isImgField && $value)
                                                                    <img class="lpe-img-preview"
                                                                         src="{{ $value }}"
                                                                         alt=""
                                                                         onerror="this.style.display='none'">
                                                                @elseif($isImgField)
                                                                    <img class="lpe-img-preview" style="display:none;" src="" alt="">
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                @else
                                    {{-- ── Object section: flat fields in responsive grid ── --}}
                                    <div class="row g-3">
                                        @foreach($section->fields as $key => $field)
                                            @if(isset($field->fields) && count((array)$field->fields) > 0)
                                                {{-- Nested sub-fields (legacy support) --}}
                                                @foreach($field->fields as $subField)
                                                    @php
                                                        $subValue   = getSectionValue($page->sections, $subField->key ?? '');
                                                        $lastSeg    = last(explode('.', $subField->key ?? ''));
                                                        $isImgField = in_array($lastSeg, $imageKeys);
                                                        $isWide     = ($subField->type ?? 'text') === 'textarea';
                                                    @endphp
                                                    <div class="{{ $isWide ? 'col-12' : 'col-md-6' }}">
                                                        <div class="lpe-field">
                                                            <label class="lpe-label">{{ $subField->label ?? $key }}</label>
                                                            @if($isWide)
                                                                <textarea name="fields[{{ $subField->key }} ]"
                                                                          class="lpe-textarea"
                                                                          rows="3"
                                                                          placeholder="{{ $subField->placeholder ?? '' }}">{{ old('fields.' . $subField->key, $subValue) }}</textarea>
                                                            @else
                                                                <input type="text"
                                                                       name="fields[{{ $subField->key }} ]"
                                                                       class="lpe-input"
                                                                       value="{{ old('fields.' . $subField->key, $subValue) }}"
                                                                       placeholder="{{ $subField->placeholder ?? '' }}"
                                                                       @if($isImgField) oninput="lpePreview(this)" @endif>
                                                                @if($isImgField && $subValue)
                                                                    <img class="lpe-img-preview" src="{{ $subValue }}" alt="" onerror="this.style.display='none'">
                                                                @elseif($isImgField)
                                                                    <img class="lpe-img-preview" style="display:none;" src="" alt="">
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                {{-- Regular flat field --}}
                                                @php
                                                    $value      = getSectionValue($page->sections, $field->key ?? '');
                                                    $lastSeg    = last(explode('.', $field->key ?? ''));
                                                    $isImgField = in_array($lastSeg, $imageKeys);
                                                    $isWide     = ($field->type ?? 'text') === 'textarea';
                                                @endphp
                                                <div class="{{ $isWide ? 'col-12' : 'col-md-6' }}">
                                                    <div class="lpe-field">
                                                        <label class="lpe-label">{{ $field->label ?? $key }}</label>
                                                        @if($isWide)
                                                            <textarea name="fields[{{ $field->key }} ]"
                                                                      class="lpe-textarea"
                                                                      rows="3"
                                                                      placeholder="{{ $field->placeholder ?? '' }}">{{ old('fields.' . $field->key, $value) }}</textarea>
                                                        @else
                                                            <input type="text"
                                                                   name="fields[{{ $field->key }} ]"
                                                                   class="lpe-input"
                                                                   value="{{ old('fields.' . $field->key, $value) }}"
                                                                   placeholder="{{ $field->placeholder ?? '' }}"
                                                                   @if($isImgField) oninput="lpePreview(this)" @endif>
                                                            @if($isImgField && $value)
                                                                <img class="lpe-img-preview" src="{{ $value }}" alt="" onerror="this.style.display='none'">
                                                            @elseif($isImgField)
                                                                <img class="lpe-img-preview" style="display:none;" src="" alt="">
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- ── Save Bar ───────────────────────────────────── --}}
                <div class="lpe-save-bar">
                    <div style="font-size:13px;color:#6b7280;">
                        <i class="icon-info" style="margin-right:4px;"></i>
                        All sections are saved together.
                    </div>
                    <button type="submit" class="lpe-btn primary">
                        <i class="icon-save"></i> Save All Changes
                    </button>
                </div>

            </div>{{-- /col-lg-9 --}}
        </div>{{-- /row --}}
    </form>

</div>
</div>

@push('scripts')
<script>
// ── Image preview on URL input change ─────────────────────────────────
function lpePreview(input) {
    const img = input.parentElement.querySelector('.lpe-img-preview');
    if (!img) return;
    const val = input.value.trim();
    img.src = val;
    img.style.display = val ? 'block' : 'none';
}

// ── Collapse chevron rotation ──────────────────────────────────────────
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(header => {
    const collapseEl = document.querySelector(header.dataset.bsTarget);
    if (!collapseEl) return;
    collapseEl.addEventListener('hide.bs.collapse', () => {
        header.querySelector('.lpe-chevron')?.classList.add('rotated');
        header.setAttribute('aria-expanded', 'false');
    });
    collapseEl.addEventListener('show.bs.collapse', () => {
        header.querySelector('.lpe-chevron')?.classList.remove('rotated');
        header.setAttribute('aria-expanded', 'true');
    });
});

// ── Sticky nav: smooth scroll + active highlight ───────────────────────
document.querySelectorAll('.js-nav-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        const target   = document.querySelector(targetId);
        if (!target) return;

        // Expand section if collapsed
        const collapseEl = target.querySelector('.collapse');
        if (collapseEl && !collapseEl.classList.contains('show')) {
            bootstrap.Collapse.getOrCreateInstance(collapseEl).show();
        }

        setTimeout(() => {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, collapseEl && !collapseEl.classList.contains('show') ? 280 : 0);

        document.querySelectorAll('.js-nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    });
});

// ── IntersectionObserver: highlight nav item when section in view ──────
const sections = document.querySelectorAll('.lpe-section-anchor');
const navLinks  = document.querySelectorAll('.js-nav-link');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            navLinks.forEach(l => l.classList.remove('active'));
            const active = document.querySelector(`.js-nav-link[href="#${entry.target.id}"]`);
            if (active) active.classList.add('active');
        }
    });
}, { rootMargin: '-10% 0px -80% 0px' });

sections.forEach(s => observer.observe(s));
</script>
@endpush

@endsection
