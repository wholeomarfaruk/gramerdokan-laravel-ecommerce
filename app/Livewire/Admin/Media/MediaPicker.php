<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MediaPicker extends Component
{
    use WithPagination;

    public bool   $isOpen     = false;
    public string $search     = '';
    public string $typeFilter = 'image';
    public bool   $multiple   = false;
    public array  $selected   = [];
    public string $activeTab  = 'browse'; // 'browse' | 'upload'

    /** JS callback key passed in when opening — returned with the picked media */
    public string $callbackKey = '';

    public string $paginationTheme = 'bootstrap';

    // ─── Open / Close ────────────────────────────────────────────────────────

    #[On('open-media-picker')]
    public function open(bool $multiple = false, string $callbackKey = ''): void
    {
        $this->multiple    = $multiple;
        $this->callbackKey = $callbackKey;
        $this->selected    = [];
        $this->search      = '';
        $this->activeTab   = 'browse';
        $this->resetPage();
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen    = false;
        $this->activeTab = 'browse';
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ─── After upload: refresh grid and switch back to browse ────────────────

    #[On('media-uploaded')]
    public function onMediaUploaded(): void
    {
        $this->activeTab = 'browse';
        $this->resetPage();
    }

    // ─── Selection ───────────────────────────────────────────────────────────

    public function toggleItem(int $id): void
    {
        if ($this->multiple) {
            if (in_array($id, $this->selected)) {
                $this->selected = array_values(array_diff($this->selected, [$id]));
            } else {
                $this->selected[] = $id;
            }
        } else {
            $this->selected = [$id];
        }
    }

    public function confirmSelection(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $mediaItems = Media::with('variants')
            ->whereIn('id', $this->selected)
            ->get()
            ->map(fn(Media $m) => [
                'id'           => $m->id,
                'url'          => $m->getUrl(),
                'thumbnail'    => $m->getThumbnailUrl(),
                'original_name' => $m->original_name,
                'type'         => $m->type,
                'size'         => $m->readableSize(),
            ])
            ->values()
            ->toArray();

        $this->dispatch('media-picker-confirmed', [
            'callbackKey' => $this->callbackKey,
            'media'       => $mediaItems,
            'single'      => !$this->multiple ? ($mediaItems[0] ?? null) : null,
        ]);

        $this->isOpen = false;
    }

    // ─── Search ──────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $mediaItems = Media::with('variants')
            ->when($this->search, fn($q) =>
                $q->where('original_name', 'LIKE', "%{$this->search}%")
            )
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.media.picker', compact('mediaItems'));
    }
}
