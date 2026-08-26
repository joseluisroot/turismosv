<?php
namespace App\Http\Controllers;
use App\Models\Place;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReviewController extends Controller {
    public function store(Request $request, Place $place): RedirectResponse {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'], 'title' => ['required', 'string', 'min:4', 'max:120'],
            'body' => ['required', 'string', 'min:30', 'max:2000'], 'visited_at' => ['nullable', 'date', 'before_or_equal:today'], 'community_rules' => ['accepted'],
        ], ['body.min' => 'Cuéntanos un poco más: la reseña debe tener al menos 30 caracteres.', 'community_rules.accepted' => 'Debes confirmar que tu reseña refleja una experiencia honesta.']);
        unset($validated['community_rules']);
        DB::transaction(function () use ($request, $place, $validated) {
            Review::query()->updateOrCreate(['user_id' => $request->user()->id, 'place_id' => $place->id], [...$validated, 'status' => 'published']);
            $metrics = Review::query()->where('place_id', $place->id)->where('status', 'published')->selectRaw('COUNT(*) as total, AVG(rating) as average')->first();
            $place->update(['reviews_count' => $metrics->total, 'rating_average' => round((float) $metrics->average, 1)]);
        });
        return redirect()->route('places.show', $place)->with('review_status', 'Tu reseña fue publicada. Puedes actualizarla cuando quieras.');
    }
}
