<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('website.news.index', compact('news'));
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(4)
            ->get();

        return view('website.news.show', compact('news', 'related'));
    }
}
