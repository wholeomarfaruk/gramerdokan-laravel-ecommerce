<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Models\Variant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MediaVariantService
{
    /**
     * Variant definitions: ratio_name => [width, height, mode]
     * mode "cover"  → crop to exact dimensions (good for thumbnails)
     * mode "scale"  → proportional scale-down (good for large/medium)
     */
    protected array $variants = [
        'thumb'  => ['width' => 150,  'height' => 150,  'mode' => 'cover'],
        'medium' => ['width' => 600,  'height' => 600,  'mode' => 'scale'],
        'large'  => ['width' => 1200, 'height' => 1200, 'mode' => 'scale'],
    ];

    public function generate(Media $media): void
    {
        if (!$media->isImage()) {
            return;
        }

        $sourcePath = Storage::disk($media->disk)->path($media->path);

        if (!file_exists($sourcePath)) {
            Log::warning('MediaVariantService: source file not found.', ['path' => $sourcePath, 'media_id' => $media->id]);
            return;
        }

        foreach ($this->variants as $ratio => $config) {
            $this->generateVariant($media, $ratio, $config, $sourcePath);
        }

        $this->generateWebp($media, $sourcePath);
    }

    protected function generateVariant(Media $media, string $ratio, array $config, string $sourcePath): void
    {
        try {
            $image = Image::read($sourcePath);

            if ($config['mode'] === 'cover') {
                $image->cover($config['width'], $config['height']);
            } else {
                $image->scaleDown(width: $config['width'], height: $config['height']);
            }

            $dir      = "media/variants/{$ratio}";
            $savePath = Storage::disk($media->disk)->path("{$dir}/{$media->filename}");

            $this->ensureDirectory(dirname($savePath));
            $image->save($savePath);

            Variant::updateOrCreate(
                ['media_id' => $media->id, 'ratio' => $ratio],
                [
                    'path' => "{$dir}/{$media->filename}",
                    'size' => file_exists($savePath) ? filesize($savePath) : 0,
                ]
            );

        } catch (\Throwable $e) {
            Log::error("MediaVariantService: failed to create '{$ratio}' variant.", [
                'media_id' => $media->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    protected function generateWebp(Media $media, string $sourcePath): void
    {
        try {
            $webpFilename = pathinfo($media->filename, PATHINFO_FILENAME) . '.webp';
            $dir          = 'media/variants/webp';
            $savePath     = Storage::disk($media->disk)->path("{$dir}/{$webpFilename}");

            $this->ensureDirectory(dirname($savePath));

            Image::read($sourcePath)->toWebp(quality: 82)->save($savePath);

            Variant::updateOrCreate(
                ['media_id' => $media->id, 'ratio' => 'webp'],
                [
                    'path' => "{$dir}/{$webpFilename}",
                    'size' => file_exists($savePath) ? filesize($savePath) : 0,
                ]
            );

        } catch (\Throwable $e) {
            Log::error('MediaVariantService: failed to create webp variant.', [
                'media_id' => $media->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    protected function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
