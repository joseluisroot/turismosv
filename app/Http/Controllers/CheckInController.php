<?php
namespace App\Http\Controllers;
use App\Models\CheckIn;
use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class CheckInController extends Controller {
    public function store(Request $request, Place $place): RedirectResponse {
        abort_unless($place->publication_status==='published',404);
        $validated=$request->validate([
            'visited_on'=>['required','date','before_or_equal:today','after_or_equal:'.now()->subDays(30)->toDateString()],
            'note'=>['nullable','string','max:500'], 'verification_consent'=>['accepted'],
        ],['visited_on.after_or_equal'=>'En esta primera versión solo puedes registrar visitas de los últimos 30 días.','verification_consent.accepted'=>'Debes confirmar que la visita es real y aceptar su proceso de verificación.']);
        $checkIn=CheckIn::query()->where('user_id',$request->user()->id)->where('place_id',$place->id)->whereDate('visited_on',$validated['visited_on'])->first();
        if ($checkIn) {
            return redirect()->route('places.show',$place)->with('checkin_status','Ya habías registrado una visita para esa fecha.');
        }
        CheckIn::query()->create(['user_id'=>$request->user()->id,'place_id'=>$place->id,'visited_on'=>$validated['visited_on'],'status'=>'pending','evidence_method'=>'self_reported','note'=>$validated['note']??null,'verification_consent_at'=>now()]);
        $message='Registramos tu visita como pendiente. No sumará al pasaporte hasta completar una verificación.';
        return redirect()->route('places.show',$place)->with('checkin_status',$message);
    }
}
