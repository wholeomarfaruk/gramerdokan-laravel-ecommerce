<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use App\Services\Media\MediaDeleteService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MediaLibrary extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $typeFilter  = '';
    public int    $perPage     = 24;
    public array  $selected    = [];

    public string $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search'     => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
        } else {
            $this->selected[] = $id;
        }
    }

    public function selectAllVisible(): void
    {
        $ids = $this->buildQuery()->pluck('id')->toArray();
        $this->selected = array_unique(array_merge($this->selected, $ids));
    }

    public function clearSelection(): void
    {
        $this->selected = [];
    }

    public function deleteOne(int $id, MediaDeleteService $svc): void
    {
        $media = Media::find($id);
        if (!$media) {
            return;
        }

        $svc->delete($media);
        $this->selected = array_values(array_diff($this->selected, [$id]));

        $this->dispatch('toast', [
            'title'   => 'Deleted',
            'message' => 'Media item deleted.',
            'icon'    => 'success',
        ]);
    }

    public function bulkDelete(MediaDeleteService $svc): void
    {
        if (empty($this->selected)) {
            $this->dispatch('toast', [
                'title'   => 'Warning',
                'message' => 'No items selected.',
                'icon'    => 'warning',
            ]);
            return;
        }

        $count = $svc->bulkDelete($this->selected);
        $this->selected = [];

        $this->dispatch('toast', [
            'title'   => 'Deleted',
            'message' => "{$count} item(s) deleted.",
            'icon'    => 'success',
        ]);
    }

    #[On('media-uploaded')]
    public function onMediaUploaded(): void
    {
        $this->resetPage();
    }

    protected function buildQuery()
    {
        return Media::with('variants')
            ->when($this->search, fn($q) =>
                $q->where('original_name', 'LIKE', "%{$this->search}%")
                  ->orWhere('caption', 'LIKE', "%{$this->search}%")
            )
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->latest();
    }

    public function render()
    {
        $mediaItems = $this->buildQuery()->paginate($this->perPage);
        $totalCount = Media::count();

        return view('livewire.admin.media.library', compact('mediaItems', 'totalCount'));
    }
}
