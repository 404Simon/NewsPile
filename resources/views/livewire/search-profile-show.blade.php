<div class="px-4 sm:px-6 lg:px-8 py-6" x-data="{ showModal: false, selectedArticleId: null, selectedArticleData: null }"
     @keydown.escape.window="showModal = false; document.body.classList.remove('overflow-hidden')"
     x-effect="if (showModal) { document.body.classList.add('overflow-hidden'); } else { document.body.classList.remove('overflow-hidden'); }">
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

    <!-- Articles -->
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
        <!-- Mobile Card Layout -->
        <div class="block lg:hidden space-y-4">
            @foreach ($articles as $article)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-150">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white leading-snug pr-2">
                            <button @click="selectedArticleId = {{ $article->id }}; selectedArticleData = @js(['title' => $article->title, 'newsOutlet' => $article->newsOutlet?->name, 'genres' => $article->genres->pluck('name'), 'published_at' => $article->published_at?->format('M j, Y'), 'description' => $article->description, 'content' => $article->content ? (new \League\CommonMark\CommonMarkConverter())->convert($article->content)->getContent() : null, 'url' => $article->url]); showModal = true"
                                    class="text-left hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-150">
                                {{ $article->title }}
                            </button>
                        </h3>
                        @if ($article->url)
                            <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer"
                                class="flex-shrink-0 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                    </path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    @if ($article->description)
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-3 line-clamp-2">
                            {{ Str::limit($article->description, 120) }}
                        </p>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-3">
                        @if ($article->newsOutlet)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                {{ $article->newsOutlet->name }}
                            </span>
                        @endif

                        @if ($article->genres->isNotEmpty())
                            @foreach ($article->genres as $genre)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $article->published_at?->diffForHumans() ?? $article->created_at->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table Layout -->
        <div class="hidden lg:block bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
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
                                            <a href="{{ route('articles.show', $article, absolute: false) }}"
                                               @click.prevent="selectedArticleId = {{ $article->id }}; selectedArticleData = @js(['title' => $article->title, 'newsOutlet' => $article->newsOutlet?->name, 'genres' => $article->genres->pluck('name'), 'published_at' => $article->published_at?->format('M j, Y'), 'description' => $article->description, 'content' => $article->content ? (new \League\CommonMark\CommonMarkConverter())->convert($article->content)->getContent() : null, 'url' => $article->url]); showModal = true"
                                               @click.middle=""
                                               class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-150">
                                                {{ $article->title }}
                                            </a>
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

    <!-- Article Modal -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-50">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
             @click="showModal = false"></div>

        <!-- Modal Content -->
        <div class="fixed inset-0 lg:inset-8 lg:mx-auto lg:max-w-4xl bg-white dark:bg-zinc-900 shadow-2xl flex flex-col lg:rounded-2xl">
            <!-- Header with close button -->
            <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 flex-shrink-0 lg:rounded-t-2xl">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white flex-1 pr-4 line-clamp-2" x-text="selectedArticleData?.title"></h2>
                <button @click="showModal = false"
                        class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto overscroll-contain">
                <div class="p-4 space-y-4">
                    <!-- Article Meta -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <template x-if="selectedArticleData?.newsOutlet">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200" x-text="selectedArticleData.newsOutlet"></span>
                        </template>

                        <template x-if="selectedArticleData?.genres?.length">
                            <template x-for="genre in selectedArticleData.genres" :key="genre">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200" x-text="genre"></span>
                            </template>
                        </template>

                        <template x-if="selectedArticleData?.published_at">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200" x-text="selectedArticleData.published_at"></span>
                        </template>
                    </div>

                    <!-- Description -->
                    <template x-if="selectedArticleData?.description">
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-xl p-4 mb-4">
                            <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed" x-text="selectedArticleData.description"></p>
                        </div>
                    </template>

                    <!-- Content -->
                    <template x-if="selectedArticleData?.content">
                        <div class="prose prose-sm prose-zinc dark:prose-invert max-w-none" x-html="selectedArticleData.content"></div>
                    </template>
                </div>
            </div>

            <!-- Bottom Action Bar -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 flex gap-3 flex-shrink-0 lg:rounded-b-2xl">
                <template x-if="selectedArticleData?.url">
                    <a :href="selectedArticleData.url" target="_blank" rel="noopener noreferrer"
                       class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Read Original
                    </a>
                </template>
                <button @click="navigator.share ? navigator.share({title: selectedArticleData?.title, url: window.location.origin + '/articles/' + selectedArticleId}) : navigator.clipboard.writeText(window.location.origin + '/articles/' + selectedArticleId).then(() => alert('Link copied!'))"
                        class="px-4 py-3 bg-zinc-100 dark:bg-zinc-700 border border-transparent rounded-xl font-semibold text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
