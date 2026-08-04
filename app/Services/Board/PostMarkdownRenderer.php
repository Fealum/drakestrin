<?php

namespace App\Services\Board;

use App\Models\Board\Post;

class PostMarkdownRenderer
{
    public function render(Post $post): string
    {
        $markdown = $post->elements->map(function ($element) {
            if ($element->message) {
                return (string) $element->message->message;
            }
            if ($element->transfer) {
                return '[Handlung: '.$element->transfer->items
                    ->map(fn ($item) => $item->item?->name.' ('.$item->stack.')')
                    ->filter()->implode(', ').']';
            }
            if ($element->sceneTransition?->startedScene) {
                $prefix = $element->sceneTransition->endedScene ? 'Szenenwechsel' : 'Szene';

                return '['.$prefix.': '.$element->sceneTransition->startedScene->location?->name.']';
            }
            if ($element->sceneTransition?->endedScene) {
                return '[Szene beendet: '.$element->sceneTransition->endedScene->location?->name.']';
            }
            if ($element->poll) {
                return '[Umfrage: '.$element->poll->question."]\n".$element->poll->options
                    ->map(fn ($option) => '- '.$option->label.' ('.$option->voteCount().' Stimmen)')
                    ->implode("\n");
            }

            return '';
        })->filter()->implode("\n\n");

        return $markdown !== '' ? $markdown : (string) $post->message;
    }
}
