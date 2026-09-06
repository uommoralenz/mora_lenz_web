@php
    $navItems = [
        ['label' => 'Home', 'href' => '#home'],
        [
            'label' => 'About',
            'children' => [
                ['label' => 'Our Story', 'href' => '#about', 'description' => 'Learn about Mora Lenz Media Club'],
                ['label' => 'Our Team', 'href' => '#team', 'description' => 'Meet our talented members'],
            ],
        ],
        ['label' => 'Events', 'href' => '#events'],
        ['label' => 'Gallery', 'href' => '#gallery'],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
@endphp

<nav
    id="site-nav"
    class="fixed inset-x-0 top-0 z-50 bg-transparent transition-all duration-300"
    data-navbar
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between md:h-20">
            <a href="#home" class="flex items-center gap-2 group" data-scroll-link>
                <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full transition-transform duration-300 group-hover:scale-105 md:h-20 md:w-20">
                    <img src="{{ asset('favicon.ico') }}" alt="Mora Lenz Logo" class="h-full w-full object-cover">
                </span>
                <span class="hidden text-xl font-bold text-gray-900 dark:text-white sm:block">Mora Lenz</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @foreach ($navItems as $item)
                    <div class="relative" data-dropdown>
                        @if (isset($item['children']))
                            <button
                                type="button"
                                class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-900/5 hover:text-gray-900 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white"
                                data-dropdown-button
                            >
                                {{ $item['label'] }}
                                @include('partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4 transition-transform duration-200'])
                            </button>

                            <div
                                class="pointer-events-none absolute left-0 top-full w-64 -translate-y-2 pt-2 opacity-0 transition-all duration-300"
                                data-dropdown-menu
                            >
                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white/95 shadow-xl shadow-black/10 backdrop-blur-md dark:border-white/10 dark:bg-slate-800/95 dark:shadow-black/20">
                                    <div class="p-2">
                                        @foreach ($item['children'] as $child)
                                            <a
                                                href="{{ $child['href'] }}"
                                                class="block rounded-lg px-4 py-3 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-white/10"
                                                data-scroll-link
                                            >
                                                <span class="text-sm font-medium text-gray-900 transition-colors group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                                                    {{ $child['label'] }}
                                                </span>
                                                <span class="mt-0.5 block text-xs text-gray-500 dark:text-slate-400">
                                                    {{ $child['description'] }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a
                                href="{{ $item['href'] }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-900/5 hover:text-gray-900 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white"
                                data-scroll-link
                            >
                                {{ $item['label'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="#contact" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:text-gray-900 dark:text-slate-300 dark:hover:text-white" data-scroll-link>
                    Join Us
                </a>
                <a href="#events" class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:from-emerald-600 hover:to-teal-700 hover:shadow-emerald-500/40" data-scroll-link>
                    Explore Events
                    @include('partials.icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4'])
                </a>
                <button
                    type="button"
                    class="relative flex h-9 w-9 items-center justify-center rounded-full bg-gray-900/10 transition-all duration-300 hover:bg-gray-900/20 dark:bg-white/10 dark:hover:bg-white/20"
                    data-theme-toggle
                    aria-label="Toggle theme"
                >
                    @include('partials.icon', ['name' => 'sun', 'class' => 'hidden h-5 w-5 text-amber-400 dark:block'])
                    @include('partials.icon', ['name' => 'moon', 'class' => 'h-5 w-5 text-violet-500 dark:hidden'])
                </button>
            </div>

            <div class="flex items-center gap-2 md:hidden">
                <button
                    type="button"
                    class="relative flex h-8 w-8 items-center justify-center rounded-full bg-gray-900/10 transition-all duration-300 hover:bg-gray-900/20 dark:bg-white/10 dark:hover:bg-white/20"
                    data-theme-toggle
                    aria-label="Toggle theme"
                >
                    @include('partials.icon', ['name' => 'sun', 'class' => 'hidden h-5 w-5 text-amber-400 dark:block'])
                    @include('partials.icon', ['name' => 'moon', 'class' => 'h-5 w-5 text-violet-500 dark:hidden'])
                </button>
                <button
                    type="button"
                    class="relative flex h-10 w-10 items-center justify-center rounded-lg transition-colors duration-200 hover:bg-gray-900/10 dark:hover:bg-white/10"
                    data-menu-button
                    aria-label="Toggle menu"
                    aria-expanded="false"
                >
                    <span class="flex h-5 w-6 flex-col justify-between">
                        <span class="h-0.5 w-full rounded-full bg-gray-900 transition-all duration-300 dark:bg-white" data-menu-line="top"></span>
                        <span class="h-0.5 w-full rounded-full bg-gray-900 transition-all duration-300 dark:bg-white" data-menu-line="middle"></span>
                        <span class="h-0.5 w-full rounded-full bg-gray-900 transition-all duration-300 dark:bg-white" data-menu-line="bottom"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div
        class="pointer-events-none absolute left-0 right-0 top-full -translate-y-4 border-t border-gray-200 bg-white/95 opacity-0 backdrop-blur-md transition-all duration-300 dark:border-white/10 dark:bg-slate-900/95 md:hidden"
        data-mobile-menu
    >
        <div class="mx-auto max-w-7xl px-4 py-4">
            @foreach ($navItems as $item)
                <div class="border-b border-gray-200 last:border-0 dark:border-white/5">
                    @if (isset($item['children']))
                        <button
                            type="button"
                            class="flex w-full items-center justify-between px-4 py-3 font-medium text-gray-900 dark:text-white"
                            data-mobile-dropdown-button
                        >
                            {{ $item['label'] }}
                            @include('partials.icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5 transition-transform duration-200'])
                        </button>
                        <div class="max-h-0 overflow-hidden transition-all duration-300" data-mobile-dropdown-menu>
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" class="block px-8 py-2.5 text-gray-500 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400" data-scroll-link>
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ $item['href'] }}" class="block px-4 py-3 font-medium text-gray-900 transition-colors hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400" data-scroll-link>
                            {{ $item['label'] }}
                        </a>
                    @endif
                </div>
            @endforeach

            <div class="mt-4 space-y-3">
                <a href="#events" class="block w-full rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-3 text-center font-semibold text-white" data-scroll-link>
                    Explore Events
                </a>
                <a href="#contact" class="block w-full rounded-lg border border-gray-300 px-5 py-3 text-center font-medium text-gray-700 hover:bg-gray-100 dark:border-white/20 dark:text-slate-300 dark:hover:bg-white/5" data-scroll-link>
                    Join Us
                </a>
            </div>
        </div>
    </div>
</nav>
