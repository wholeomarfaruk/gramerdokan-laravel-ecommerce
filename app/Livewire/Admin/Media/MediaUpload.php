<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use App\Services\Media\MediaDeleteService;
use App\Services\Media\MediaUploadService;
use App\Services\Media\MediaVariantService;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaUpload extends Component
{
    use WithFileUploads;

    /** Staged Livewire temp files – bound via wire:model */
    public $pendingUploads = [];

    /** IDs of already-saved Media records shown as previews */
    public array $uploadedIds = [];

    /** Passed in from parent to attach media to a model */
    public string $category = '';

    public bool $isProcessing = false;

    // ─── Lifecycle ───────────────────────────────────────────────────────────

    /**
     * Run basic validation as soon as files land in temp storage,
     * so the user gets instant feedback before clicking "Upload All".
     */
    public function updatedPendingUploads(): void
    {
        $this->validate([
            'pendingUploads.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,pdf,doc,docx,xls,xlsx,zip',
            ],
        ]);
    }

    // ─── Actions ─────────────────────────────────────────────────────────────

    public function processAll(MediaUploadService $uploadSvc, MediaVariantService $variantSvc): void
    {
        if (empty($this->pendingUploads)) {
            $this->dispatch('toast', [
                'title'   => 'Warning',
                'message' => 'No files selected.',
                'icon'    => 'warning',
            ]);
            return;
        }

        $this->validate([
            'pendingUploads.*' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,pdf,doc,docx,xls,xlsx,zip',
            ],
        ]);

        $this->isProcessing = true;

        $successCount = 0;
        $errors       = [];

        foreach ($this->pendingUploads as $file) {
            if (!$file) {
                continue;
            }

            try {
                $media = $uploadSvc->upload($file, [
                    'category' => $this->category ?: null,
                    'user_id'  => auth()->id(),
                ]);

                $variantSvc->generate($media);

                $this->uploadedIds[] = $media->id;
                $successCount++;

            } catch (\Throwable $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $this->pendingUploads = [];
        $this->isProcessing   = false;

        if ($successCount > 0) {
            $this->dispatch('media-uploaded', [
                'count' => $successCount,
                'ids'   => $this->uploadedIds,
            ]);

            $this->dispatch('toast', [
                'title'   => 'Uploaded',
                'message' => "{$successCount} file(s) uploaded successfully.",
                'icon'    => 'success',
            ]);
        }

        foreach ($errors as $error) {
            $this->dispatch('toast', [
                'title'   => 'Upload Error',
                'message' => $error,
                'icon'    => 'error',
            ]);
        }
    }

    public function removeUploaded(int $mediaId, MediaDeleteService $deleteSvc): void
    {
        $media = Media::find($mediaId);
        if ($media) {
            $deleteSvc->delete($media);
        }

        $this->uploadedIds = array_values(array_diff($this->uploadedIds, [$mediaId]));
    }

    public function clearPending(): void
    {
        $this->pendingUploads = [];
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $uploadedMedia = Media::with('variants')
            ->whereIn('id', $this->uploadedIds)
            ->latest()
            ->get();

        return view('livewire.admin.media.upload', compact('uploadedMedia'));
    }
}
