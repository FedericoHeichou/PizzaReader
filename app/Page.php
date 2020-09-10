<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    protected $fillable = [
        'chapter_id', 'filename', 'size', 'width', 'height', 'mime', 'hidden',
    ];

    public function scopePublic() {
        return $this->where('hidden', 0);
    }

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }

    public static function getUrl($comic, $chapter, $page) {
        return asset('storage/' . Chapter::buildPath($comic, $chapter) . '/' . urlencode($page->filename) . '?v=' . strtotime($page->updated_at));
    }

    public static function getUrlById($page_id) {
        $page = Page::find($page_id);
        $chapter = Chapter::find($page->chapter_id);
        $comic = Comic::find($chapter->comic_id);
        return self::getUrl($comic, $chapter, $page);
    }

    public static function getAllPagesForFileUpload($comic, $chapter) {
        $response = ["files" => []];
        foreach ($chapter->pages as $page) {
            $page->url = Page::getUrl($comic, $chapter, $page);
            array_push($response['files'], [
                'name' => $page->filename,
                'size' => $page->size,
                'url' => $page->url,
                'thumbnailUrl' => $page->url,
                'deleteUrl' => route('admin.comics.chapters.pages.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id, 'page' => $page->id]),
                'deleteType' => 'DELETE'
            ]);
        }
        return $response;
    }

    public static function getAllPagesForReader($comic, $chapter) {
        if (!$comic || !$chapter || $comic->id !== $chapter->comic_id) return null;
        $urls = [];
        foreach ($chapter->publicPages as $page) {
            array_push($urls, Page::getUrl($comic, $chapter, $page));
        }
        return $urls;
    }
}
