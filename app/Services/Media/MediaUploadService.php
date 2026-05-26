<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaUploadService
{
    protected array $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'bash', 'py', 'rb', 'pl',
        'htaccess', 'htpasswd', 'env',
    ];

    protected array $allowedMimeTypes = [
        // Images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif',
        // Video
        'video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm', 'video/x-msvideo',
        // Audio
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        // Archives
        'application/zip',
        'application/x-zip-compressed',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
    ];

    public function upload(UploadedFile $file, array $options = []): Media
    {
        $this->validate($file);

        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = Str::uuid() . '.' . $extension;
        $disk      = $options['disk'] ?? 'public';
        $directory = 'media/original';

        $path = $file->storeAs($directory, $filename, $disk);

        if ($path === false) {
            throw new \RuntimeException('Failed to store uploaded file.');
        }

        $media = Media::create([
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType() ?? $file->getClientMimeType(),
            'extension'     => $extension,
            'size'          => $file->getSize(),
            'type'          => $this->resolveType($file->getMimeType() ?? $file->getClientMimeType()),
            'disk'          => $disk,
            'path'          => $path,
            'category'      => $options['category'] ?? null,
            'caption'       => $options['caption'] ?? null,
            'user_id'       => $options['user_id'] ?? auth()->id(),
            'mediable_id'   => isset($options['mediable']) ? $options['mediable']->getKey() : null,
            'mediable_type' => isset($options['mediable']) ? get_class($options['mediable']) : null,
        ]);

        return $media;
    }

    protected function validate(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, $this->blockedExtensions)) {
            throw new InvalidArgumentException("File type '.{$extension}' is not allowed for security reasons.");
        }

        $mime = $file->getMimeType() ?? $file->getClientMimeType();

        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new InvalidArgumentException("MIME type '{$mime}' is not permitted.");
        }
    }

    protected function resolveType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/'))  return 'image';
        if (str_starts_with($mimeType, 'video/'))  return 'video';
        if (str_starts_with($mimeType, 'audio/'))  return 'audio';

        if (
            str_contains($mimeType, 'pdf') ||
            str_contains($mimeType, 'word') ||
            str_contains($mimeType, 'excel') ||
            str_contains($mimeType, 'spreadsheet') ||
            str_contains($mimeType, 'powerpoint') ||
            str_contains($mimeType, 'presentation') ||
            $mimeType === 'text/plain'
        ) {
            return 'document';
        }

        if (
            str_contains($mimeType, 'zip') ||
            str_contains($mimeType, 'rar') ||
            str_contains($mimeType, '7z')
        ) {
            return 'archive';
        }

        return 'other';
    }
}
