<div>
    {{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
    <div class="wg-box mb-3">
        <div class="row gy-2 align-items-center">

            {{-- Search --}}
            <div class="col-md-5">
                <form class="form-search" onsubmit="return false;">
                    <fieldset class="name">
                        <input
                            wire:model.live.debounce.400ms="search"
                            type="text"
                            placeholder="Search by file name or caption…"
                            class=""
                        >
                    </fieldset>
                    <div class="button-submit">
                        <button type="button"><i class="icon-search"></i></button>
                    </div>
                </form>
            </div>

            {{-- Type filter --}}
            <div class="col-md-3">
                <select wire:model.live="typeFilter" class="form-control">
                    <option value="">All types</option>
                    <option value="image">Images</option>
                    <option value="video">Videos</option>
                    <option value="document">Documents</option>
                    <option value="audio">Audio</option>
                    <option value="archive">Archives</option>
                    <option value="other">Other</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-md-4 d-flex gap-2 justify-content-md-end flex-wrap">
                @if (!empty($selected))
                    <button
                        type="button"
                        wire:click="bulkDelete"
                        wire:confirm="Delete {{ count($selected) }} selected item(s)? This cannot be undone."
                        class="tf-button style-1"
                        style="background:#dc3545; border-color:#dc3545;"
                    >
                        <i class="icon-trash"></i> Delete ({{ count($selected) }})
                    </button>
                    <button type="button" wire:click="clearSelection" class="tf-button style-1">
                        <i class="icon-x"></i> Clear
                    </button>
                @endif
                <button type="button" wire:click="selectAllVisible" class="tf-button style-1">
                    <i class="icon-check-square"></i> Select page
                </button>
            </div>

        </div>
    </div>

    {{-- ── Stats bar ───────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap10 mb-3" style="font-size:13px; color:#6b7280;">
        <span>Total: <strong>{{ $totalCount }}</strong></span>
        @if (!empty($selected))
            &nbsp;·&nbsp; <span style="color:#2377FC;"><strong>{{ count($selected) }}</strong> selected</span>
        @endif
    </div>

    {{-- ── Grid ───────────────────────────────────────────────────────────── --}}
    <div class="wg-box">
        @if ($mediaItems->isEmpty())
            <div class="text-center py-5">
                <i class="icon-image" style="font-size:48px; color:#d1d5db; display:block; margin-bottom:12px;"></i>
                <div class="body-title" style="color:#9ca3af;">No media found</div>
                @if ($search || $typeFilter)
                    <div class="text-tiny mt-1">Try adjusting your filters</div>
                @endif
            </div>
        @else
            <div class="row g-3" id="media-grid">
                @foreach ($mediaItems as $item)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div
                            class="media-card"
                            style="
                                border: 2px solid {{ in_array($item->id, $selected) ? '#2377FC' : '#e5e7eb' }};
                                border-radius: 8px;
                                overflow: hidden;
                                position: relative;
                                background: #fff;
                                transition: border-color .15s, box-shadow .15s;
                                cursor: pointer;
                            "
                            wire:click="toggleSelect({{ $item->id }})"
                        >
                            {{-- Selection tick --}}
                            @if (in_array($item->id, $selected))
                                <div style="
                                    position:absolute; top:6px; left:6px; z-index:2;
                                    width:20px; height:20px; background:#2377FC;
                                    border-radius:50%; display:flex; align-items:center; justify-content:center;
                                ">
                                    <i class="icon-check" style="color:#fff; font-size:11px;"></i>
                                </div>
                            @endif

                            {{-- Delete button --}}
                            <button
                                type="button"
                                wire:click.stop="deleteOne({{ $item->id }})"
                                wire:confirm="Delete '{{ $item->original_name }}'?"
                                style="
                                    position:absolute; top:6px; right:6px; z-index:2;
                                    background:rgba(220,53,69,.85); color:#fff; border:none;
                                    border-radius:4px; padding:2px 6px; font-size:11px; cursor:pointer;
                                "
                                title="Delete"
                            >
                                <i class="icon-trash"></i>
                            </button>

                            {{-- Thumbnail / icon --}}
                            @if ($item->isImage())
                                <div style="height:120px; overflow:hidden; background:#f3f4f6;">
                                    <img
                                        src="{{ $item->getThumbnailUrl() }}"
                                        alt="{{ $item->original_name }}"
                                        style="width:100%; height:100%; object-fit:cover;"
                                        loading="lazy"
                                    >
                                </div>
                            @elseif ($item->isVideo())
                                <div style="height:120px; background:#1e293b; display:flex; align-items:center; justify-content:center;">
                                    <i class="icon-play-circle" style="font-size:40px; color:#fff;"></i>
                                </div>
                            @else
                                <div style="height:120px; background:#f8fafc; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:4px;">
                                    @php
                                        $iconMap = ['document'=>'icon-file-text','audio'=>'icon-music','archive'=>'icon-package'];
                                        $icon = $iconMap[$item->type] ?? 'icon-file';
                                    @endphp
                                    <i class="{{ $icon }}" style="font-size:36px; color:#9ca3af;"></i>
                                    <span class="text-tiny" style="color:#9ca3af; text-transform:uppercase;">{{ $item->extension }}</span>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div style="padding:8px;">
                                <div
                                    class="text-tiny"
                                    style="overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-weight:600;"
                                    title="{{ $item->original_name }}"
                                >
                                    {{ $item->original_name }}
                                </div>
                                <div class="text-tiny" style="color:#9ca3af;">
                                    {{ $item->readableSize() }}
                                    &nbsp;·&nbsp;
                                    {{ $item->created_at->format('M d') }}
                                </div>

                                {{-- Copy URL dropdown --}}
                                <div style="position:relative;" onclick="event.stopPropagation()">
                                    <button
                                        type="button"
                                        onclick="toggleUrlMenu(this)"
                                        class="tf-button style-1 w-100 mt-1"
                                        style="padding:2px 6px; font-size:11px; display:flex; align-items:center; justify-content:center; gap:4px;"
                                    >
                                        <i class="icon-copy"></i> Copy URL <span style="font-size:9px;">▼</span>
                                    </button>

                                    <div class="url-menu" style="display:none; position:absolute; bottom:calc(100% + 4px); left:0; right:0; z-index:50;
                                        background:#fff; border:1px solid #e5e7eb; border-radius:6px; box-shadow:0 4px 14px rgba(0,0,0,.12); overflow:hidden;">

                                        {{-- Original --}}
                                        <button type="button" onclick="copyMediaUrl('{{ $item->getUrl() }}', this)"
                                            class="url-menu-item" style="display:flex;width:100%;border:none;background:none;padding:6px 10px;font-size:11px;cursor:pointer;align-items:center;gap:6px;text-align:left;">
                                            <i class="icon-copy" style="color:#6b7280;flex-shrink:0;"></i>
                                            <span style="flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">Original</span>
                                        </button>

                                        {{-- Variants --}}
                                        @foreach($item->variants->sortBy('ratio') as $variant)
                                            <button type="button" onclick="copyMediaUrl('{{ $variant->getUrl() }}', this)"
                                                class="url-menu-item" style="display:flex;width:100%;border:none;background:none;padding:6px 10px;font-size:11px;cursor:pointer;align-items:center;gap:6px;text-align:left;border-top:1px solid #f3f4f6;">
                                                <i class="icon-copy" style="color:#6b7280;flex-shrink:0;"></i>
                                                <span style="flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ ucfirst($variant->ratio) }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="divider mt-4"></div>
            <div class="flex items-center justify-between flex-wrap gap10 mt-3 wgp-pagination">
                {{ $mediaItems->links() }}
            </div>
        @endif
    </div>

</div>

@once
<script>
    function copyMediaUrl(url, btn) {
        navigator.clipboard.writeText(url).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="icon-check"></i> Copied!';
            btn.style.background = '#d1fae5';
            setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; }, 2000);
        });
    }

    function toggleUrlMenu(btn) {
        const menu = btn.nextElementSibling;
        const isOpen = menu.style.display === 'block';
        // close all open menus
        document.querySelectorAll('.url-menu').forEach(m => m.style.display = 'none');
        menu.style.display = isOpen ? 'none' : 'block';
    }

    // close menus on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.url-menu').forEach(m => m.style.display = 'none');
    });

    // hover highlight for menu items
    document.addEventListener('mouseover', e => {
        if (e.target.closest('.url-menu-item')) {
            e.target.closest('.url-menu-item').style.background = '#f3f4f6';
        }
    });
    document.addEventListener('mouseout', e => {
        if (e.target.closest('.url-menu-item')) {
            e.target.closest('.url-menu-item').style.background = '';
        }
    });
</script>
@endonce
