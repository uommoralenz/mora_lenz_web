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

    /** A single event's page. */
    public static function event(Event $event): string
    {
        if (! static::compat()) {
            return route('events.show', $event->slug);
        }

        return url('/events/').'?e='.rawurlencode($event->slug);
    }

    /**
     * Where the contact form posts.
     *
     * The trailing slash matters: nginx answers /contact with a 301 to
     * /contact/, and browsers downgrade a redirected POST to GET, which would
     * silently throw the message away.
     */
    public static function contactPost(): string
    {
        return static::compat() ? url('/contact/') : route('contact.store');
    }

    /**
     * Named routes that DO have a real folder. The trailing slash just saves a
     * redirect hop; without it the page still loads.
     */
    public static function page(string $name, string $path): string
    {
        return static::compat() ? url($path.'/') : route($name);
    }

    public static function events(): string
    {
        return static::page('events.index', '/events');
    }

    public static function team(): string
    {
        return static::page('team', '/team');
    }

    public static function service(string $type): string
    {
        return static::compat()
            ? url('/services/'.$type.'/')
            : route('services.show', $type);
    }
}
