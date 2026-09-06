@php
    $photoUrl = $member->photo_url ?: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60';
    $photoSrc = \Illuminate\Support\Str::startsWith($photoUrl, ['http://', 'https://'])
        ? $photoUrl
        : asset(ltrim($photoUrl, '/'));
@endphp

@if ($member->card_size === 'lg')
    <article class="mx-auto flex w-full max-w-4xl flex-col items-center gap-4 overflow-hidden rounded-2xl border border-gray-200 bg-white/80 px-6 py-6 text-center shadow-lg backdrop-blur transition-colors duration-300 dark:border-white/10 dark:bg-slate-800/70 md:flex-row md:gap-6 md:px-8 md:text-left">
        <div class="h-24 w-24 flex-shrink-0 rounded-full bg-gradient-to-b from-blue-500 to-indigo-500 p-[3px] md:h-28 md:w-28">
            <img src="{{ $photoSrc }}" alt="{{ $member->display_name }}" class="h-full w-full rounded-full bg-gray-100 object-cover dark:bg-slate-900" loading="lazy" decoding="async">
        </div>

        <div class="flex-1">
            <p class="text-sm italic leading-relaxed text-gray-600 dark:text-slate-100/80 md:text-base">
                "{{ $member->bio }}"
            </p>
            <h3 class="mt-4 text-xl font-bold text-sky-600 dark:text-sky-400 md:text-2xl">
                {{ $member->display_name }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-300">
                {{ $member->position }}
            </p>
        </div>
    </article>
@else
    <article class="group relative w-full max-w-[280px] cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white/80 px-4 py-5 text-center shadow-lg backdrop-blur transition-all duration-300 hover:scale-105 hover:border-sky-400/50 hover:shadow-xl hover:shadow-sky-500/20 dark:border-white/10 dark:bg-slate-800/70">
        <div class="mx-auto mb-3 h-20 w-20 rounded-full bg-gradient-to-b from-blue-500 to-indigo-500 p-[2px] transition-transform duration-300 group-hover:scale-110">
            <img src="{{ $photoSrc }}" alt="{{ $member->display_name }}" class="h-full w-full rounded-full bg-gray-100 object-cover dark:bg-slate-900" loading="lazy" decoding="async">
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $member->display_name }}</h3>
        <div class="mt-1">
            <span class="inline-block text-center text-sm font-medium text-sky-600 dark:text-sky-400">
                {{ $member->position }}
            </span>
        </div>
        <p class="mx-auto mt-3 text-center text-sm leading-relaxed text-gray-600 dark:text-slate-100/80">
            "{{ $member->bio }}"
        </p>
    </article>
@endif
