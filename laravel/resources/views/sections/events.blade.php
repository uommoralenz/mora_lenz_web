<section id="events" class="relative overflow-hidden bg-gradient-to-b from-emerald-50/40 via-cyan-50/30 to-blue-50/50 px-6 py-20 text-gray-900 transition-colors duration-300 dark:from-emerald-950/40 dark:via-cyan-950/30 dark:to-blue-950/50 dark:text-white">
    <div class="relative z-10 mx-auto max-w-7xl">
        <div class="mb-20">
            <h2 class="mb-12 text-center text-4xl font-bold md:text-5xl">Upcoming Events</h2>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($upcomingEvents as $event)
                    @php
                        $cover = $event->coverImage();
                        $coverSrc = \Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset(ltrim($cover, '/'));
                    @endphp
                    <article class="group relative cursor-pointer overflow-hidden rounded-lg shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="relative h-96 w-full">
                            <img src="{{ $coverSrc }}" alt="{{ $event->title }}" class="h-full w-full object-cover" loading="lazy" decoding="async">

                            <div
                                class="absolute right-4 top-4 flex h-16 w-16 flex-col items-center justify-center rounded-full bg-black/50 backdrop-blur-sm"
                                data-countdown
                                data-event-date="{{ $event->event_date->toIso8601String() }}"
                            >
                                <p class="text-2xl font-bold leading-none text-yellow-400" data-countdown-days>0</p>
                                <p class="mt-0.5 text-[9px] text-white" data-countdown-label>DAYS</p>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/60 to-transparent backdrop-blur-sm"></div>
                            <div class="absolute inset-x-0 bottom-0 p-4">
                                <h3 class="mb-1 text-xl font-bold text-white drop-shadow-lg">{{ $event->title }}</h3>
                                @if ($event->location)
                                    <p class="mb-1 text-xs text-cyan-300 drop-shadow-md">Location: {{ $event->location }}</p>
                                @endif
                                <p class="mb-1 line-clamp-2 text-xs text-zinc-200 drop-shadow-md">{{ $event->description }}</p>
                                <p class="text-xs text-zinc-300 drop-shadow-md">
                                    Date: {{ $event->event_date->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-600 dark:border-white/10 dark:text-gray-400 md:col-span-2 lg:col-span-3">
                        No upcoming events are available yet.
                    </p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="mb-12 text-center text-4xl font-bold md:text-5xl">Past Events</h2>

            <div class="relative mx-auto max-w-7xl px-12" data-past-carousel>
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" data-past-track>
                        @forelse ($pastEvents as $event)
                            @php
                                $cover = $event->coverImage();
                                $coverSrc = \Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset(ltrim($cover, '/'));
                            @endphp
                            <div class="w-full flex-shrink-0 px-3 md:w-1/2 lg:w-1/3">
                                <article class="group relative cursor-pointer overflow-hidden rounded-lg">
                                    <div class="relative h-[512px] w-full">
                                        <img src="{{ $coverSrc }}" alt="{{ $event->title }}" class="h-full w-full object-cover grayscale transition-all duration-300 group-hover:grayscale-0" loading="lazy" decoding="async">
                                        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/50 to-transparent backdrop-blur-sm"></div>
                                        <div class="absolute inset-x-0 bottom-0 p-6">
                                            <h3 class="mb-2 text-2xl font-bold text-white drop-shadow-lg">{{ $event->title }}</h3>
                                            <p class="mb-2 line-clamp-3 text-sm text-zinc-200 drop-shadow-md">{{ $event->description }}</p>
                                            <p class="text-sm text-cyan-300 drop-shadow-md">
                                                {{ $event->event_date->format('F j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <p class="w-full rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-600 dark:border-white/10 dark:text-gray-400">
                                No past events are available yet.
                            </p>
                        @endforelse
                    </div>
                </div>

                @if ($pastEvents->count() > 3)
                    <button
                        type="button"
                        class="absolute left-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-3 text-white transition-all duration-300 hover:-translate-x-1 hover:scale-110 hover:bg-black/70 active:scale-95"
                        data-past-prev
                        aria-label="Previous events"
                    >
                        @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-6 w-6'])
                    </button>

                    <button
                        type="button"
                        class="absolute right-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-3 text-white transition-all duration-300 hover:translate-x-1 hover:scale-110 hover:bg-black/70 active:scale-95"
                        data-past-next
                        aria-label="Next events"
                    >
                        @include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-6 w-6'])
                    </button>

                    <div class="mt-8 flex justify-center gap-2" data-past-dots></div>
                @endif
            </div>
        </div>
    </div>
</section>
