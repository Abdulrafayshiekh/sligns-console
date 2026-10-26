<?php

namespace App\Http\Controllers\Company;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CoachRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(Request $request): View
    {
        $coaches = $request->user()->company->coaches()->withCount('clients')->orderBy('name')->get();
        $editing = $request->filled('coach') ? $coaches->firstWhere('id', (int) $request->query('coach')) : null;

        return view('company.coaches', [
            'coaches' => $coaches,
            'editing' => $editing,
            'adding' => ! $editing && ($request->boolean('add') || old('panel') === 'add'),
        ]);
    }

    public function store(CoachRequest $request): RedirectResponse
    {
        $coach = $request->user()->company->users()->create($request->validated() + ['role' => Role::Coach]);

        return redirect()->route('company.coaches.index', ['coach' => $coach->id])->with('status', __('Coach added.'));
    }

    public function update(CoachRequest $request, User $coach): RedirectResponse
    {
        Gate::authorize('manage-coach', $coach);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $coach->update($data);

        return redirect()->route('company.coaches.index', ['coach' => $coach->id])->with('status', __('Coach saved.'));
    }

    public function destroy(User $coach): RedirectResponse
    {
        Gate::authorize('manage-coach', $coach);

        if ($coach->clients()->exists()) {
            return redirect()->route('company.reassign.show', $coach)->withErrors([
                'coach' => __('Move this coach’s clients to another coach before removing the coach.'),
            ]);
        }

        $coach->delete();

        return redirect()->route('company.coaches.index')->with('status', __('Coach removed.'));
    }
}
