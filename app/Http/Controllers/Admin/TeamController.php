<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller {
    public function index() {
        return view('admin.teams.index', ['teams' => Team::paginate(50)]);
    }

    public function create() {
        return view('admin.teams.edit');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => ['string', 'min:3', 'max:191', 'required'],
            'slug' => ['string', 'max:191'],
            'url' => ['string', 'max:191', 'url', 'required'],
        ]);
        $slug = Team::generateSlug($request);
        $team = Team::create([
            'name' => $request->name,
            'slug' => $slug,
            'url' => $request->url,
        ]);
        return redirect()->route('admin.teams.edit', $team->id)->with('success', 'Team created');
    }

    public function edit($team_id) {
        $team = Team::find($team_id);
        if (!$team) {
            abort(404);
        }
        return view('admin.teams.edit')->with(['team' => $team]);
    }

    public function update(Request $request, $team_id) {
        $team = Team::find($team_id);
        if (!$team) {
            abort(404);
        }
        $request->validate([
            'name' => ['string', 'min:3', 'max:191'],
            'slug' => ['string', 'max:191'],
            'url' => ['string', 'max:191'],
        ]);

        // If slug is not set but name is changed OR slug is set and is changed
        if ((!$request->slug && $request->name && $request->name !== $team->name) || ($request->slug && $request->slug !== $team->slug)) {
            $team->slug = Team::generateSlug($request);
        }
        if ($request->name) $team->name = $request->name;
        if ($request->url) $team->url = $request->url;
        $team->save();
        return back()->with('success', 'Team ' . $team->name . ' updated');
    }

    public function destroy($team_id) {
        Team::destroy($team_id);
        return redirect()->route('admin.teams.index')->with('success', 'Team deleted');
    }
}
