<?php

namespace App\Support;
class EventBlocks
{
    public const MAX_BLOCKS = 80;

    public const MAX_LIST_ITEMS = 30;

    public const MAX_GALLERY_IMAGES = 12;

    /** How much markup one html block may hold. */
    public const MAX_HTML = 60000;

    /** How much page-wide custom code (CSS/JS/markup) one event may hold. */
    public const MAX_PAGE_CODE = 20000;

    /** The tallest a sandboxed html frame may be asked to be, in pixels. */
    public const MAX_FRAME_HEIGHT = 6000;

    /** Defaults for the page-level layout options. */
    public const PAGE_DEFAULTS = [
        'width' => 'normal',
        'hero' => 'photo',
        'show_meta' => true,
        'custom_code' => '',
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
                // Raw markup, kept exactly as typed. See the class doc block.
                'html' => [
                    'type' => 'html',
                    'code' => static::code($block['code'] ?? '', self::MAX_HTML),
                    'width' => static::pick($block['width'] ?? 'normal', ['normal', 'wide', 'full'], 'normal'),
                    'mode' => static::pick($block['mode'] ?? 'inline', ['inline', 'frame'], 'inline'),
                    'height' => static::frameHeight($block['height'] ?? 0),
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
            'width' => static::pick($input['width'] ?? '', ['normal', 'wide', 'full'], self::PAGE_DEFAULTS['width']),
            'hero' => static::pick($input['hero'] ?? '', ['photo', 'compact', 'plain'], self::PAGE_DEFAULTS['hero']),
            'show_meta' => filter_var(
                $input['show_meta'] ?? self::PAGE_DEFAULTS['show_meta'],
                FILTER_VALIDATE_BOOL
            ),
            // Page-wide markup: <style> rules that theme every block, a web
            // font, a <script> the html blocks below rely on. Printed at the
            // end of <head>, so it can override the site stylesheet.
            'custom_code' => static::code($input['custom_code'] ?? '', self::MAX_PAGE_CODE),
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

    /**
     * Wraps an html block's code in a standalone document for "frame" mode.
     *
     * The iframe is sandboxed without allow-same-origin, so the code inside
     * can run but cannot read this site's cookies, DOM or storage, and its CSS
     * cannot leak out into the rest of the page. The trade-off is that the
     * frame has no height of its own, so the bootstrap script at the end
     * measures the content and posts it to the parent, which resizes the
     * element (see the listener in partials/event-blocks.blade.php).
     */
    public static function frameDocument(string $code): string
    {
        // Preserve custom styling while preventing fixed-width content from
        // breaking the layout on phones and other narrow viewports.
        $safety = <<<'CSS'
            *, *::before, *::after { box-sizing: border-box; }
            html, body { max-width: 100%; overflow-x: hidden; }
            body { overflow-wrap: anywhere; }
            body * { min-width: 0; }
            body > * { max-width: 100% !important; }
            img, video, canvas, svg { max-width: 100% !important; height: auto; }
            iframe, embed, object { max-width: 100% !important; }
            table { display: block; max-width: 100% !important; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            pre { max-width: 100%; overflow-x: auto; }
            input, select, textarea, button { max-width: 100%; }
            @media (max-width: 600px) {
                .ml-event-gallery { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
            CSS;

        // Matches the site's dark surface so an embed looks at home without
        // the admin having to restate any of it.
        $theme = <<<'CSS'
            :root { color-scheme: dark; }
            html, body { margin: 0; padding: 0; background: transparent; }
            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
                color: #e5e7eb;
                line-height: 1.7;
            }
            a { color: #93c5fd; }
            CSS;

        $base = $theme."\n".$safety;

        $bootstrap = <<<'JS'
            (function () {
                var last = 0;
                function report() {
                    var doc = document.documentElement;
                    var height = Math.max(
                        document.body ? document.body.scrollHeight : 0,
                        document.body ? document.body.offsetHeight : 0,
                        doc.scrollHeight,
                        doc.offsetHeight
                    );
                    if (height && Math.abs(height - last) > 1) {
                        last = height;
                        parent.postMessage({ moraLenzFrame: true, height: height }, '*');
                    }
                }
                window.addEventListener('load', report);
                window.addEventListener('resize', report);
                if (window.ResizeObserver && document.body) {
                    new ResizeObserver(report).observe(document.body);
                }
                // Fallbacks for content that settles after load (fonts, images).
                [0, 120, 500, 1500].forEach(function (delay) { setTimeout(report, delay); });
            })();
            JS;

        // A whole page pasted in as it stands — the usual case when a design
        // was built elsewhere and brought over complete. Wrapping it would
        // nest one document inside another, so it is used as it is and only
        // the height reporter is added.
        if (static::isFullDocument($code)) {
            $head = '<style data-mora-lenz-responsive>'.$safety.'</style>';

            if (! preg_match('/<meta\s+[^>]*name\s*=\s*["\']viewport["\']/i', $code)) {
                $head = '<meta name="viewport" content="width=device-width, initial-scale=1">'.$head;
            }

            return static::withScript(static::withHead($code, $head), $bootstrap);
        }

        return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<style>'.$base.'</style></head><body>'
            .$code
            .'<script>'.$bootstrap.'</script>'
            .'</body></html>';
    }

    /**
     * Swap [gallery event=<slug>] shortcodes in an html block for the photo
     * album of that event. [gallery id=N] still shows an album from the old
     * Gallery section.
     *
     * Options: limit=N (max photos, default all), columns=2-6 (default 3).
     * Rendered when the page is shown, so adding photos to the album updates
     * every page that embeds it. Everything printed is escaped; the
     * admin's own markup around the shortcode is untouched.
     */
    public static function expandGalleries(string $code): string
    {
        if (stripos($code, '[gallery') === false) {
            return $code;
        }

        return preg_replace_callback('/\[gallery\s+([^\]]*)\]/i', function (array $m) {
            preg_match_all("/(\w+)\s*=\s*(?:\"([^\"]*)\"|'([^']*)'|(\S+))/", $m[1], $pairs, PREG_SET_ORDER);

            $args = [];
            foreach ($pairs as $pair) {
                $args[strtolower($pair[1])] = ($pair[4] ?? '') !== '' ? $pair[4] : (($pair[3] ?? '') !== '' ? $pair[3] : ($pair[2] ?? ''));
            }

            if (filled($args['event'] ?? null)) {
                // An event's own album, by slug.
                $event = \App\Models\Event::active()->with('photos')->where('slug', $args['event'])->first();

                if (! $event) {
                    return '';
                }

                $title = $event->title;
                $photos = $event->photos->map(fn ($p) => ['src' => $p->image_url, 'caption' => $p->caption]);
            } else {
                // id=N: an album from the old Gallery section, kept so pages
                // written before it was retired still render.
                $gallery = \App\Models\FeaturedGallery::active()->with('images')->find((int) ($args['id'] ?? 0));

                if (! $gallery) {
                    return '';
                }

                $title = $gallery->title;
                $photos = $gallery->images->map(fn ($i) => ['src' => $i->image_url, 'caption' => $i->description]);

                if ($photos->isEmpty() && $gallery->image_url) {
                    $photos = collect([['src' => $gallery->image_url, 'caption' => null]]);
                }
            }

            $limit = (int) ($args['limit'] ?? 0);
            if ($limit > 0) {
                $photos = $photos->take($limit);
            }

            $columns = max(2, min(6, (int) ($args['columns'] ?? 3)));

            $html = '<div class="ml-event-gallery" style="display:grid;grid-template-columns:repeat('.$columns.',minmax(0,1fr));gap:10px;margin:1.5rem 0">';
            foreach ($photos as $photo) {
                $src = e($photo['src']);
                $alt = e($photo['caption'] ?: $title);
                $html .= '<a href="'.$src.'" target="_blank" rel="noopener" style="display:block;aspect-ratio:4/3;overflow:hidden;border-radius:10px">'
                    .'<img src="'.$src.'" alt="'.$alt.'" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block"></a>';
            }

            return $html.'</div>';
        }, $code) ?? $code;
    }

    /** Is this a complete HTML document rather than a fragment? */
    public static function isFullDocument(string $code): bool
    {
        // Only the opening of the string matters: a <html> further in is part
        // of the content (an example, an escaped snippet), not the document.
        $start = ltrim(mb_substr($code, 0, 2000));

        return (bool) preg_match('/^<!DOCTYPE\s+html/i', $start)
            || (bool) preg_match('/^(<\?xml[^>]*>\s*)?<html[\s>]/i', $start);
    }

    /** Put a script just before </body>, or at the end if there is no body. */
    protected static function withScript(string $document, string $javascript): string
    {
        $script = '<script>'.$javascript.'</script>';
        $at = strripos($document, '</body>');

        return $at === false
            ? $document.$script
            : substr($document, 0, $at).$script.substr($document, $at);
    }

    /** Put responsive metadata and styles at the end of a full document head. */
    protected static function withHead(string $document, string $markup): string
    {
        $at = strripos($document, '</head>');

        if ($at !== false) {
            return substr($document, 0, $at).$markup.substr($document, $at);
        }

        if (preg_match('/<html\b[^>]*>/i', $document, $match, PREG_OFFSET_CAPTURE)) {
            $end = $match[0][1] + strlen($match[0][0]);

            return substr($document, 0, $end).'<head>'.$markup.'</head>'.substr($document, $end);
        }

        return '<head>'.$markup.'</head>'.$document;
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

    /**
     * Admin-authored markup, kept byte for byte.
     *
     * Nothing is escaped or stripped here beyond NUL, which cannot legally
     * appear in markup — the point of these fields is that what the admin
     * typed is what the page gets. The length cap is what stops a paste
     * accident from filling the column.
     */
    protected static function code(mixed $value, int $max): string
    {
        $value = is_string($value) ? $value : '';

        return mb_substr(trim(str_replace("\0", '', $value)), 0, $max);
    }

    /** A fixed frame height in pixels, or 0 to measure the content instead. */
    protected static function frameHeight(mixed $value): int
    {
        $height = (int) $value;

        if ($height <= 0) {
            return 0;
        }

        return min(self::MAX_FRAME_HEIGHT, max(80, $height));
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
            'html' => $item['code'] === '',
            'callout' => $item['title'] === '' && $item['text'] === '',
            default => false,
        };
    }
}
