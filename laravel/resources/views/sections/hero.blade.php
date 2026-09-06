<section
    id="home"
    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 transition-colors duration-300 dark:from-purple-950 dark:via-blue-950 dark:to-black"
>
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1758270703733-3663d99c9dd7?w=1920')] bg-cover bg-center opacity-20"></div>

    <div class="absolute left-10 top-24 hidden animate-float text-purple-400/30 md:block">
        @include('partials.icon', ['name' => 'camera', 'class' => 'h-16 w-16'])
    </div>
    <div class="absolute bottom-24 right-10 hidden animate-float-reverse text-blue-400/30 md:block">
        @include('partials.icon', ['name' => 'film', 'class' => 'h-16 w-16'])
    </div>

    <div class="relative z-10 max-w-5xl px-6 text-center">
        <h1 class="text-6xl font-black tracking-normal text-gray-900 dark:text-white sm:text-7xl md:text-9xl">
            Mora<span class="bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">Lenz</span>
        </h1>
        <p class="mt-6 text-xl text-gray-700 dark:text-gray-300 md:text-2xl">
            Capturing Moments, Creating Stories
        </p>
        <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600 dark:text-gray-400">
            Join the premier media club where creativity meets passion. From photography to videography, we bring your vision to life.
        </p>

        <div class="mt-12 flex flex-wrap justify-center gap-4">
            <a href="#contact" class="rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-4 font-semibold text-white transition-transform hover:scale-105" data-scroll-link>
                Join Us Today
            </a>
            <a href="#gallery" class="rounded-full border border-gray-900/20 bg-gray-900/10 px-8 py-4 font-semibold text-gray-900 backdrop-blur-sm transition-colors hover:bg-gray-900/20 dark:border-white/20 dark:bg-white/10 dark:text-white dark:hover:bg-white/20" data-scroll-link>
                View Our Work
            </a>
        </div>
    </div>

    <a href="#about" class="absolute bottom-10 left-1/2 z-10 -translate-x-1/2 animate-bounce" data-scroll-link aria-label="Scroll to about section">
        <span class="flex h-10 w-6 justify-center rounded-full border-2 border-gray-900/50 pt-2 dark:border-white/50">
            <span class="h-2 w-1 rounded-full bg-gray-900/50 dark:bg-white/50"></span>
        </span>
    </a>

</section>
