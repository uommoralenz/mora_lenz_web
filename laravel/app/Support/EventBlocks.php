<?php

namespace App\Support;

/**
 * The custom body of an event page: an ordered list of blocks the admin builds
 * in the panel (headings, text, images, galleries, quotes, lists, buttons,
 * videos…), plus the page-level options that decide how the page around those
 * blocks is laid out.
 *
 * Everything coming from the panel is rebuilt here from a whitelist, so the
 * database only ever holds known block types with known fields, and every URL
 * is checked for a safe scheme.
 *
 * Text is stored as plain text and marked up at render time by inline(): the
 * admin writes **bold**, *italic*, `code` and [label](url), and that becomes
 * HTML only after the text has been escaped. Nothing an admin types can turn
 * into a tag, so this class needs no HTML sanitiser anywhere.
 */
class EventBlocks
{
    public const MAX_BLOCKS = 80;

    public const MAX_LIST_ITEMS = 30;

    public const MAX_GALLERY_IMAGES = 12;

    /** Defaults for the page-level layout options. */
    public const PAGE_DEFAULTS = [
        'width' => 'normal',
        'hero' => 'photo',
        'show_meta' => true,
    ];

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
                    'level' => static::level($block['level'] ?? 2),
                    'align' => static::pick($block['align'] ?? 'left', ['left', 'center'], 'left'),
                ],
                'text' => [
                    'type' => 'text',
                    'text' => static::text($block['text'] ?? '', 10000),
                    'align' => static::pick($block['align'] ?? 'left', ['left', 'center', 'right'], 'left'),
                    'size' => static::pick($block['size'] ?? 'normal', ['normal', 'lead'], 'normal'),
                ],
                'quote' => [
                    'type' => 'quote',
                    'text' => static::text($block['text'] ?? '', 2000),
                    'cite' => static::text($block['cite'] ?? '', 160),
                ],
                'list' => [
                    'type' => 'list',
                    'style' => static::pick($block['style'] ?? 'bullet', ['bullet', 'number', 'check'], 'bullet'),
                    'items' => static::items($block['items'] ?? []),
                ],
                'image' => [
                    'type' => 'image',
                    'url' => static::url($block['url'] ?? '', ['http', 'https']),
                    'caption' => static::text($block['caption'] ?? '', 300),
                    'width' => static::pick($block['width'] ?? 'full', ['full', 'wide', 'narrow'], 'full'),
                ],
                'gallery' => [
                    'type' => 'gallery',
                    'urls' => static::urls($block['urls'] ?? []),
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
                    'tone' => static::pick($block['tone'] ?? 'info', ['info', 'warn', 'success'], 'info'),
                ],
                'spacer' => [
                    'type' => 'spacer',
                    'size' => static::pick($block['size'] ?? 'medium', ['small', 'medium', 'large'], 'medium'),
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

    /**
     * The page-level layout choices, rebuilt from a whitelist the same way the
     * blocks are. Anything unknown falls back to its default, so a page still
     * renders if the stored options came from an older version of the panel.
     */
    public static function pageOptions(mixed $input): array
    {
        if (is_string($input)) {
            $input = json_decode($input, true);
        }

        $input = is_array($input) ? $input : [];

        return [
            'width' => static::pick($input['width'] ?? '', ['normal', 'wide'], self::PAGE_DEFAULTS['width']),
            'hero' => static::pick($input['hero'] ?? '', ['photo', 'compact', 'plain'], self::PAGE_DEFAULTS['hero']),
            'show_meta' => filter_var(
                $input['show_meta'] ?? self::PAGE_DEFAULTS['show_meta'],
                FILTER_VALIDATE_BOOL
            ),
        ];
    }

    /**
     * Escape plain admin text, then turn its inline marks into HTML.
     *
     * The escaping happens FIRST and only once, so by the time any tag is added
     * the text can no longer contain one. That ordering is the whole reason the
     * result is safe to print unescaped.
     */
    public static function inline(string $text): string
    {
        $html = e($text);

        // [label](url) — the address is scheme-checked before it is trusted,
        // and the label keeps whatever other marks it holds.
        $html = preg_replace_callback(
            '/\[([^\]\n]{1,200})\]\(([^)\s]{1,1000})\)/',
            function (array $m): string {
                // e() encoded the URL; undo that to validate the real address.
                $url = static::url(
                    htmlspecialchars_decode($m[2], ENT_QUOTES),
                    ['http', 'https', 'mailto', 'tel']
                );

                if ($url === '') {
                    return $m[0];
                }

                $attrs = str_starts_with($url, 'http') ? ' target="_blank" rel="noopener noreferrer"' : '';

                return '<a href="'.e($url).'"'.$attrs.'>'.$m[1].'</a>';
            },
            $html
        ) ?? $html;

        // Bold before italic, so the ** of **bold** is never read as a lone *.
        $html = preg_replace('/\*\*(?=\S)(.+?)(?<=\S)\*\*/s', '<strong>$1</strong>', $html) ?? $html;
        $html = preg_replace('/(?<![*\w])\*(?=\S)([^*\n]+?)(?<=\S)\*(?![*\w])/', '<em>$1</em>', $html) ?? $html;
        $html = preg_replace('/`([^`\n]{1,200})`/', '<code>$1</code>', $html) ?? $html;

        return $html;
    }

    /**
     * A multi-paragraph run of admin text as HTML: a blank line starts a new
     * paragraph, a single newline becomes a line break, inline marks applied.
     */
    public static function rich(string $text): string
    {
        $html = '';

        foreach (preg_split('/\R{2,}/', trim($text)) ?: [] as $paragraph) {
            if (trim($paragraph) === '') {
                continue;
            }

            $html .= '<p>'.nl2br(static::inline($paragraph)).'</p>';
        }

        return $html;
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

    protected static function level(mixed $value): int
    {
        $level = (int) $value;

        return in_array($level, [2, 3, 4], true) ? $level : 2;
    }

    /** The lines of a list block: plain text, blanks dropped. */
    protected static function items(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach (array_slice(array_values($value), 0, self::MAX_LIST_ITEMS) as $item) {
            $text = static::text($item, 300);

            if ($text !== '') {
                $items[] = $text;
            }
        }

        return $items;
    }

    /** The images of a gallery block, unusable addresses dropped. */
    protected static function urls(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $urls = [];

        foreach (array_slice(array_values($value), 0, self::MAX_GALLERY_IMAGES) as $url) {
            $clean = static::url($url, ['http', 'https']);

            if ($clean !== '') {
                $urls[] = $clean;
            }
        }

        return $urls;
    }

    /** Blocks with nothing to show (no text, no link…) are dropped on save. */
    protected static function isEmpty(array $item): bool
    {
        return match ($item['type']) {
            'heading', 'text', 'quote' => $item['text'] === '',
            'image', 'video' => $item['url'] === '',
            'gallery' => $item['urls'] === [],
            'list' => $item['items'] === [],
            'button' => $item['label'] === '' || $item['url'] === '',
            'callout' => $item['title'] === '' && $item['text'] === '',
            default => false,
        };
    }
}
