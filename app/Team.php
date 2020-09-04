<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model {
    protected $fillable = [
        'name', 'slug', 'url',
    ];

    // TODO controllare se becca sia team che team2
    public function chapters() {
        return $this->hasMany(Chapter::class);
    }

    public static function generateReaderArray($team) {
        if (!$team) return null;
        return [
            'name' => $team->name,
            'url' => $team->url,
        ];
    }
}
