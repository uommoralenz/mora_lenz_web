<?php

namespace App\Support;

/**
 * The custom body of an event page: an ordered list of blocks the admin builds
 * in the panel (headings, text, images, buttons, videos…).
 *
 * Everything coming from the panel is rebuilt here from a whitelist, so the
 * database only ever holds known block types with known fields, and every URL
 * is checked for a safe scheme. Rendering then only has to escape.
 */
class EventBlocks
{
    public const MAX_BLOCKS = 80;

    /** Turn untrusted input (array or JSON string) into a clean block list. */
    public static function sanitize(mixed $input): array
    {
        if (is_string($input)) {
            $input = json_decode($input, true);
        }

        if (! is_array($input)) {
            return [];
        }

        $clean = [];

        foreach (array_slice(array_values($input), 0, self::MAX_BLOCKS) as $block) {
            if (! is_array($block)) {
                continue;
            }

            $item = match ($block['type'] ?? null) {
                'heading' => [
                    'type' => 'heading',
                    'text' => static::text($block['text'] ?? '', 200),
                    'level' => ((int) ($block['level'] ?? 2)) === 3 ? 3 : 2,
                ],
                'text' => [
                    'type' => 'text',
                    'text' => static::text($block['text'] ?? '', 10000),
                    'align' => static::pick($block['align'] ?? 'left', ['left', 'center'], 'left'),
                ],
                'image' => [
                    'type' => 'image',
                    'url' => static::url($block['url'] ?? '', ['http', 'https']),
                    'caption' => static::text($block['caption'] ?? '', 300),
                ],
                'button' => [
                    'type' => 'button',
                    'label' => static::text($block['label'] ?? '', 80),
                    'url' => static::url($block['url'] ?? '', ['http', 'https', 'mailto', 'tel']),
                    'style' => static::pick($block['style'] ?? 'primary', ['primary', 'outline'], 'primary'),
                ],
                'video' => [
                    'type' => 'video',
                    'url' => static::url($block['url'] ?? '', ['http', 'https']),
                ],
                'callout' => [
                    'type' => 'callout',
                    'title' => static::text($block['title'] ?? '', 200),
                    'text' => static::text($block['text'] ?? '', 3000),
                ],
                'divider' => ['type' => 'divider'],
                default => null,
            };

            if ($item === null || static::isEmpty($item)) {
                continue;
            }

            $clean[] = $item;
        }

        return $clean;
    }

    /** Which brand a link points at, so buttons can show the right icon. */
    public static function platform(string $url): string
    {
        if (str_starts_with($url, 'mailto:')) {
            return 'mail';
        }
        if (str_starts_with($url, 'tel:')) {
            return 'phone';
        }

        $host = preg_replace('/^www\./', '', strtolower((string) parse_url($url, PHP_URL_HOST)));
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));

        return match (true) {
            $host === 'wa.me', str_ends_with($host, 'whatsapp.com') => 'whatsapp',
            $host === 'fb.me', $host === 'fb.com', str_ends_with($host, 'facebook.com') => 'facebook',
            str_ends_with($host, 'instagram.com') => 'instagram',
            $host === 'youtu.be', str_ends_with($host, 'youtube.com') => 'youtube',
            str_ends_with($host, 'tiktok.com') => 'tiktok',
            $host === 'forms.gle',
            $host === 'docs.google.com' && str_starts_with($path, '/forms') => 'form',
            $host === 'drive.google.com', $host === 'docs.google.com' => 'drive',
            default => 'link',
        };
    }

    /** The embeddable address for a YouTube link, or null if it is not one. */
    public static function youtubeEmbed(string $url): ?string
    {
        $host = preg_replace('/^www\./', '', strtolower((string) parse_url($url, PHP_URL_HOST)));
        $path = (string) parse_url($url, PHP_URL_PATH);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $id = null;

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
        } elseif (str_ends_with($host, 'youtube.com')) {
            if (isset($query['v'])) {
                $id = $query['v'];
            } elseif (preg_match('#^/(embed|shorts|live)/([^/]+)#', $path, $m)) {
                $id = $m[2];
            }
        }

        if (! is_string($id) || ! preg_match('/^[A-Za-z0-9_-]{6,20}$/', $id)) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$id;
    }

    protected static function text(mixed $value, int $max): string
    {
        $value = is_scalar($value) ? (string) $value : '';

        // Plain text only (rendering escapes it); this just drops control bytes.
        $value = preg_replace('/[^\P{C}\n\t]+/u', '', $value) ?? '';

        return mb_substr(trim($value), 0, $max);
    }

    protected static function url(mixed $value, array $schemes): string
    {
        $value = is_string($value) ? trim($value) : '';

        if ($value === '' || strlen($value) > 1000 || preg_match('/[\s<>"\x00-\x1f]/', $value)) {
            return '';
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        if (! in_array($scheme, $schemes, true)) {
            return '';
        }

        if (in_array($scheme, ['http', 'https'], true) && ! parse_url($value, PHP_URL_HOST)) {
            return '';
        }

        return $value;
    }

    protected static function pick(mixed $value, array $allowed, string $default): string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : $default;
    }

    /** Blocks with nothing to show (no text, no link…) are dropped on save. */
    protected static function isEmpty(array $item): bool
    {
        return match ($item['type']) {
            'heading', 'text' => $item['text'] === '',
            'image', 'video' => $item['url'] === '',
            'button' => $item['label'] === '' || $item['url'] === '',
            'callout' => $item['title'] === '' && $item['text'] === '',
            default => false,
        };
    }
}
