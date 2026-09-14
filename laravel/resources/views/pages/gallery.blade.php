@extends('layouts.app')

@section('title', 'Gallery | Mora Lenz')
@section('description', 'Explore photographs and visual stories from the Mora Lenz community.')

@section('content')
    <div class="container page-head">
        <h1 class="page-head__title">Through Our Lens</h1>
        <p class="page-head__lede">Moments, people, and stories captured by the Mora Lenz community.</p>
    </div>
    <section class="section">
        <div class="container">
            <div class="card-grid">
                @forelse ($galleries as $gallery)
                    <article class="event-card" data-reveal>
                        <div class="event-card__media">
                            @if ($gallery->image_url)
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" loading="lazy">
                            @endif
                        </div>
                        <div class="event-card__body">
                            <h2 class="event-card__title">{{ $gallery->title }}</h2>
                            <p class="event-card__desc">{{ $gallery->description }}</p>
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
