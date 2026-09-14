<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Writes admin uploads into public/uploads/<type>/ and hands back a public URL.
 *
 * Files land directly under public/ rather than storage/, because CWP shared
 * hosting will not reliably let you create the storage symlink that
 * `php artisan storage:link` needs.
 */
class ImageStore
{
    /** Store an uploaded file and return its absolute public URL. */
    public static function put(UploadedFile $file, string $type): string
    {
        $folder = static::folderFor($type);

        $directory = public_path(config('moralenz.upload_dir', 'uploads').'/'.$folder);

        File::ensureDirectoryExists($directory, 0755);

        $extension = Str::lower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');

        $name = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 40, '');
        $name = $name !== '' ? $name : 'image';

        $filename = $name.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(6)).'.'.$extension;

        $file->move($directory, $filename);

        return static::urlFor($folder.'/'.$filename);
    }

    /**
     * Delete a previously stored image, given the URL that was saved in the DB.
     * Silently does nothing for external URLs or paths outside the uploads dir,
     * so a stray value can never be used to delete arbitrary files.
     */
    public static function delete(?string $url): void
    {
        $relative = static::relativePathFor($url);

        if ($relative === null) {
            return;
        }

        $base = public_path(config('moralenz.upload_dir', 'uploads'));
        $full = $base.'/'.$relative;

        $realBase = realpath($base);
        $realFull = realpath($full);

        // Guard against traversal: the resolved file must sit inside uploads/.
        if (! $realBase || ! $realFull || ! str_starts_with($realFull, $realBase.DIRECTORY_SEPARATOR)) {
            return;
        }

        if (is_file($realFull)) {
            @unlink($realFull);
        }
    }

    /** Replace one image with another, cleaning up the old file. */
    public static function replace(UploadedFile $file, string $type, ?string $previousUrl): string
    {
        $url = static::put($file, $type);

        if ($previousUrl && $previousUrl !== $url) {
            static::delete($previousUrl);
        }

        return $url;
    }

    public static function folderFor(string $type): string
    {
        $types = (array) config('moralenz.upload.types', []);

        return $types[$type] ?? throw new \InvalidArgumentException("Unknown upload type [{$type}].");
    }

    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, (array) config('moralenz.upload.types', []));
    }

    public static function urlFor(string $relativePath): string
    {
        return rtrim(config('app.url'), '/')
            .'/'.trim(config('moralenz.upload_dir', 'uploads'), '/')
            .'/'.ltrim($relativePath, '/');
    }

    /** Turn a stored URL back into a path relative to public/uploads, or null. */
    protected static function relativePathFor(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $path = urldecode($path);

        $marker = '/'.trim(config('moralenz.upload_dir', 'uploads'), '/').'/';
        $position = strpos($path, $marker);

        if ($position === false) {
            return null;
        }

        $relative = substr($path, $position + strlen($marker));

        // Reject anything with a traversal segment before it touches the disk.
        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        return $relative;
    }
}
