<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Throwable;

class PrivateMediaService
{
    public static function diskName(): string
    {
        return config('filesystems.media_disk', env('MEDIA_DISK', 'local'));
    }

    public static function store(UploadedFile $file, string $directory = 'activities'): string
    {
        $disk = self::diskName();

        return $file->store($directory, [
            'disk' => $disk,
            'visibility' => 'private',
        ]);
    }

    /**
     * Generate a temporary signed URL for a private media path.
     * Supports S3 / Cloudflare R2 presigned URLs and local private signed URLs.
     */
    public static function signedUrl(?string $path, int $minutes = 30): ?string
    {
        if (! $path) {
            return null;
        }

        // Built-in SVG data URLs or seeded SVG assets in private storage
        if (str_starts_with($path, 'data:')) {
            return $path;
        }

        $disk = self::diskName();

        if ($disk === 's3' && config('filesystems.disks.s3.key')) {
            try {
                return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
            } catch (Throwable) {
                // Fallback to application-level temporary signed route
            }
        }

        return URL::temporarySignedRoute(
            'media.private',
            now()->addMinutes($minutes),
            ['path' => base64_encode($path)]
        );
    }
}

