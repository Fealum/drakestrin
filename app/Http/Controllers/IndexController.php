<?php

namespace App\Http\Controllers;

use App\Models\Board\Post;
use App\Models\Dictionary\Word;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        $postCount24h = Post::where('time', '>=', now()->subDay()->timestamp)->count();
        $postCount7d = Post::where('time', '>=', now()->subDays(7)->timestamp)->count();

        $word = Word::with(['translationKeysFrom.toWord.wordType'])
            ->where('language_id', 2)
            ->inRandomOrder()
            ->first();

        $news = Post::where('thread_id', 2108)
            ->with('elements.message')
            ->orderByDesc('time')
            ->first();

        return view('index', [
            'news' => $news,
            'postCount24h' => $postCount24h,
            'postCount7d' => $postCount7d,
            'word' => $word,
        ]);
    }
}
