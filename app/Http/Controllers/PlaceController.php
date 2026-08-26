<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Contracts\View\View;

class PlaceController extends Controller
{
    public function show(Place $place): View
    {
        $place->load(['category', 'department']);

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

        return view('places.show', compact('place', 'relatedPlaces'));
    }
}
