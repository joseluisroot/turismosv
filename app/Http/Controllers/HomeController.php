<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'categories' => Category::query()->withCount('places')->orderBy('name')->get(),
            'featuredPlaces' => Place::query()
                ->with(['category', 'department'])
                ->where('is_featured', true)
                ->orderByDesc('verification_score')
                ->take(6)
                ->get(),
            'stats' => [
                'places' => Place::query()->count(),
                'verified' => Place::query()->where('verification_status', 'verified')->count(),
                'departments' => Place::query()->distinct('department_id')->count('department_id'),
            ],
        ]);
    }
}
