<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Place;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FounderCatalogController extends Controller
{
    public function index(): View
    {
        $query = Place::query()->where('is_founder_candidate', true);
        $places = (clone $query)->with(['category', 'department'])->orderBy('founder_priority')->orderBy('name')->get();

        return view('admin.founder-catalog.index', [
            'places' => $places,
            'departments' => Department::query()->withCount(['places as founder_places_count' => fn ($place) => $place->where('is_founder_candidate', true)])->orderBy('name')->get(),
            'metrics' => [
                'total' => (clone $query)->count(),
                'published' => (clone $query)->where('publication_status', 'published')->count(),
                'ready' => (clone $query)->where('founder_stage', 'ready')->count(),
                'without_source' => (clone $query)->whereNull('source_verified_at')->count(),
                'without_contact' => (clone $query)->whereIn('founder_contact_status', ['not_started', 'identified'])->count(),
                'without_photos' => (clone $query)->where('founder_photo_status', 'missing')->count(),
            ],
        ]);
    }

    public function update(Request $request, Place $place): RedirectResponse
    {
        abort_unless($place->is_founder_candidate, 404);
        $data = $request->validate([
            'founder_priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'founder_stage' => ['required', Rule::in(['candidate', 'researching', 'contacting', 'ready', 'published'])],
            'founder_contact_status' => ['required', Rule::in(['not_started', 'identified', 'contacted', 'responded', 'declined'])],
            'founder_photo_status' => ['required', Rule::in(['missing', 'requested', 'authorized', 'community'])],
            'founder_contact_name' => ['nullable', 'string', 'max:191'],
            'founder_contact_email' => ['nullable', 'email', 'max:191'],
            'founder_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if ($data['founder_stage'] === 'published' && $place->publication_status !== 'published') {
            return back()->withErrors(['founder_stage' => 'Primero valida la fuente y publica la ficha desde el editor del catálogo.']);
        }

        $data['founder_assigned_to'] = $request->user()->id;
        if ($data['founder_contact_status'] === 'contacted' && ! $place->founder_contacted_at) {
            $data['founder_contacted_at'] = now();
        }
        $place->update($data);

        return back()->with('founder_status', "Seguimiento actualizado para {$place->name}.");
    }
}
