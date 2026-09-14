@extends('layouts.app')

@section('title', $service['title'].' | Mora Lenz')
@section('description', $service['description'])

@section('content')

    <div class="container">
        {{-- ------------------------------------------------------ header --}}
        <section class="service-hero">
            <div class="service-hero__text">
                <span class="service-hero__eyebrow">
                    @include('partials.icons', ['icon' => $service['icon']])
                    Professional Service
                </span>

                <h1 class="service-hero__title">{{ $service['title'] }}</h1>

                <p class="service-hero__desc">{{ $service['description'] }}</p>
            </div>

            <div class="service-hero__media">
                @if (count($carousel) > 0)
                    <div class="carousel" data-carousel>
                        @foreach ($carousel as $index => $image)
                            <div class="carousel__slide {{ $index === 0 ? 'is-active' : '' }}">
                                <img src="{{ $image }}" alt=""
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="placeholder-media">
                        @include('partials.icons', ['icon' => $service['icon']])
                    </div>
                @endif

                <div class="service-hero__features">
                    @foreach ($service['features'] as $feature)
                        <span>{{ $feature }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ---------------------------------------------------- packages --}}
        <section class="section" style="padding-top: 0;">
            <div class="section__head">
                <h2 class="section__title">Available Packages</h2>
            </div>

            <div class="card-grid">
                @forelse ($packages as $package)
                    <article class="package-card" data-reveal>
                        <div class="package-card__media">
                            @if ($package->image_url)
                                <img src="{{ $package->image_url }}" alt="{{ $package->name }}" loading="lazy">
                            @else
                                <div class="placeholder-media">
                                    @include('partials.icons', ['icon' => 'image'])
                                </div>
                            @endif

                            @if ($package->offered_price !== null)
                                <span class="package-card__offer">Special Offer</span>
                            @endif
                        </div>

                        <div class="package-card__body">
                            <h3 class="package-card__name">{{ $package->name }}</h3>

                            <div class="package-card__price">
                                @if ($package->offered_price !== null)
                                    <strong>LKR {{ number_format((float) $package->offered_price) }}</strong>
                                    <del>LKR {{ number_format((float) $package->price) }}</del>
                                @else
                                    <strong>LKR {{ number_format((float) $package->price) }}</strong>
                                @endif
                            </div>

                            @if (count($package->points()) > 0)
                                <ul class="package-card__points">
                                    @foreach ($package->points() as $point)
                                        <li>
                                            @include('partials.icons', ['icon' => 'check'])
                                            <span>{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <a class="package-card__cta"
                               href="{{ route('home') }}#contact"
                               aria-label="Enquire about the {{ $package->name }} package">
                                Choose Package
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <p>No packages currently available for this service.</p>
                        <p>Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

@endsection
