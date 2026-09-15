@extends('layouts.app')

@section('title', 'Gallery | Mora Lenz')
@section('description', 'Explore photographs and visual stories from the Mora Lenz community.')

@section('content')
    <section class="gallery-hero">
        <div class="container">
            <span class="gallery-hero__eyebrow">Mora Lenz archive</span>
            <h1 class="page-head__title">Through Our Lens</h1>
            <p class="page-head__lede">Moments, people, and stories captured by the Mora Lenz community.</p>
        </div>
    </section>
    <section class="section gallery-section">
        <div class="container">
            <div class="gallery-album-grid">
                @forelse ($galleries as $gallery)
                    <article class="gallery-album" data-reveal>
                        <a class="gallery-album__link" href="{{ $gallery->facebook_album_url ?: '#' }}" @if($gallery->facebook_album_url) target="_blank" rel="noopener noreferrer" @endif aria-label="{{ $gallery->facebook_album_url ? 'Open '.$gallery->title.' on Facebook' : $gallery->title }}">
                            <div class="gallery-slideshow" data-gallery-slideshow>
                                @forelse ($gallery->images as $image)
                                    <figure class="gallery-slideshow__slide{{ $loop->first ? ' is-active' : '' }}">
                                        <img src="{{ $image->image_url }}" alt="{{ $gallery->title }}" loading="lazy">
                                    </figure>
                                @empty
                                    @if ($gallery->image_url)<img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" loading="lazy">@endif
                                @endforelse
                                @if ($gallery->images->count() > 1)<span class="gallery-slideshow__count">{{ $gallery->images->count() }} photos</span>@endif
                            </div>
                        </a>
                        <div class="gallery-album__body">
                            <div class="gallery-album__meta"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $gallery->images->count() }} {{ $gallery->images->count() === 1 ? 'photo' : 'photos' }}</span></div>
                            <h2 class="gallery-album__title">{{ $gallery->title }}</h2>
                            @if ($gallery->description)<p class="gallery-album__description">{{ $gallery->description }}</p>@endif
                            @if ($gallery->facebook_album_url)<a class="gallery-album__facebook" href="{{ $gallery->facebook_album_url }}" target="_blank" rel="noopener noreferrer">View Facebook album <span aria-hidden="true">↗</span></a>@endif
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
@endsection
