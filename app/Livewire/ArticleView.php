<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Article;
use League\CommonMark\CommonMarkConverter;
use Livewire\Component;

final class ArticleView extends Component
{
    public Article $article;

    public bool $modalMode = false;

    public function mount(Article $article, bool $modalMode = false): void
    {
        $this->article = $article->load(['newsOutlet', 'genres']);
        $this->modalMode = $modalMode;
    }

    public function getRenderedContentProperty(): string
    {
        $converter = new CommonMarkConverter();

        return $converter->convert($this->article->content ?? '')->getContent();
    }

    public function render()
    {
        $viewName = $this->modalMode ? 'livewire.article-view-modal' : 'livewire.article-view';

        return view($viewName)
            ->layout($this->modalMode ? null : 'components.layouts.public')
            ->title($this->article->title);
    }
}
