<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('profiles', absolute: false) }}"
                    class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200" wire:navigate>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ $searchProfile->name }}</h1>
            </div>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Articles matching your search criteria</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('profiles.edit', $searchProfile, absolute: false) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150"
                wire:navigate>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Edit Profile
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Search -->
            <div>
                <label for="search"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Search</label>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search articles..." />
            </div>

            <!-- Genre Filter -->
            <div>
                <label for="genre"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Genre</label>
                <x-multi-select 
                    wire-model="selectedGenre"
                    placeholder="All Genres"
                    :options="$genres->map(fn($genre) => ['value' => $genre->id, 'label' => $genre->name])->toArray()"
                    searchable
                />
            </div>

            <!-- News Outlet Filter -->
            <div>
                <label for="outlet" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">News
                    Outlet</label>
                <x-multi-select 
                    wire-model="selectedNewsOutlet"
                    placeholder="All News Outlets"
                    :options="$newsOutlets->map(fn($outlet) => ['value' => $outlet->id, 'label' => $outlet->name])->toArray()"
                    searchable
                />
            </div>
        </div>
    </div>

    <!-- Articles Table -->
    @if ($articles->isEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden p-6 text-center">
            <div class="flex flex-col items-center justify-center py-12">
                <div class="mx-auto h-24 w-24 text-zinc-400 dark:text-zinc-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">No articles found</h3>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                    No articles match your current search criteria. Try adjusting your filters or search terms.
                </p>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Article
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Genre
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                News Outlet
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Published
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($articles as $article)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                            {{ $article->title }}
                                        </div>
                                        @if ($article->description)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2">
                                                {{ Str::limit($article->description, 120) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($article->genres->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($article->genres as $genre)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                    {{ $genre->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($article->newsOutlet)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                            {{ $article->newsOutlet->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $article->published_at?->diffForHumans() ?? $article->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if ($article->url)
                                        <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $articles->links() }}
        </div>
    @endif
</div>
