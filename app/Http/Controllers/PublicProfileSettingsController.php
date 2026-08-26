<?php
namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class PublicProfileSettingsController extends Controller {
    public function update(Request $request): RedirectResponse {
        $enabled=$request->boolean('is_profile_public');
        $validated=$request->validate([
            'is_profile_public'=>['nullable','boolean'],
            'public_name_mode'=>['required',Rule::in(['alias','real'])],
            'public_alias'=>[Rule::requiredIf($enabled&&$request->input('public_name_mode')==='alias'),'nullable','string','min:3','max:40','regex:/^[\pL\pN ._-]+$/u'],
            'show_public_achievements'=>['nullable','boolean'],'show_public_stamps'=>['nullable','boolean'],
            'public_consent'=>[Rule::excludeIf(!$enabled),Rule::requiredIf($enabled),'accepted'],
        ],['public_alias.regex'=>'El alias solo puede contener letras, números, espacios, puntos, guiones y guiones bajos.','public_consent.required'=>'Debes confirmar que comprendes qué información será pública.']);
        $user=$request->user();$user->update(['public_profile_id'=>$user->public_profile_id??(string)Str::uuid(),'is_profile_public'=>$enabled,'public_name_mode'=>$validated['public_name_mode'],'public_alias'=>$validated['public_alias']??null,'show_public_achievements'=>$request->boolean('show_public_achievements'),'show_public_stamps'=>$request->boolean('show_public_stamps'),'public_profile_updated_at'=>now()]);
        return back()->with('status',$enabled?'Tu perfil público quedó activo con las preferencias seleccionadas.':'Tu perfil público fue desactivado.');
    }
}
