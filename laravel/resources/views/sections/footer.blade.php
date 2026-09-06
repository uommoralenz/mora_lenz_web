<footer class="relative overflow-hidden border-t border-gray-200 bg-gradient-to-b from-slate-100 to-gray-200 py-12 transition-colors duration-300 dark:border-white/5 dark:from-slate-950 dark:to-black">
    <div class="container relative z-10 mx-auto px-6">
        <div class="mb-8 grid gap-8 md:grid-cols-4">
            <div>
                <h3 class="mb-4 text-3xl font-black text-gray-900 dark:text-white">
                    Mora<span class="bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">Lenz</span>
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Capturing moments, creating stories. The premier media club for creative minds.
                </p>
            </div>

            <div>
                <h4 class="mb-4 font-bold text-gray-900 dark:text-white">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="#about" class="text-sm text-gray-600 transition-colors hover:text-purple-500 dark:text-gray-400 dark:hover:text-purple-400" data-scroll-link>About Us</a></li>
                    <li><a href="#gallery" class="text-sm text-gray-600 transition-colors hover:text-purple-500 dark:text-gray-400 dark:hover:text-purple-400" data-scroll-link>Gallery</a></li>
                    <li><a href="#events" class="text-sm text-gray-600 transition-colors hover:text-purple-500 dark:text-gray-400 dark:hover:text-purple-400" data-scroll-link>Events</a></li>
                    <li><a href="#contact" class="text-sm text-gray-600 transition-colors hover:text-purple-500 dark:text-gray-400 dark:hover:text-purple-400" data-scroll-link>Join Us</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 font-bold text-gray-900 dark:text-white">Contact</h4>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        @include('partials.icon', ['name' => 'mail', 'class' => 'h-4 w-4'])
                        <span>info@moralenz.com</span>
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        @include('partials.icon', ['name' => 'phone', 'class' => 'h-4 w-4'])
                        <span>+94 11 234 5678</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        @include('partials.icon', ['name' => 'map-pin', 'class' => 'mt-1 h-4 w-4'])
                        <span>University Campus<br>Colombo, Sri Lanka</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 font-bold text-gray-900 dark:text-white">Follow Us</h4>
                <div class="flex gap-4">
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 transition-colors hover:bg-purple-600 dark:bg-gray-800 group" aria-label="Instagram">
                        @include('partials.icon', ['name' => 'instagram', 'class' => 'h-5 w-5 text-gray-700 group-hover:text-white dark:text-white'])
                    </a>
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 transition-colors hover:bg-blue-600 dark:bg-gray-800 group" aria-label="Facebook">
                        @include('partials.icon', ['name' => 'facebook', 'class' => 'h-5 w-5 text-gray-700 group-hover:text-white dark:text-white'])
                    </a>
                    <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 transition-colors hover:bg-red-600 dark:bg-gray-800 group" aria-label="YouTube">
                        @include('partials.icon', ['name' => 'youtube', 'class' => 'h-5 w-5 text-gray-700 group-hover:text-white dark:text-white'])
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-300 pt-8 text-center dark:border-gray-800">
            <p class="text-sm text-gray-500">(c) 2026 MoraLenz Media Club. All rights reserved.</p>
        </div>
    </div>
</footer>
