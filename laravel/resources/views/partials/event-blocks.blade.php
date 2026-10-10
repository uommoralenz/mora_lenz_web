{{--
    Renders an event's custom blocks (see App\Support\EventBlocks).

    Plain values are escaped by {{ }}. Text that may carry inline marks goes
    through EventBlocks::inline()/rich(), which escape first and only then add
    tags, so their output is safe to print raw. URLs were scheme-checked when
    they were saved.

    Consecutive buttons are grouped into one wrapping row.

    The one block printed unescaped on purpose is "html": admin-authored markup
    (with its own <style>/<script>), the Blogger-gadget escape hatch for pages
    the fixed blocks cannot build. "frame" mode puts it in a sandboxed iframe
    instead, which is why this file ends with a height listener.
--}}
@php
    use App\Support\EventBlocks;

    $blocks = array_values((array) ($blocks ?? []));

    $iconPaths = [
        'whatsapp' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
        'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
        'youtube' => '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><polygon points="10 15 15 12 10 9"/>',
        'tiktok' => '<path d="M15 3v10.5a3.5 3.5 0 1 1-3.5-3.5h.5"/><path d="M15 3a5 5 0 0 0 5 5"/>',
        'form' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/>',
        'drive' => '<path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'link' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/>',
    ];

    $i = 0;
    $count = count($blocks);

    // The listener at the bottom is only worth printing if something needs it.
    $hasFrames = collect($blocks)
        ->contains(fn ($b) => ($b['type'] ?? '') === 'html' && ($b['mode'] ?? 'inline') === 'frame'
            && (int) ($b['height'] ?? 0) === 0);
@endphp

