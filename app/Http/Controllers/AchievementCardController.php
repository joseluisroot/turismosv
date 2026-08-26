<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class AchievementCardController extends Controller
{
    public function show(Achievement $achievement): View
    {
        $user = request()->user()->fresh();
        $earned = $user->achievements()->whereKey($achievement->id)->firstOrFail();

        return view('passport.achievement-card', [
            'user' => $user,
            'achievement' => $earned,
            'earnedAt' => Carbon::parse($earned->pivot->earned_at),
        ]);
    }
}
