<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\Place;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class PlaceManagementController extends Controller {
    public function index(): View { return view('admin.places.index',['places'=>Place::with(['category','department'])->latest()->paginate(25),'categories'=>Category::withCount('places')->orderBy('name')->get(),'departments'=>Department::withCount('places')->orderBy('name')->get()]); }
    public function create(): View { return $this->form(new Place(['publication_status'=>'draft','verification_status'=>'registered','verification_score'=>0])); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request);$data['slug']=$this->uniqueSlug($data['name']);$data['created_by']=$request->user()->id;$data['editorial_updated_by']=$request->user()->id;$data['published_at']=$data['publication_status']==='published'?now():null;$place=Place::create($data);return redirect()->route('admin.places.edit',$place)->with('catalog_status','Lugar creado como '.$this->statusName($place->publication_status).'.'); }
    public function edit(Place $place): View { return $this->form($place); }
    public function update(Request $request,Place $place): RedirectResponse { $data=$this->validated($request,$place);$data['slug']=$this->uniqueSlug($data['name'],$place);$data['editorial_updated_by']=$request->user()->id;if($data['publication_status']==='published'&&!$place->published_at)$data['published_at']=now();$place->update($data);return back()->with('catalog_status','Ficha editorial actualizada.'); }
    private function form(Place $place): View { return view('admin.places.form',['place'=>$place,'categories'=>Category::orderBy('name')->get(),'departments'=>Department::orderBy('name')->get()]); }
    private function validated(Request $request,?Place $place=null): array { $published=$request->input('publication_status')==='published';$data=$request->validate(['name'=>['required','string','max:191'],'category_id'=>['required','exists:categories,id'],'department_id'=>['required','exists:departments,id'],'municipality'=>['required','string','max:191'],'summary'=>['required','string','min:40','max:2000'],'publication_status'=>['required',Rule::in(['draft','published','archived'])],'verification_status'=>['required',Rule::in(['registered','community_confirmed','verified'])],'verification_score'=>['required','integer','min:0','max:100'],'is_featured'=>['nullable','boolean'],'source_name'=>[Rule::requiredIf($published),'nullable','string','max:191'],'source_url'=>['nullable','url:http,https','max:191'],'source_verified_at'=>[Rule::requiredIf($published),'nullable','date','before_or_equal:today'],'editorial_notes'=>['nullable','string','max:3000']]);$data['is_featured']=$request->boolean('is_featured');return $data; }
    private function uniqueSlug(string $name,?Place $ignore=null): string { $base=Str::slug($name)?:'lugar';$slug=$base;$number=2;while(Place::where('slug',$slug)->when($ignore,fn($query)=>$query->whereKeyNot($ignore->id))->exists())$slug=$base.'-'.$number++;return $slug; }
    private function statusName(string $status): string { return ['draft'=>'borrador','published'=>'publicado','archived'=>'archivado'][$status]; }
}
