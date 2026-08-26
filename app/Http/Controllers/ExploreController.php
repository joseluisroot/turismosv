<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Department;
use App\Models\Place;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ExploreController extends Controller {
    public function __invoke(Request $request): View {
        $filters=$request->validate(['q'=>['nullable','string','min:2','max:80'],'category'=>['nullable','string','exists:categories,slug'],'department'=>['nullable','string','exists:departments,slug'],'verification'=>['nullable',Rule::in(['registered','community_confirmed','verified'])],'min_rating'=>['nullable','numeric','between:1,5'],'sort'=>['nullable',Rule::in(['relevance','rating','popular','recent'])]]);
        $query=Place::query()->with(['category','department'])->where('publication_status','published');$term=trim($filters['q']??'');
        if($term!==''){$query->where(function($builder)use($term){$builder->where('name','like',"%{$term}%")->orWhere('municipality','like',"%{$term}%")->orWhereHas('department',fn($department)=>$department->where('name','like',"%{$term}%"));});}
        if(!empty($filters['category']))$query->whereHas('category',fn($category)=>$category->where('slug',$filters['category']));
        if(!empty($filters['department']))$query->whereHas('department',fn($department)=>$department->where('slug',$filters['department']));
        if(!empty($filters['verification']))$query->where('verification_status',$filters['verification']);
        if(isset($filters['min_rating']))$query->where('rating_average','>=',$filters['min_rating']);
        $sort=$filters['sort']??'relevance';
        match($sort){'rating'=>$query->orderByDesc('rating_average')->orderByDesc('reviews_count'),'popular'=>$query->orderByDesc('verified_visits_count')->orderByDesc('reviews_count'),'recent'=>$query->orderByDesc('published_at')->orderByDesc('id'),default=>$term!==''?$query->orderByRaw('CASE WHEN name = ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END',[$term,"{$term}%"])->orderByDesc('verification_score'):$query->orderByDesc('verification_score')->orderByDesc('rating_average')};
        return view('explore.index',['places'=>$query->paginate(12)->withQueryString(),'categories'=>Category::query()->whereHas('places',fn($place)=>$place->where('publication_status','published'))->orderBy('name')->get(),'departments'=>Department::query()->whereHas('places',fn($place)=>$place->where('publication_status','published'))->orderBy('name')->get(),'filters'=>$filters,'term'=>$term,'sort'=>$sort]);
    }
}
