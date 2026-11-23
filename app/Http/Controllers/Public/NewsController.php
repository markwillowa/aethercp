<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = News::published()->pinnedFirst();

        if ($category && in_array($category, ['news', 'announcement', 'event', 'update', 'maintenance'])) {
            $query->where('category', $category);
        }

        $news = $query->paginate(10);

        return view('news.index', compact('news', 'category'));
    }

    public function show(string $slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();
        $recentNews = News::published()->where('id', '!=', $article->id)->limit(4)->get();

        return view('news.show', compact('article', 'recentNews'));
    }
}

