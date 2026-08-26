<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Models\CheckIn;
use Illuminate\Contracts\View\View;

class PlaceController extends Controller
{
    public function show(Place $place): View
    {
        $place->load(['category', 'department', 'reviews' => fn ($query) => $query->with('user:id,name')->where('status', 'published')->latest()->limit(20)]);

        $userReview = auth()->check()
            ? Review::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->first()
            : null;
        $userCheckIn = auth()->check()
            ? CheckIn::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->latest('visited_on')->first()
            : null;

        $relatedPlaces = Place::query()
            ->with(['category', 'department'])
            ->whereKeyNot($place->id)
            ->where(function ($query) use ($place) {
                $query->where('category_id', $place->category_id)
                    ->orWhere('department_id', $place->department_id);
            })
            ->orderByDesc('verification_score')
            ->take(3)
            ->get();

        return view('places.show', compact('place', 'relatedPlaces', 'userReview', 'userCheckIn'));
    }
}
