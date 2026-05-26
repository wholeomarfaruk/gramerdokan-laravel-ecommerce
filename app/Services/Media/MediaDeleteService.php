<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaDeleteService
{
    public function delete(Media $media): bool
    {
        try {
            $disk = $media->disk ?? 'public';

            // Delete variant files from disk
            foreach ($media->variants as $variant) {
                Storage::disk($disk)->delete($variant->path);
            }

            // Delete original file from disk
            Storage::disk($disk)->delete($media->path);

            // Delete DB record (variants cascade via foreign key)
            $media->delete();

            return true;

        } catch (\Throwable $e) {
            Log::error('MediaDeleteService: failed to delete media.', [
                'media_id' => $media->id,
                'error'    => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Bulk delete by IDs. Returns count of successfully deleted items.
     */
    public function bulkDelete(array $ids): int
    {
        $deleted = 0;

        Media::with('variants')
            ->whereIn('id', $ids)
            ->get()
            ->each(function (Media $media) use (&$deleted) {
                if ($this->delete($media)) {
                    $deleted++;
                }
            });

        return $deleted;
    }
}
