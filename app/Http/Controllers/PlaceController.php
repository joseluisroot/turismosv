<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Models\CheckIn;
use App\Models\PlacePhoto;
use App\Models\BusinessClaim;
use Illuminate\Contracts\View\View;

class PlaceController extends Controller
{
    public function show(Place $place): View
    {
        abort_unless($place->publication_status==='published'||auth()->user()?->role==='admin',404);
        $place->load(['category', 'department','photos'=>fn($query)=>$query->with('user:id,name')->where('status','approved')->latest('moderated_at')->limit(12), 'reviews' => fn ($query) => $query->with('user:id,name')->where('status', 'published')->latest()->limit(20)]);

        $userReview = auth()->check()
            ? Review::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->first()
            : null;
        $userCheckIn = auth()->check()
            ? CheckIn::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->latest('visited_on')->first()
            : null;
        $userPhotos=auth()->check()?PlacePhoto::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->latest()->limit(5)->get():collect();
        $userClaim=auth()->check()?BusinessClaim::query()->whereBelongsTo(auth()->user())->whereBelongsTo($place)->first():null;

        $relatedPlaces = Place::query()
            ->with(['category', 'department'])
            ->whereKeyNot($place->id)
            ->where('publication_status','published')
            ->where(function ($query) use ($place) {
                $query->where('category_id', $place->category_id)
                    ->orWhere('department_id', $place->department_id);
            })
            ->orderByDesc('verification_score')
            ->take(3)
            ->get();

        return view('places.show', compact('place', 'relatedPlaces', 'userReview', 'userCheckIn','userPhotos','userClaim'));
    }
}
