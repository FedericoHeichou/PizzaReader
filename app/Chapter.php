<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model {
    protected $fillable = [
        'comic_id', 'team_id', 'team2_id', 'volume', 'chapter', 'subchapter', 'title', 'stub', 'salt', 'hidden', 'views',
    ];

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function comic() {
        return $this->belongsTo(Comic::class);
    }

    public function teams() {
        return $this->belongsTo(Team::class);
    }
}
