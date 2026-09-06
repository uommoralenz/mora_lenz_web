<section id="team" class="relative overflow-hidden bg-gradient-to-b from-blue-50/50 via-sky-50/40 to-violet-50/50 py-20 transition-colors duration-300 dark:from-blue-950/50 dark:via-sky-950/40 dark:to-violet-950/50">
    <div class="relative z-10 mx-auto max-w-7xl px-6">
        <h2 class="text-center text-4xl font-bold tracking-normal md:text-5xl">
            <span class="text-gray-900 dark:text-white">Our </span>
            <span class="bg-gradient-to-r from-sky-400 to-cyan-400 bg-clip-text text-transparent">Team</span>
        </h2>
        <p class="mx-auto mt-4 max-w-3xl text-center text-sm font-medium leading-relaxed text-gray-700 dark:text-white/80 md:text-base">
            Meet the talented individuals who make Mora Lenz Media Club a thriving community of creative professionals.
        </p>

        <div class="flex justify-center">
            <h3 class="relative mb-6 mt-14 inline-block text-2xl font-semibold tracking-normal text-gray-800 dark:text-gray-100 md:text-3xl">
                Advisors & Senior Leadership
                <span class="absolute -bottom-2 left-1/2 h-[2px] w-24 -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
            </h3>
        </div>

        <div class="mt-6 justify-items-center">
            @forelse ($advisors as $member)
                @include('partials.position-card', ['member' => $member])
            @empty
                <p class="text-center text-gray-600 dark:text-slate-400">No advisors have been added yet.</p>
            @endforelse
        </div>

        <div class="flex justify-center">
            <h3 class="relative mb-6 mt-14 inline-block text-2xl font-semibold tracking-normal text-gray-800 dark:text-gray-100 md:text-3xl">
                Executive Committee 2024/25
                <span class="absolute -bottom-2 left-1/2 h-[2px] w-24 -translate-x-1/2 bg-gradient-to-r from-transparent via-sky-400 to-transparent"></span>
            </h3>
        </div>

        <div class="mt-8 space-y-6">
            @foreach ($executiveRows as $row)
                @continue($row['members']->isEmpty())
                <div
                    @class([
                        'grid justify-items-center gap-4',
                        'mx-auto max-w-2xl grid-cols-1 sm:grid-cols-2' => $row['cols'] === 2,
                        'mx-auto max-w-4xl grid-cols-1 sm:grid-cols-3' => $row['cols'] === 3,
                        'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' => $row['cols'] === 4,
                    ])
                >
                    @foreach ($row['members'] as $member)
                        @include('partials.position-card', ['member' => $member])
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
