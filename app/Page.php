<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    protected $fillable = [
        'chapter_id', 'filename', 'size', 'width', 'height', 'mime', 'hidden',
    ];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }

    public static function getUrl($comic, $chapter, $page) {
        return asset('storage/' . Chapter::buildPath($comic, $chapter) . '/' . $page->filename . '?v=' . strtotime($page->updated_at));
    }

    public static function getUrlById($page_id) {
        $page = Page::find($page_id);
        $chapter = Chapter::find($page->chapter_id);
        $comic = Comic::find($chapter->comic_id);
        return self::getUrl($comic, $chapter, $page);
    }
}
