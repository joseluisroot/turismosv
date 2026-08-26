<?php
namespace App\Http\Controllers;
use App\Models\SponsorshipCampaign;use Illuminate\Http\RedirectResponse;
class SponsorshipClickController extends Controller {public function __invoke(SponsorshipCampaign $campaign):RedirectResponse{abort_unless(SponsorshipCampaign::active()->whereKey($campaign)->exists(),404);$campaign->increment('clicks');return redirect()->away($campaign->destination_url);}}
