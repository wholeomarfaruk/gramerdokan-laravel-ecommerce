{{--
    MediaPicker — reusable picker modal.

    Usage in any admin page:
        @livewire('admin.media.media-picker')

    Open via Livewire event:
        Livewire.dispatch('open-media-picker', { multiple: false, callbackKey: 'featured' })

    Listen for confirmation:
        window.addEventListener('media-picker-confirmed', e => {
            const { callbackKey, single, media } = e.detail[0] ?? e.detail;
            if (callbackKey === 'featured') {
                document.getElementById('featured_image').value = single.id;
                document.getElementById('featured_preview').src  = single.thumbnail;
            }
        });
--}}

<div
    x-data="{ open: @entangle('isOpen').live }"
    x-cloak
>
    {{-- ── Backdrop ──────────────────────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1040;"
        @click="$wire.close()"
    ></div>

    {{-- ── Modal panel ───────────────────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="
            position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
            z-index:1050; width:min(94vw,1000px); max-height:88vh;
            background:#fff; border-radius:12px; display:flex; flex-direction:column;
            box-shadow:0 20px 60px rgba(0,0,0,.25);
        "
    >
        {{-- Header --}}
        <div style="padding:14px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div>
                <div class="body-title">Media Library</div>
                <div class="text-tiny" style="color:#9ca3af;">
                    {{ $multiple ? 'Select one or more files' : 'Select a file' }}
                    @if (!empty($selected))
                        &nbsp;·&nbsp; <strong>{{ count($selected) }}</strong> selected
                    @endif
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                {{-- Tab switcher --}}
                <div style="display:flex; background:#f3f4f6; border-radius:8px; padding:3px; gap:2px;">
                    <button type="button" wire:click="switchTab('browse')"
                        style="padding:5px 14px; border-radius:6px; border:none; font-size:12px; font-weight:600; cursor:pointer;
                               background: {{ $activeTab === 'browse' ? '#fff' : 'transparent' }};
                               color: {{ $activeTab === 'browse' ? '#111827' : '#6b7280' }};
                               box-shadow: {{ $activeTab === 'browse' ? '0 1px 3px rgba(0,0,0,.1)' : 'none' }};
                               transition:.15s;">
                        <i class="icon-grid" style="margin-right:4px;"></i> Browse
                    </button>
                    <button type="button" wire:click="switchTab('upload')"
                        style="padding:5px 14px; border-radius:6px; border:none; font-size:12px; font-weight:600; cursor:pointer;
                               background: {{ $activeTab === 'upload' ? '#fff' : 'transparent' }};
                               color: {{ $activeTab === 'upload' ? '#111827' : '#6b7280' }};
                               box-shadow: {{ $activeTab === 'upload' ? '0 1px 3px rgba(0,0,0,.1)' : 'none' }};
                               transition:.15s;">
                        <i class="icon-upload" style="margin-right:4px;"></i> Upload
                    </button>
                </div>
                <button type="button" wire:click="close"
                    style="background:none;border:none;font-size:20px;cursor:pointer;color:#6b7280;line-height:1;"
                    aria-label="Close">&times;</button>
            </div>
        </div>

        {{-- ══ BROWSE TAB ══════════════════════════════════════════════════════ --}}
        @if ($activeTab === 'browse')

        {{-- Filters --}}
        <div style="padding:10px 20px; border-bottom:1px solid #e5e7eb; display:flex; gap:10px; flex-wrap:wrap;">
            <div class="form-search" style="flex:1; min-width:180px;">
                <fieldset class="name">
                    <input
                        wire:model.live.debounce.400ms="search"
                        type="text"
                        placeholder="Search files…"
                    >
                </fieldset>
            </div>
            <select wire:model.live="typeFilter" class="form-control" style="width:140px;">
                <option value="">All types</option>
                <option value="image">Images</option>
                <option value="video">Videos</option>
                <option value="document">Documents</option>
                <option value="audio">Audio</option>
                <option value="archive">Archives</option>
            </select>
        </div>

        {{-- Grid (scrollable) --}}
        <div style="flex:1; overflow-y:auto; padding:16px 20px;">
            @if ($mediaItems->isEmpty())
                <div class="text-center py-5">
                    <i class="icon-image" style="font-size:42px; color:#d1d5db; display:block; margin-bottom:10px;"></i>
                    <div class="text-tiny" style="color:#9ca3af; margin-bottom:12px;">No media found</div>
                    <button type="button" wire:click="switchTab('upload')"
                            style="background:#2377FC;color:#fff;border:none;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="icon-upload"></i> Upload files
                    </button>
                </div>
            @else
                <div class="row g-2">
                    @foreach ($mediaItems as $item)
                        <div class="col-6 col-sm-4 col-md-3">
                            <div
                                wire:click="toggleItem({{ $item->id }})"
                                style="
                                    border: 2px solid {{ in_array($item->id, $selected) ? '#2377FC' : '#e5e7eb' }};
                                    border-radius: 8px; overflow:hidden; cursor:pointer;
                                    background: {{ in_array($item->id, $selected) ? '#eff6ff' : '#fff' }};
                                    transition: border-color .15s;
                                    position: relative;
                                "
                            >
                                {{-- Check badge --}}
                                @if (in_array($item->id, $selected))
                                    <div style="
                                        position:absolute; top:6px; left:6px; z-index:1;
                                        width:22px; height:22px; background:#2377FC;
                                        border-radius:50%; display:flex; align-items:center; justify-content:center;
                                    ">
                                        <i class="icon-check" style="color:#fff; font-size:11px;"></i>
                                    </div>
                                @endif

                                {{-- Thumbnail --}}
                                @if ($item->isImage())
                                    <div style="height:100px; overflow:hidden; background:#f3f4f6;">
                                        <img
                                            src="{{ $item->getThumbnailUrl() }}"
                                            alt="{{ $item->original_name }}"
                                            style="width:100%; height:100%; object-fit:cover;"
                                            loading="lazy"
                                        >
                                    </div>
                                @else
                                    <div style="height:100px; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
                                        @php
                                            $iconMap = ['video'=>'icon-play-circle','document'=>'icon-file-text','audio'=>'icon-music','archive'=>'icon-package'];
                                            $icon = $iconMap[$item->type] ?? 'icon-file';
                                        @endphp
                                        <i class="{{ $icon }}" style="font-size:32px; color:#9ca3af;"></i>
                                    </div>
                                @endif

                                <div style="padding:6px 8px;">
                                    <div
                                        class="text-tiny"
                                        style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-weight:500;"
                                        title="{{ $item->original_name }}"
                                    >{{ $item->original_name }}</div>
                                    <div class="text-tiny" style="color:#9ca3af;">{{ $item->readableSize() }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 wgp-pagination">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; gap:10px;">
            <div class="text-tiny" style="color:#9ca3af;">
                {{ $mediaItems->total() }} file(s) available
            </div>
            <div class="d-flex gap-2">
                <button type="button" wire:click="close" class="tf-button style-1">
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmSelection"
                    class="tf-button style-1"
                    style="background:#2377FC; border-color:#2377FC; color:#fff;"
                    @if (empty($selected)) disabled @endif
                >
                    <i class="icon-check"></i>
                    Insert {{ count($selected) > 0 ? '(' . count($selected) . ')' : '' }}
                </button>
            </div>
        </div>

        @endif

        {{-- ══ UPLOAD TAB ══════════════════════════════════════════════════════ --}}
        @if ($activeTab === 'upload')
        <div style="flex:1; overflow-y:auto; padding:20px;">
            <div class="text-tiny mb-3" style="color:#6b7280;">
                Upload files to the media library. After uploading, switch to
                <strong>Browse</strong> to select them.
            </div>
            @livewire('admin.media.media-upload')
        </div>
        <div style="padding:14px 20px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" wire:click="switchTab('browse')" class="tf-button style-1">
                <i class="icon-arrow-left"></i> Back to Browse
            </button>
            <button type="button" wire:click="close" class="tf-button style-1">
                Cancel
            </button>
        </div>
        @endif
    </div>
</div>
