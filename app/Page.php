<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    protected $fillable = [
        'chapter_id', 'filename', 'width', 'height', 'mime', 'hidden',
    ];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }
}
