<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('profiles', absolute: false) }}"
               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-2"
               wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Edit Search Profile</h1>
        </div>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Update your news preferences and sources
        </p>
    </div>

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
        <form wire:submit="save" class="p-6 space-y-6">
            <!-- Profile Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Profile Name
                </label>
                <div class="mt-1">
                    <input
                        id="name"
                        type="text"
                        wire:model="name"
                        class="block w-full rounded-md border-zinc-300 dark:border-zinc-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-700 dark:text-white sm:text-sm"
                        placeholder="Enter a name for this profile"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Genres Section -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Select Genres
                </label>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Choose the types of news you're interested in
                </p>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($genres as $genre)
                        <label class="relative flex items-start py-2">
                            <div class="min-w-0 flex-1 text-sm">
                                <div class="flex items-center">
                                    <input
                                        id="genre-{{ $genre->id }}"
                                        type="checkbox"
                                        wire:model="selectedGenres"
                                        value="{{ $genre->id }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700 dark:focus:ring-indigo-600 dark:focus:ring-offset-zinc-800"
                                    >
                                    <span class="ml-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $genre->name }}</span>
                                </div>
                            </div>
                        </label>
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
                    @foreach($newsOutlets as $outlet)
                        <label class="relative flex items-start py-2">
                            <div class="min-w-0 flex-1 text-sm">
                                <div class="flex items-center">
                                    <input
                                        id="outlet-{{ $outlet->id }}"
                                        type="checkbox"
                                        wire:model="selectedNewsOutlets"
                                        value="{{ $outlet->id }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500 dark:border-zinc-600 dark:bg-zinc-700 dark:focus:ring-emerald-600 dark:focus:ring-offset-zinc-800"
                                    >
                                    <span class="ml-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $outlet->name }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <a
                    href="{{ route('profiles', absolute: false) }}"
                    class="inline-flex justify-center py-2 px-4 border border-zinc-300 dark:border-zinc-600 shadow-sm text-sm font-medium rounded-md text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800 mr-3"
                    wire:navigate
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800"
                >
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
