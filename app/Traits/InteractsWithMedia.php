<?php

namespace App\Traits;

use App\Models\Media;
use App\Services\Media\MediaDeleteService;
use App\Services\Media\MediaUploadService;
use App\Services\Media\MediaVariantService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

trait InteractsWithMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Upload a file and attach it to this model.
     */
    public function addMedia(UploadedFile $file, array $options = []): Media
    {
        $media = app(MediaUploadService::class)->upload($file, array_merge($options, [
            'mediable' => $this,
        ]));

        app(MediaVariantService::class)->generate($media);

        return $media;
    }

    /**
     * Get attached media, optionally filtered by category.
     */
    public function getMedia(?string $category = null): Collection
    {
        return $this->media()
            ->with('variants')
            ->when($category, fn($q) => $q->where('category', $category))
            ->get();
    }

    /**
     * Get the first attached media item, optionally filtered by category.
     */
    public function getFirstMedia(?string $category = null): ?Media
    {
        return $this->media()
            ->with('variants')
            ->when($category, fn($q) => $q->where('category', $category))
            ->first();
    }

    /**
     * Delete all attached media, optionally filtered by category.
     */
    public function deleteMedia(?string $category = null): void
    {
        $deleteService = app(MediaDeleteService::class);

        $this->getMedia($category)->each(fn(Media $media) => $deleteService->delete($media));
    }
}
