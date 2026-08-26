<?php
namespace App\Http\Controllers;
use App\Models\BusinessClaim;
use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
class BusinessClaimController extends Controller {
    public function store(Request $request,Place $place): RedirectResponse {
        abort_unless($place->publication_status==='published',404);
        $validated=$request->validate(['relationship_role'=>['required','string','max:80'],'business_email'=>['required','email:rfc','max:191'],'business_phone'=>['required','string','max:30'],'evidence_note'=>['required','string','min:30','max:500'],'verification_document'=>['required',File::types(['pdf','jpg','jpeg','png'])->max('5mb')],'business_declaration'=>['accepted']],['business_declaration.accepted'=>'Debes confirmar tu relación con el comercio y la autenticidad de la evidencia.']);
        $existing=BusinessClaim::query()->whereBelongsTo($request->user())->whereBelongsTo($place)->first();if($existing?->status==='approved')return back()->withErrors(['claim'=>'Ya administras esta ficha.']);if($existing?->status==='pending')return back()->withErrors(['claim'=>'Ya tienes una solicitud pendiente para este lugar.']);
        $file=$validated['verification_document'];$publicId=(string)Str::uuid();$path=$file->storeAs("business-claims/{$place->id}","{$publicId}.{$file->extension()}",'local');if(!$path)return back()->withErrors(['verification_document'=>'No pudimos guardar la evidencia.']);
        if($existing&&$existing->document_path)Storage::disk('local')->delete($existing->document_path);
        BusinessClaim::query()->updateOrCreate(['place_id'=>$place->id,'user_id'=>$request->user()->id],['public_id'=>$publicId,'relationship_role'=>$validated['relationship_role'],'business_email'=>Str::lower($validated['business_email']),'business_phone'=>$validated['business_phone'],'evidence_note'=>$validated['evidence_note'],'document_path'=>$path,'document_name'=>Str::limit($file->getClientOriginalName(),191,''),'document_mime'=>$file->getMimeType(),'status'=>'pending','declaration_accepted_at'=>now(),'reviewed_by'=>null,'reviewed_at'=>null,'review_note'=>null]);
        return back()->with('claim_status','Recibimos tu solicitud. La evidencia es privada y será revisada por TurismoSV.')->withFragment('reclamar');
    }
}