<div class="event-blocks">
    @while ($i < $count)
        @php $block = $blocks[$i]; @endphp

        @switch($block['type'] ?? '')
            @case('heading')
                @php
                    $level = in_array((int) ($block['level'] ?? 2), [2, 3, 4], true) ? (int) $block['level'] : 2;
                    $headingClass = 'event-blocks__h'.$level
                        .(($block['align'] ?? 'left') === 'center' ? ' is-center' : '');
                @endphp
                <h{{ $level }} class="{{ $headingClass }}">{{ $block['text'] }}</h{{ $level }}>
                @break

            @case('text')
                @php
                    $align = $block['align'] ?? 'left';
                    $textClass = 'prose event-blocks__text'
                        .($align !== 'left' ? ' is-'.$align : '')
                        .(($block['size'] ?? 'normal') === 'lead' ? ' is-lead' : '');
                @endphp
                <div class="{{ $textClass }}">{!! EventBlocks::rich($block['text']) !!}</div>
                @break

            @case('quote')
                <blockquote class="event-blocks__quote">
                    <p>{!! nl2br(EventBlocks::inline($block['text'])) !!}</p>
                    @if (! empty($block['cite']))
                        <cite>{{ $block['cite'] }}</cite>
                    @endif
                </blockquote>
                @break

            @case('list')
                @php
                    $style = $block['style'] ?? 'bullet';
                    $tag = $style === 'number' ? 'ol' : 'ul';
                @endphp
                <{{ $tag }} class="event-blocks__list is-{{ $style }}">
                    @foreach ($block['items'] as $item)
                        <li>{!! EventBlocks::inline($item) !!}</li>
                    @endforeach
                </{{ $tag }}>
                @break

            @case('image')
                <figure class="event-blocks__figure is-{{ $block['width'] ?? 'full' }}">
                    <img src="{{ $block['url'] }}" alt="{{ $block['caption'] ?? '' }}" loading="lazy">
                    @if (! empty($block['caption']))
                        <figcaption>{{ $block['caption'] }}</figcaption>
                    @endif
                </figure>
                @break

            @case('gallery')
                @php $urls = array_values((array) ($block['urls'] ?? [])); @endphp
                <figure class="event-blocks__gallery" data-count="{{ min(count($urls), 4) }}">
                    <div class="event-blocks__gallery-grid">
                        @foreach ($urls as $url)
                            <img src="{{ $url }}" alt="{{ $block['caption'] ?? '' }}" loading="lazy">
                        @endforeach
                    </div>
                    @if (! empty($block['caption']))
                        <figcaption>{{ $block['caption'] }}</figcaption>
                    @endif
                </figure>
                @break

            @case('button')
                <div class="event-blocks__buttons">
                    @while ($i < $count && ($blocks[$i]['type'] ?? '') === 'button')
                        @php
                            $button = $blocks[$i];
                            $platform = EventBlocks::platform($button['url']);
                            $external = str_starts_with($button['url'], 'http');
                        @endphp
                        <a class="cta {{ ($button['style'] ?? 'primary') === 'outline' ? 'cta--ghost' : '' }} event-blocks__btn"
                           href="{{ $button['url'] }}"
                           @if ($external) target="_blank" rel="noopener noreferrer" @endif>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $iconPaths[$platform] ?? $iconPaths['link'] !!}</svg>
                            <span class="cta__label">{{ $button['label'] }}</span>
                        </a>
                        @php $i++; @endphp
                    @endwhile
                    @php $i--; @endphp
                </div>
                @break

            @case('video')
                @php $embed = EventBlocks::youtubeEmbed($block['url']); @endphp
                @if ($embed)
                    <div class="event-blocks__video">
                        <iframe src="{{ $embed }}" title="Video" loading="lazy" allowfullscreen
                                allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                                referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                @else
                    <div class="event-blocks__buttons">
                        <a class="cta event-blocks__btn" href="{{ $block['url'] }}" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $iconPaths['youtube'] !!}</svg>
                            <span class="cta__label">Watch video</span>
                        </a>
                    </div>
                @endif
                @break

            @case('callout')
                <aside class="event-blocks__callout is-{{ $block['tone'] ?? 'info' }}">
                    @if (! empty($block['title']))
                        <h3>{{ $block['title'] }}</h3>
                    @endif
                    @if (! empty($block['text']))
                        <p>{!! nl2br(EventBlocks::inline($block['text'])) !!}</p>
                    @endif
                </aside>
                @break

            @case('html')
                @php
                    $width = $block['width'] ?? 'normal';
                    $frameHeight = (int) ($block['height'] ?? 0);
                @endphp
                @if (($block['mode'] ?? 'inline') === 'frame')
                    {{-- No allow-same-origin: the code runs, but it cannot reach
                         this page's DOM, cookies or storage, and its CSS stays in. --}}
                    <div class="event-blocks__html is-{{ $width }} is-frame">
                        <iframe class="event-blocks__html-frame"
                                title="Embedded content"
                                loading="lazy"
                                sandbox="allow-scripts allow-popups allow-popups-to-escape-sandbox allow-forms"
                                referrerpolicy="strict-origin-when-cross-origin"
                                @if ($frameHeight > 0)
                                    style="height: {{ $frameHeight }}px"
                                    data-fixed-height="1"
                                @endif
                                srcdoc="{{ EventBlocks::frameDocument($block['code']) }}"></iframe>
                    </div>
                @else
                    <div class="event-blocks__html is-{{ $width }}">{!! $block['code'] !!}</div>
                @endif
                @break

            @case('spacer')
                <div class="event-blocks__spacer is-{{ $block['size'] ?? 'medium' }}" aria-hidden="true"></div>
                @break

            @case('divider')
                <hr class="event-blocks__divider">
                @break
        @endswitch

        @php $i++; @endphp
    @endwhile
</div>

@if ($hasFrames)
    @push('scripts')
        <script>
            /*
                Auto-height for sandboxed html blocks. A sandboxed frame has no
                same-origin access, so it reports its own content height by
                postMessage and we match the sender against each frame's
                contentWindow — the origin of such a frame is "null", so it is
                the window identity, not the origin, that identifies it.
            */
            (function () {
                var MAX = {{ \App\Support\EventBlocks::MAX_FRAME_HEIGHT }};

                window.addEventListener('message', function (event) {
                    var data = event.data;

                    if (!data || data.moraLenzFrame !== true) return;

                    var height = Math.min(MAX, Math.max(80, parseInt(data.height, 10) || 0));
                    var frames = document.querySelectorAll('.event-blocks__html-frame');

                    for (var i = 0; i < frames.length; i++) {
                        if (frames[i].contentWindow === event.source && !frames[i].dataset.fixedHeight) {
                            frames[i].style.height = height + 'px';
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
