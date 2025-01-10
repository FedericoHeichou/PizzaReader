<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MangadexChapter extends Model
{
    protected $fillable = [
        'mangadex_id', 'chapter_id', 'title', 'chapter_number', 'volume_number', 'language', 'is_processed', 'mangadex_manga_id'
    ];
    public function mangadexManga()
    {
        $this->belongsTo(MangadexManga::class);
    }
    public function chapter()
    {
        $this->belongsTo(Chapter::class);
    }
}
