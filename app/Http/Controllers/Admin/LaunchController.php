<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessClaim;
use App\Models\CheckIn;
use App\Models\ContentReport;
use App\Models\LaunchChecklistItem;
use App\Models\Place;
use App\Models\PlacePhoto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaunchController extends Controller
{
    public function index(): View
    {
        $items = LaunchChecklistItem::with('completedBy')->orderByDesc('is_required')->orderBy('area')->get();
        $required = $items->where('is_required', true);
        $pendingModeration = PlacePhoto::where('status', 'pending')->count()
            + CheckIn::where('status', 'pending')->count()
            + ContentReport::where('status', 'pending')->count()
            + BusinessClaim::where('status', 'pending')->count();

        $automated = collect([
            ['label' => 'Entorno de producción y HTTPS', 'passed' => app()->environment('production') && str_starts_with((string) config('app.url'), 'https://')],
            ['label' => 'Identidad legal completa', 'passed' => collect(['owner_name', 'tax_id', 'contact_email', 'address'])->every(fn (string $field) => filled(config("legal.{$field}")))],
            ['label' => 'Correo SMTP habilitado', 'passed' => config('mail.default') !== 'log'],
            ['label' => 'Catálogo público mínimo', 'passed' => Place::where('publication_status', 'published')->count() >= config('launch.minimum_published_places')],
            ['label' => 'Bandejas de moderación despejadas', 'passed' => $pendingModeration === 0],
        ]);

        return view('admin.launch', [
            'items' => $items,
            'automated' => $automated,
            'requiredCompleted' => $required->where('is_completed', true)->count(),
            'requiredTotal' => $required->count(),
            'ready' => $required->isNotEmpty() && $required->every->is_completed && $automated->every('passed'),
        ]);
    }

    public function update(Request $request, LaunchChecklistItem $item): RedirectResponse
    {
        $data = $request->validate([
            'is_completed' => ['nullable', 'boolean'],
            'evidence_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $completed = $request->boolean('is_completed');

        $item->update([
            'is_completed' => $completed,
            'evidence_notes' => $data['evidence_notes'] ?? null,
            'completed_by' => $completed ? $request->user()->id : null,
            'completed_at' => $completed ? now() : null,
        ]);

        return back()->with('launch_status', 'Control de lanzamiento actualizado.');
    }
}
