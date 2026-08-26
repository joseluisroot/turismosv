<?php
namespace App\Http\Controllers;
use App\Models\Place;
use App\Models\PlacePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class PlacePhotoController extends Controller {
    public function store(Request $request,Place $place): RedirectResponse {
        if(PlacePhoto::query()->whereBelongsTo($request->user())->where('status','pending')->count()>=10)return back()->withErrors(['photo'=>'Tienes diez fotografías pendientes. Espera su revisión antes de enviar más.'])->withFragment('fotos');
        $validated=$request->validate(['photo'=>['required',File::image()->types(['jpg','jpeg','png','webp'])->max('5mb')->dimensions(Rule::dimensions()->minWidth(600)->minHeight(400)->maxWidth(8000)->maxHeight(8000))],'alt_text'=>['nullable','string','max:160'],'photo_rights'=>['accepted'],'photo_license'=>['accepted']],['photo_rights.accepted'=>'Debes confirmar que la fotografía es tuya o que tienes autorización para compartirla.','photo_license.accepted'=>'Debes aceptar la licencia de exhibición para enviar la fotografía.']);
        $file=$validated['photo'];$publicId=(string)Str::uuid();$extension=$file->extension();$path=$file->storeAs("community-photos/{$place->id}","{$publicId}.{$extension}",'local');
        if(!$path)return back()->withErrors(['photo'=>'No pudimos guardar el archivo. Intenta nuevamente.'])->withFragment('fotos');
        try{PlacePhoto::create(['public_id'=>$publicId,'place_id'=>$place->id,'user_id'=>$request->user()->id,'storage_path'=>$path,'original_name'=>Str::limit($file->getClientOriginalName(),191,''),'mime_type'=>$file->getMimeType(),'file_size'=>$file->getSize(),'alt_text'=>$validated['alt_text']??null,'status'=>'pending','license_version'=>'community-photo-2026-08','rights_confirmed_at'=>now()]);}catch(\Throwable $exception){Storage::disk('local')->delete($path);throw $exception;}
        return back()->with('photo_status','Recibimos tu fotografía. Permanecerá privada hasta completar la moderación.')->withFragment('fotos');
    }
    public function show(string $publicId): BinaryFileResponse {
        $photo=PlacePhoto::query()->where('public_id',$publicId)->where('status','approved')->firstOrFail();abort_unless(Storage::disk('local')->exists($photo->storage_path),404);
        return response()->file(Storage::disk('local')->path($photo->storage_path),['Content-Type'=>$photo->mime_type,'Cache-Control'=>'public, max-age=86400','X-Content-Type-Options'=>'nosniff']);
    }
}
