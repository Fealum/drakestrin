<?php

namespace App\Http\Controllers;

use App\Models\Dictionary\Word;
use App\Models\Post;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        $postCount24h = Post::where('time', '>=', now()->subDay()->timestamp)->count();
        $postCount7d = Post::where('time', '>=', now()->subDays(7)->timestamp)->count();

        $word = Word::with(['translationKeysFrom.toWord.wordType'])
            ->where('language', 2)
            ->inRandomOrder()
            ->first();

        $news = Post::where('thread', 2108)
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
