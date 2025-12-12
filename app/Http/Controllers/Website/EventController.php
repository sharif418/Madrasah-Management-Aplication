<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $upcoming = Event::where('is_public', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->get();

        $past = Event::where('is_public', true)
            ->where('start_date', '<', now())
            ->orderBy('start_date', 'desc')
            ->take(10)
            ->get();

        return view('website.events', compact('upcoming', 'past'));
    }
}
