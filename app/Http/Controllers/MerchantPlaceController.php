<?php
namespace App\Http\Controllers;
use App\Models\BusinessClaim;
use App\Models\Place;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class MerchantPlaceController extends Controller {
    public function index(): View { $claims=BusinessClaim::query()->whereBelongsTo(request()->user())->with('place')->latest()->get();return view('merchant.index',compact('claims')); }
    public function edit(Place $place): View { $this->authorizeManagement($place);return view('merchant.edit',compact('place')); }
    public function update(Request $request,Place $place): RedirectResponse {
        $this->authorizeManagement($place);$validated=$request->validate(['official_phone'=>['nullable','string','max:30'],'official_whatsapp'=>['nullable','string','max:30'],'official_website'=>['nullable','url:http,https','max:191'],'official_address'=>['nullable','string','max:191'],'official_opening_hours'=>['nullable','string','max:1000'],'official_price_reference'=>['nullable','string','max:120'],'official_description'=>['nullable','string','max:1500']]);$place->update($validated+['official_updated_at'=>now(),'official_updated_by'=>$request->user()->id]);return back()->with('merchant_status','La información oficial fue actualizada. Reseñas, estrellas y ranking no fueron modificados.');
    }
    private function authorizeManagement(Place $place): void { abort_unless(BusinessClaim::query()->whereBelongsTo(request()->user())->whereBelongsTo($place)->where('status','approved')->exists(),403); }
}
