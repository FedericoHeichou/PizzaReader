<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comic extends Model {
    protected $fillable = [
        'name', 'stub', 'salt', 'hidden', 'author', 'artist', 'target', 'status', 'description', 'thumbnail',
        'custom_chapter', 'comic_format_id', 'adult', 'download_link',
    ];

    public function format() {
        return $this->belongsTo(ComicFormat::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }

    public static function stub($stub) {
        return Comic::where('stub', $stub)->first();
    }
}
