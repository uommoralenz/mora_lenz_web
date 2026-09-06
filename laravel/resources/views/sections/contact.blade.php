<section id="contact" class="bg-gradient-to-b from-slate-100 via-slate-50/80 to-slate-100 py-16 text-gray-900 transition-colors duration-300 dark:from-slate-950 dark:via-slate-900/80 dark:to-slate-950 dark:text-white">
    <div class="container mx-auto px-4">
        <h2 class="mb-8 text-center text-3xl font-bold text-gray-900 dark:text-white">Get In Touch</h2>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <form method="POST" action="{{ route('contact.store') }}" class="rounded-lg border border-purple-200 bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:border-purple-500/20 dark:bg-slate-800/60 dark:shadow-md">
                @csrf

                @if (session('contact_success'))
                    <div class="mb-5 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                        {{ session('contact_success') }}
                    </div>
                @endif

                <div class="mb-4">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="subject" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Subject</label>
                    <select
                        id="subject"
                        name="subject"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white"
                    >
                        <option value="">Select a subject</option>
                        @foreach (['General Inquiry', 'Support', 'Feedback'] as $subject)
                            <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                        @endforeach
                    </select>
                    @error('subject')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="message" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Message</label>
                    <textarea
                        id="message"
                        name="message"
                        required
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-slate-600 dark:bg-slate-900/80 dark:text-white"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-purple-600 py-3 text-white transition hover:bg-purple-700">
                    Send
                </button>
            </form>

            <div class="space-y-6">
                <article class="rounded-lg border border-purple-200 bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:border-purple-500/20 dark:bg-slate-800/60 dark:shadow-md">
                    <div class="mb-2 text-purple-500 dark:text-purple-400">
                        @include('partials.icon', ['name' => 'mail', 'class' => 'h-6 w-6'])
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Email</h3>
                    <p class="text-gray-600 dark:text-gray-300">uommediaunit@gmail.com</p>
                </article>

                <article class="rounded-lg border border-purple-200 bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:border-purple-500/20 dark:bg-slate-800/60 dark:shadow-md">
                    <div class="mb-2 text-purple-500 dark:text-purple-400">
                        @include('partials.icon', ['name' => 'map-pin', 'class' => 'h-6 w-6'])
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Location</h3>
                    <p class="text-gray-600 dark:text-gray-300">University of Moratuwa, Katubedda, Moratuwa, Sri Lanka</p>
                </article>

                <div class="flex space-x-4">
                    <a href="#" class="text-purple-500 transition hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300" aria-label="Facebook">
                        @include('partials.icon', ['name' => 'facebook', 'class' => 'h-6 w-6'])
                    </a>
                    <a href="#" class="text-purple-500 transition hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300" aria-label="Instagram">
                        @include('partials.icon', ['name' => 'instagram', 'class' => 'h-6 w-6'])
                    </a>
                    <a href="#" class="text-purple-500 transition hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300" aria-label="LinkedIn">
                        @include('partials.icon', ['name' => 'linkedin', 'class' => 'h-6 w-6'])
                    </a>
                </div>

                <div class="overflow-hidden rounded-lg">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.948090123456!2d79.9028!3d6.7956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25b0f0f0f0f0f%3A0x0!2sUniversity%20of%20Moratuwa!5e0!3m2!1sen!2slk!4v1234567890"
                        width="100%"
                        height="250"
                        style="border: 0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
