<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $recommendedPlaces=collect();$user=request()->user();
        if($user?->hasVerifiedEmail()&&$user->interests()->exists()){$categoryIds=$user->interests()->whereNotNull('category_id')->pluck('category_id')->unique()->values();$visited=$user->checkIns()->pluck('place_id');$query=Place::query()->with(['category','department'])->where('publication_status','published')->whereNotIn('id',$visited);if($categoryIds->isNotEmpty()){$query->whereIn('category_id',$categoryIds);}$recommendedPlaces=$query->orderByDesc('verification_score')->orderByDesc('rating_average')->take(3)->get();}
        return view('home', [
            'categories' => Category::query()->withCount(['places'=>fn($query)=>$query->where('publication_status','published')])->orderBy('name')->get(),
            'featuredPlaces' => Place::query()
                ->with(['category', 'department'])
                ->where('is_featured', true)
                ->where('publication_status','published')
                ->orderByDesc('verification_score')
                ->take(6)
                ->get(),
            'recommendedPlaces'=>$recommendedPlaces,
            'stats' => [
                'places' => Place::query()->where('publication_status','published')->count(),
                'verified' => Place::query()->where('publication_status','published')->where('verification_status', 'verified')->count(),
                'departments' => Place::query()->where('publication_status','published')->distinct('department_id')->count('department_id'),
            ],
        ]);
    }
}
