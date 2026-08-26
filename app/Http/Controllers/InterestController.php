<?php
namespace App\Http\Controllers;
use App\Models\Interest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class InterestController extends Controller {
    public function edit(): View { $user=request()->user()->load('interests');return view('profile.interests',['user'=>$user,'interests'=>Interest::query()->where('is_active',true)->orderBy('sort_order')->get()]); }
    public function update(Request $request): RedirectResponse {
        $activeIds=Interest::query()->where('is_active',true)->pluck('id');$validated=$request->validate(['interests'=>['nullable','array','max:6'],'interests.*'=>['integer',Rule::in($activeIds)]]);
        $request->user()->interests()->sync($validated['interests']??[]);$request->user()->update(['interests_selected_at'=>now()]);
        return redirect()->route('home')->with('status',empty($validated['interests'])?'Puedes elegir tus intereses más adelante desde tu perfil.':'Usaremos tus intereses para recomendarte lugares.');
    }
}
