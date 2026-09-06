<section class="relative overflow-hidden bg-gradient-to-b from-violet-100/50 via-fuchsia-100/30 to-slate-100 py-20 transition-colors duration-300 dark:from-violet-950/50 dark:via-fuchsia-950/30 dark:to-slate-950">
    <div class="container relative z-10 mx-auto px-6">
        <div class="mb-16 text-center">
            <h2 class="mb-4 text-5xl font-black text-gray-900 dark:text-white md:text-6xl">
                Latest <span class="bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">Updates</span>
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">Do not miss out on exciting opportunities</p>
        </div>

        <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-3">
            @foreach ($ads as $ad)
                <article class="group relative cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br {{ $ad['color'] }} p-8 transition-transform duration-300 hover:scale-105">
                    <div class="absolute right-0 top-0 h-32 w-32 -translate-y-16 translate-x-16 rounded-full bg-white/10 transition-transform duration-700 group-hover:scale-150"></div>

                    <div class="relative z-10">
                        @include('partials.icon', ['name' => $ad['icon'], 'class' => 'mb-4 h-16 w-16 text-white'])
                        <h3 class="mb-2 text-2xl font-bold text-white">{{ $ad['title'] }}</h3>
                        <p class="mb-4 text-lg text-white/90">{{ $ad['subtitle'] }}</p>
                        <div class="inline-block rounded-full bg-white/20 px-4 py-2 backdrop-blur-sm">
                            <p class="text-sm font-semibold text-white">{{ $ad['date'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
