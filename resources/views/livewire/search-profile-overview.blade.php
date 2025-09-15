<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header with title and create button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Search Profiles</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage your news preferences and sources</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('profiles.create', absolute: false) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150"
               wire:navigate>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Profile
            </a>
        </div>
    </div>

    <!-- Empty state -->
    @if ($searchProfiles->isEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden p-6 text-center">
            <div class="flex flex-col items-center justify-center py-12">
                <div class="mx-auto h-24 w-24 text-zinc-400 dark:text-zinc-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">No search profiles yet</h3>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                    Create a search profile to customize your news feed with your favorite genres and news outlets.
                </p>
                <div class="mt-6">
                    <a href="{{ route('profiles.create', absolute: false) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150"
                       wire:navigate>
                        Create Your First Profile
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Card grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($searchProfiles as $profile)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg border border-zinc-100 dark:border-zinc-700">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white truncate">{{ $profile->name }}</h2>
                            <div class="flex">
                                <a href="{{ route('profiles.edit', $profile, absolute: false) }}"
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                                   wire:navigate>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <button wire:click="confirmDelete({{ $profile->id }})"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                            Created {{ $profile->created_at->diffForHumans() }}
                        </p>

                        <div class="mt-6 space-y-4">
                            <!-- Genres section -->
                            <div>
                                <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-200">Genres</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($profile->genres as $genre)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            {{ $genre->name }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">No genres selected</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- News Outlets section -->
                            <div>
                                <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-200">News Outlets</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($profile->newsOutlets as $outlet)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                            {{ $outlet->name }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">No news outlets selected</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $searchProfiles->links() }}
        </div>
    @endif

    <!-- Delete confirmation modal -->
    <flux:modal wire:model.self="showDeleteModal" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Search Profile</flux:heading>
                <flux:text class="mt-2">
                    <p>Are you sure you want to delete the search profile "{{ $profileToDelete?->name }}"?</p>
                    <p>This action cannot be undone.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="delete">Delete Profile</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
