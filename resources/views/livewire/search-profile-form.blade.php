<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('profiles', absolute: false) }}"
                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-2"
                wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Create Search Profile</h1>
        </div>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Customize your news experience by creating a personalized search profile
        </p>
    </div>

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
        <form wire:submit="save" class="p-6 space-y-6">
            <!-- Profile Name -->
            <flux:field>
                <flux:label>Profile Name</flux:label>

                <flux:input wire:model="name" />

                <flux:error name="name" />
            </flux:field>
            <!-- Genres Section -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Select Genres
                </label>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Choose the types of news you're interested in
                </p>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($genres as $genre)
                        <flux:checkbox
                            wire:model="selectedGenres"
                            value="{{ $genre->id }}"
                            label="{{ $genre->name }}"
                        />
                    @endforeach
                </div>
            </div>

            <!-- News Outlets Section -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Select News Outlets
                </label>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Choose your preferred sources for news
                </p>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($newsOutlets as $outlet)
                        <flux:checkbox
                            wire:model="selectedNewsOutlets"
                            value="{{ $outlet->id }}"
                            label="{{ $outlet->name }}"
                        />
                    @endforeach
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button
                    href="{{ route('profiles', absolute: false) }}"
                    variant="ghost"
                    wire:navigate
                    class="mr-3">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Create Profile
                </flux:button>
            </div>
        </form>
    </div>
</div>
