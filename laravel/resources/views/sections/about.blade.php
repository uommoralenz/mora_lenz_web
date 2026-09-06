<section id="about" class="relative overflow-hidden bg-gradient-to-b from-gray-50 via-purple-50/40 to-indigo-50/60 py-20 transition-colors duration-300 dark:from-black dark:via-purple-950/40 dark:to-indigo-950/60">
    <div class="container relative z-10 mx-auto px-6">
        <div class="grid items-center gap-16 md:grid-cols-2">
            <div>
                <h2 class="mb-6 text-5xl font-black text-gray-900 dark:text-white md:text-6xl">
                    About <span class="bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">MoraLenz</span>
                </h2>
                <p class="mb-6 text-lg leading-relaxed text-gray-700 dark:text-gray-300">
                    MoraLenz is the premier media club at our university, dedicated to nurturing creativity and technical excellence in photography, videography, and digital media production.
                </p>
                <p class="mb-8 text-lg leading-relaxed text-gray-700 dark:text-gray-300">
                    Founded by passionate students, we have grown into a thriving community of visual storytellers, providing members with cutting-edge equipment, expert mentorship, and countless opportunities to showcase their talent.
                </p>
                <a href="#team" class="inline-block rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-4 font-semibold text-white transition-transform hover:scale-105" data-scroll-link>
                    Learn More
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach ($features as $feature)
                    <article class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-100 p-6 shadow-lg transition-colors hover:border-purple-500 dark:border-gray-700 dark:from-gray-900 dark:to-gray-800 dark:shadow-none">
                        <div class="mb-4 text-purple-500 dark:text-purple-400">
                            @include('partials.icon', ['name' => $feature['icon'], 'class' => 'h-12 w-12'])
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
