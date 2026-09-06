<section id="gallery" class="relative overflow-hidden bg-gradient-to-b from-indigo-50/60 via-slate-100 to-emerald-50/40 py-20 transition-colors duration-300 dark:from-indigo-950/60 dark:via-slate-900 dark:to-emerald-950/40">
    <div class="container relative z-10 mx-auto px-6">
        <div class="mb-16 text-center">
            <h2 class="mb-4 text-5xl font-black text-gray-900 dark:text-white md:text-6xl">
                Our <span class="bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">Gallery</span>
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">Explore our latest captures and creative work</p>
        </div>

        <div class="mx-auto max-w-7xl" data-gallery-carousel>
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-700 ease-out" data-gallery-track>
                    @foreach ($galleryImages as $image)
                        <div class="w-full flex-shrink-0 px-3 sm:w-1/2 lg:w-1/3">
                            <article class="group relative aspect-[4/3] overflow-hidden rounded-2xl">
                                <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" decoding="async">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <div class="absolute inset-x-0 bottom-0 p-6">
                                        <p class="mb-2 text-sm text-purple-300">{{ $image['category'] }}</p>
                                        <h3 class="text-2xl font-bold text-white">{{ $image['title'] }}</h3>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-center gap-2" data-gallery-dots></div>
        </div>
    </div>
</section>
