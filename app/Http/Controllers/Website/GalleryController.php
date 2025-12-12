<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::where('is_published', true)
            ->withCount('photos')
            ->latest()
            ->paginate(12);

        return view('website.gallery.index', compact('albums'));
    }

    public function show(GalleryAlbum $album)
    {
        $album->load('photos');

        return view('website.gallery.show', compact('album'));
    }

    public function videos()
    {
        return view('website.gallery.videos');
    }
}
