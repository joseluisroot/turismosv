<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class TaxonomyController extends Controller {
    public function category(Request $request): RedirectResponse { $data=$request->validate(['name'=>['required','string','max:100','unique:categories,name'],'icon'=>['nullable','string','max:16'],'description'=>['required','string','max:500']]);Category::create($data+['slug'=>$this->uniqueSlug(Category::class,$data['name'])]);return back()->with('catalog_status','Categoría creada.'); }
    public function department(Request $request): RedirectResponse { $data=$request->validate(['name'=>['required','string','max:100','unique:departments,name']]);Department::create($data+['slug'=>$this->uniqueSlug(Department::class,$data['name'])]);return back()->with('catalog_status','Departamento creado.'); }
    public function updateCategory(Request $request,Category $category): RedirectResponse { $data=$request->validate(['name'=>['required','string','max:100',Rule::unique('categories','name')->ignore($category)],'icon'=>['nullable','string','max:16'],'description'=>['required','string','max:500']]);$category->update($data+['slug'=>$this->uniqueSlug(Category::class,$data['name'],$category->id)]);return back()->with('catalog_status','Categoría actualizada.'); }
    public function updateDepartment(Request $request,Department $department): RedirectResponse { $data=$request->validate(['name'=>['required','string','max:100',Rule::unique('departments','name')->ignore($department)]]);$department->update($data+['slug'=>$this->uniqueSlug(Department::class,$data['name'],$department->id)]);return back()->with('catalog_status','Departamento actualizado.'); }
    private function uniqueSlug(string $model,string $name,?int $ignoreId=null): string { $base=Str::slug($name);$slug=$base;$number=2;while($model::where('slug',$slug)->when($ignoreId,fn($query)=>$query->whereKeyNot($ignoreId))->exists())$slug=$base.'-'.$number++;return $slug; }
}
