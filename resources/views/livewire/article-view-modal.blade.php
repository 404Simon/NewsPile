<div class="p-4 space-y-4">
    <!-- Article Meta -->
    <div class="flex flex-wrap gap-2 mb-4">
        @if ($article->newsOutlet)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                {{ $article->newsOutlet->name }}
            </span>
        @endif

        @if ($article->genres->isNotEmpty())
            @foreach ($article->genres as $genre)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                    {{ $genre->name }}
                </span>
            @endforeach
        @endif

        @if ($article->published_at)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                {{ $article->published_at->format('M j, Y') }}
            </span>
        @endif
    </div>

    <!-- Description -->
    @if ($article->description)
        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-xl p-4 mb-4">
            <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                {{ $article->description }}
            </p>
        </div>
    @endif

    <!-- Content -->
    @if ($article->content)
        <div class="prose prose-sm prose-zinc dark:prose-invert max-w-none">
            {!! $this->renderedContent !!}
        </div>
    @endif
</div>
