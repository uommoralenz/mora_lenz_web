@extends('layouts.app')

@section('title', 'Gallery | Mora Lenz')
@section('description', 'Explore photographs and visual stories from the Mora Lenz community.')

@section('content')
    @php
        $photoTotal = $galleries->getCollection()->sum(fn ($g) => max($g->images->count(), $g->image_url ? 1 : 0));
    @endphp

    <section class="gallery-hero">
        <div class="container">
            <span class="gallery-hero__eyebrow">Mora Lenz archive</span>
            <h1 class="page-head__title">Through Our Lens</h1>
            <p class="page-head__lede">Moments, people, and stories captured by the Mora Lenz community.</p>

            @if ($galleries->total() > 0)
                <ul class="gallery-stats" aria-label="Gallery summary">
                    <li><strong>{{ $galleries->total() }}</strong> {{ $galleries->total() === 1 ? 'album' : 'albums' }}</li>
                    <li><strong>{{ $photoTotal }}</strong> {{ $photoTotal === 1 ? 'photo' : 'photos' }} {{ $galleries->hasPages() ? 'on this page' : '' }}</li>
                </ul>
            @endif
        </div>
    </section>

    <section class="section gallery-section">
        <div class="container">
            <div class="gallery-album-grid">
                @forelse ($galleries as $gallery)
                    @php
                        // Every photo the lightbox can show; fall back to the cover.
                        $photos = $gallery->images->map(fn ($i) => [
                            'src' => $i->image_url,
                            'caption' => $i->description,
                        ])->values();

                        if ($photos->isEmpty() && $gallery->image_url) {
                            $photos = collect([['src' => $gallery->image_url, 'caption' => null]]);
                        }

                        $thumbs = $photos->take(3)->pluck('src');
                    @endphp

                    <article class="gallery-album" data-reveal>
                        @if ($photos->isNotEmpty())
                            <button type="button" class="gallery-album__open"
                                    data-lightbox-open
                                    data-title="{{ $gallery->title }}"
                                    data-photos="{{ json_encode($photos) }}"
                                    aria-label="Open {{ $gallery->title }} — {{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}">
                                <span class="gallery-mosaic gallery-mosaic--{{ min($thumbs->count(), 3) }}">
                                    @foreach ($thumbs as $src)
                                        <img src="{{ $src }}" alt="" loading="lazy">
                                    @endforeach
                                </span>
                                <span class="gallery-album__badge">
                                    @include('partials.icons', ['icon' => 'image'])
                                    {{ $photos->count() }}
                                </span>
                                <span class="gallery-album__view">View photos</span>
                            </button>
                        @else
                            <div class="gallery-album__open gallery-album__open--empty">
                                <span class="gallery-album__view">No photos yet</span>
                            </div>
                        @endif

                        <div class="gallery-album__body">
                            <div class="gallery-album__meta">
                                <span>{{ str_pad((string) (($galleries->currentPage() - 1) * $galleries->perPage() + $loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $photos->count() }} {{ $photos->count() === 1 ? 'photo' : 'photos' }}</span>
                            </div>
                            <h2 class="gallery-album__title">{{ $gallery->title }}</h2>
                            @if ($gallery->description)
                                <p class="gallery-album__description">{{ $gallery->description }}</p>
                            @endif
                            @if ($gallery->facebook_album_url)
                                <a class="gallery-album__facebook" href="{{ $gallery->facebook_album_url }}" target="_blank" rel="noopener noreferrer">
                                    Open Facebook album <span aria-hidden="true">↗</span>
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="empty-state"><p>Our next collection is on its way. Check back soon.</p></div>
                @endforelse
            </div>

            @if ($galleries->hasPages())
                <nav class="gallery-pagination" aria-label="Gallery pages">
                    @if ($galleries->currentPage() > 1)
                        <a class="cta cta--ghost" href="{{ \App\Support\Links::gallery() }}?page={{ $galleries->currentPage() - 1 }}">Previous</a>
                    @endif
                    <span>Page {{ $galleries->currentPage() }} of {{ $galleries->lastPage() }}</span>
                    @if ($galleries->hasMorePages())
                        <a class="cta cta--ghost" href="{{ \App\Support\Links::gallery() }}?page={{ $galleries->currentPage() + 1 }}">Next</a>
                    @endif
                </nav>
            @endif
        </div>
    </section>

    {{-- One shared photo viewer; site.js fills it from the clicked album. --}}
    <div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="Photo viewer">
        <div class="lightbox__bar">
            <div class="lightbox__heading">
                <span class="lightbox__title" data-lightbox-title></span>
                <span class="lightbox__count" data-lightbox-count></span>
            </div>
            <button type="button" class="lightbox__btn" data-lightbox-close aria-label="Close viewer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="lightbox__stage">
            <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="Previous photo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <figure class="lightbox__figure">
                <img class="lightbox__img" data-lightbox-img alt="">
                <figcaption class="lightbox__caption" data-lightbox-caption></figcaption>
            </figure>
            <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="Next photo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        <div class="lightbox__strip" data-lightbox-strip></div>
    </div>
@endsection
