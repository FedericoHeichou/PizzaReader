<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model {
    protected $fillable = [
        'name', 'slug', 'url',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    // TODO controllare se becca sia team che team2
    public function chapters() {
        return $this->hasMany(Chapter::class);
    }

    public static function slug($slug) {
        return Team::where('slug', $slug)->first();
    }

    public static function generateReaderArray($team) {
        if (!$team) return null;
        return [
            'name' => $team->name,
            'url' => $team->url,
        ];
    }

    public static function generateSlug($request) {
        $fields['name'] = $request->name;
        $fields['slug'] = $request->slug;
        return generateSlug(new Team, $fields);
    }

    public static function getTeamById($team_id) {
        if ($team_id === null) return null;
        $k = "teams.$team_id";
        $team = config($k);
        if (!$team) {
            $team = Team::find($team_id);
            config([$k => $team]);
        }
        return $team;
    }
}
