<section class="bg-gradient-to-b from-violet-50/50 via-indigo-50/40 to-violet-50/50 py-16 text-gray-900 transition-colors duration-300 dark:from-violet-950/50 dark:via-indigo-950/40 dark:to-violet-950/50 dark:text-white">
    <div class="container mx-auto flex flex-col items-center px-4">
        <h2 class="relative mb-6 mt-14 inline-block text-center text-2xl font-semibold tracking-normal text-gray-800 dark:text-gray-100 md:text-3xl">
            Our Panels & Pillars
            <span class="absolute -bottom-2 left-1/2 h-[2px] w-24 -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
        </h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($panelsPillars as $item)
                <article class="rounded-lg border border-violet-200 bg-white/80 p-6 shadow-lg backdrop-blur-sm transition-all hover:border-violet-400 hover:shadow-xl dark:border-violet-500/20 dark:bg-slate-900/60 dark:shadow-md dark:hover:border-violet-400/40">
                    <div class="mb-4 text-violet-500 dark:text-violet-400">
                        @include('partials.icon', ['name' => $item['icon'], 'class' => 'h-6 w-6'])
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                    <p class="mb-4 text-gray-600 dark:text-gray-300">{{ $item['description'] }}</p>
                    <span class="inline-block rounded-full bg-violet-600/80 px-3 py-1 text-sm font-medium text-white">
                        {{ $item['members'] }} Members
                    </span>
                </article>
            @endforeach
        </div>
    </div>
</section>
