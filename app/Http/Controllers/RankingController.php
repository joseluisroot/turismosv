<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Place;
use App\Services\RankPlaces;
use Illuminate\Contracts\View\View;
class RankingController extends Controller {
    public function __invoke(RankPlaces $ranker,?Category $category=null): View {
        $query=Place::query()->with(['category','department'])->where('publication_status','published');if($category)$query->whereBelongsTo($category);$places=$ranker->handle($query->get())->take(20);
        return view('rankings.index',['places'=>$places,'selectedCategory'=>$category,'categories'=>Category::query()->whereHas('places',fn($place)=>$place->where('publication_status','published'))->orderBy('name')->get(),'updatedAt'=>Place::where('publication_status','published')->max('updated_at')]);
    }
}
