<?php

namespace App\Support;

use App\Models\Event;

/**
 * URL builder that copes with a server whose nginx cannot rewrite to index.php.
 *
 * On such a server the only paths that reach PHP are ones that physically exist
 * on disk, so public_html contains a folder per page (events/, team/, …) each
 * holding a tiny shim. Pages with unlimited URLs — event detail, one per slug —
 * cannot have a folder each, so in compat mode they are addressed as
 * /events/?e=<slug> and the shim converts that back.
 *
 * Set COMPAT_URLS=false in .env once nginx has:
 *     location / { try_files $uri $uri/ /index.php?$query_string; }
 * and every link reverts to its normal clean form with no other change.
 */
class Links
{
    public static function compat(): bool
    {
        return (bool) config('moralenz.compat_urls', false);
    }

    /**
     * A directory URL with its trailing slash intact.
     *
     * url() normalises what it is given with trim($path, '/'), so url('/contact/')
     * comes back as ".../contact" — the slash every caller below relies on is
     * dropped. Re-appending it here is the only dependable way to keep it, and
     * doing it in one place stops the next caller from hitting the same trap.
     */
    protected static function dir(string $path): string
    {
        return rtrim(url($path), '/').'/';
    }

    /** A single event's page. */
    public static function event(Event $event): string
    {
        if (! static::compat()) {
            return route('events.show', $event->slug);
        }

        return static::dir('/events').'?e='.rawurlencode($event->slug);
    }

    /** Draft preview of an event that may not be saved yet. */
    public static function eventPreview(string $token): string
    {
        if (! static::compat()) {
            return route('events.preview', $token);
        }

        return static::dir('/events').'?preview='.rawurlencode($token);
    }

    /**
     * Where the contact form posts.
     *
     * The trailing slash matters: nginx answers /contact with a 301 to
     * /contact/, and browsers downgrade a redirected POST to GET. The request
     * then arrives as a GET on a POST-only route, so the visitor gets a bare
     * 405 and the message is thrown away. Keep this going to /contact/.
     */
    public static function contactPost(): string
    {
        return static::compat() ? static::dir('/contact') : route('contact.store');
    }

    /**
     * Named routes that DO have a real folder. The trailing slash just saves a
     * redirect hop; without it the page still loads.
     */
    public static function page(string $name, string $path): string
    {
        return static::compat() ? static::dir($path) : route($name);
    }

    public static function events(): string
    {
        return static::page('events.index', '/events');
    }

    public static function team(): string
    {
        return static::page('team', '/team');
    }

    public static function gallery(): string
    {
        return static::page('gallery', '/gallery');
    }

    public static function service(string $type): string
    {
        return static::compat()
            ? static::dir('/services/'.$type)
            : route('services.show', $type);
    }
}
