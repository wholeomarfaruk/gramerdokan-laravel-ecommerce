<div>
    {{-- ── Drop Zone ─────────────────────────────────────────────────────────── --}}
    <div
        id="media-dropzone-{{ $this->getId() }}"
        class="media-dropzone"
        style="
            border: 2px dashed #c0c8d4;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: border-color .2s, background .2s;
            position: relative;
        "
        ondragover="event.preventDefault(); this.style.borderColor='#2377FC'; this.style.background='#eff6ff';"
        ondragleave="this.style.borderColor='#c0c8d4'; this.style.background='#f8fafc';"
        ondrop="handleMediaDrop(event, '{{ $this->getId() }}')"
        onclick="document.getElementById('media-input-{{ $this->getId() }}').click()"
    >
        <div style="pointer-events:none;">
            <i class="icon-upload" style="font-size:36px; color:#2377FC; display:block; margin-bottom:10px;"></i>
            <div class="body-title" style="margin-bottom:4px;">Drag & drop files here</div>
            <div class="text-tiny" style="color:#6b7280;">or click to browse &nbsp;·&nbsp; Max 10 MB per file</div>
            <div class="text-tiny" style="color:#9ca3af; margin-top:4px;">
                JPG, PNG, GIF, WebP, MP4, PDF, DOCX, XLSX, ZIP
            </div>
        </div>

        {{-- Hidden real input bound to Livewire --}}
        <input
            type="file"
            id="media-input-{{ $this->getId() }}"
            wire:model="pendingUploads"
            multiple
            style="display:none;"
            accept="image/*,video/mp4,video/quicktime,application/pdf,application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                    application/vnd.ms-excel,
                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                    application/zip"
        >
    </div>

    {{-- ── Upload progress indicator ───────────────────────────────────────── --}}
    <div wire:loading wire:target="pendingUploads" class="mt-2">
        <div class="progress" style="height:4px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%;"></div>
        </div>
        <div class="text-tiny mt-1" style="color:#6b7280;">Uploading files to staging area…</div>
    </div>

    {{-- ── Validation errors ────────────────────────────────────────────────── --}}
    @error('pendingUploads.*')
        <div class="text-danger text-tiny mt-1">{{ $message }}</div>
    @enderror

    {{-- ── Staged file list ─────────────────────────────────────────────────── --}}
    @if (!empty($pendingUploads))
        <div class="wg-box mt-3">
            <div class="flex items-center justify-between mb-2">
                <div class="body-title">Ready to upload ({{ count($pendingUploads) }} file{{ count($pendingUploads) > 1 ? 's' : '' }})</div>
                <button type="button" wire:click="clearPending" class="tf-button style-1" style="padding:4px 12px;font-size:12px;">
                    <i class="icon-x"></i> Clear
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;"></th>
                            <th>File name</th>
                            <th style="width:90px;">Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingUploads as $file)
                            <tr>
                                <td class="text-center">
                                    @if ($file && str_starts_with($file->getMimeType() ?? $file->getClientMimeType() ?? '', 'image/'))
                                        <img src="{{ $file->temporaryUrl() }}"
                                             style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                    @else
                                        <i class="icon-file" style="font-size:24px;color:#6b7280;"></i>
                                    @endif
                                </td>
                                <td class="text-tiny" style="vertical-align:middle;">
                                    {{ $file->getClientOriginalName() }}
                                </td>
                                <td class="text-tiny" style="vertical-align:middle;">
                                    {{ number_format($file->getSize() / 1024, 1) }} KB
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button
                    type="button"
                    wire:click="processAll"
                    wire:loading.attr="disabled"
                    class="tf-button style-1"
                >
                    <span wire:loading.remove wire:target="processAll">
                        <i class="icon-upload"></i>
                        Upload {{ count($pendingUploads) }} file{{ count($pendingUploads) > 1 ? 's' : '' }}
                    </span>
                    <span wire:loading wire:target="processAll">
                        Processing…
                    </span>
                </button>
            </div>
        </div>
    @endif

    {{-- ── Uploaded previews ────────────────────────────────────────────────── --}}
    @if ($uploadedMedia->isNotEmpty())
        <div class="wg-box mt-3">
            <div class="body-title mb-2">Uploaded ({{ $uploadedMedia->count() }})</div>
            <div class="row g-2">
                @foreach ($uploadedMedia as $media)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="wg-box" style="padding:8px; position:relative;">
                            {{-- Remove button --}}
                            <button
                                type="button"
                                wire:click="removeUploaded({{ $media->id }})"
                                style="
                                    position:absolute; top:4px; right:4px;
                                    background:#dc3545; color:#fff; border:none;
                                    border-radius:50%; width:20px; height:20px;
                                    font-size:11px; cursor:pointer; z-index:1;
                                    display:flex; align-items:center; justify-content:center;
                                "
                                title="Remove"
                            >&times;</button>

                            @if ($media->isImage())
                                <img
                                    src="{{ $media->getThumbnailUrl() }}"
                                    style="width:100%;height:70px;object-fit:cover;border-radius:4px;"
                                    alt="{{ $media->original_name }}"
                                >
                            @else
                                <div style="width:100%;height:70px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:4px;">
                                    <i class="icon-file" style="font-size:28px;color:#9ca3af;"></i>
                                </div>
                            @endif

                            <div class="text-tiny mt-1" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;" title="{{ $media->original_name }}">
                                {{ $media->original_name }}
                            </div>
                            <div class="text-tiny" style="color:#9ca3af;">{{ $media->readableSize() }}</div>

                            {{-- Copy URL button --}}
                            <button
                                type="button"
                                onclick="copyToClipboard('{{ $media->getUrl() }}', this)"
                                class="tf-button style-1 w-100 mt-1"
                                style="padding:2px 6px;font-size:11px;"
                                title="Copy URL"
                            >
                                <i class="icon-copy"></i> Copy URL
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@once
<script>
    function handleMediaDrop(event, componentId) {
        event.preventDefault();
        const dropzone = event.currentTarget;
        dropzone.style.borderColor = '#c0c8d4';
        dropzone.style.background  = '#f8fafc';

        const input = document.getElementById('media-input-' + componentId);
        if (!input) return;

        const dt = new DataTransfer();
        Array.from(event.dataTransfer.files).forEach(f => dt.items.add(f));
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="icon-check"></i> Copied!';
            setTimeout(() => { btn.innerHTML = orig; }, 2000);
        });
    }
</script>
@endonce
