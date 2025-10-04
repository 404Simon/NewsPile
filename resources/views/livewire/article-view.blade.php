<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 mb-4">
            <a href="{{ route('home') }}"
                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <nav class="text-sm text-zinc-500 dark:text-zinc-400">
                <span>Articles</span>
            </nav>
        </div>
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white leading-tight">
            {{ $article->title }}
        </h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Article Details Section -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Article Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Genres -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Genres</label>
                        @if ($article->genres->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($article->genres as $genre)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ $genre->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">-</span>
                        @endif
                    </div>

                    <!-- News Outlet -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">News Outlet</label>
                        @if ($article->newsOutlet)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                {{ $article->newsOutlet->name }}
                            </span>
                        @else
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">-</span>
                        @endif
                    </div>

                    <!-- Published Date -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Published</label>
                        <span class="text-sm text-zinc-900 dark:text-white">
                            {{ $article->published_at?->format('M j, Y') ?? '-' }}
                        </span>
                    </div>

                    <!-- Created Date -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Added</label>
                        <span class="text-sm text-zinc-900 dark:text-white">
                            {{ $article->created_at->format('M j, Y \a\t g:i A') }}
                        </span>
                    </div>
                </div>

                @if ($article->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ $article->description }}
                        </p>
                    </div>
                @endif

                @if ($article->url)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Source URL</label>
                        <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                            {{ $article->url }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            @if ($article->content)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
                    <div class="prose prose-zinc dark:prose-invert max-w-none">
                        {!! $this->renderedContent !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Article Actions</h3>

                @if ($article->url)
                    <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150 mb-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                            </path>
                        </svg>
                        View Original
                    </a>
                @endif

                <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($article->title) }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link copied to clipboard!'))"
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-zinc-600 dark:bg-zinc-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-zinc-700 dark:hover:bg-zinc-600 focus:bg-zinc-700 dark:focus:bg-zinc-600 active:bg-zinc-800 dark:active:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                        </path>
                    </svg>
                    Share Article
                </button>

                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <h4 class="text-sm font-medium text-zinc-900 dark:text-white mb-2">Article Meta</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-zinc-500 dark:text-zinc-400">Last Updated</dt>
                            <dd class="text-sm text-zinc-900 dark:text-white">
                                {{ $article->updated_at->format('M j, Y \a\t g:i A') }}
                            </dd>
                        </div>
                        @if ($article->published_at)
                            <div>
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Time Since Published</dt>
                                <dd class="text-sm text-zinc-900 dark:text-white">
                                    {{ $article->published_at->diffForHumans() }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
