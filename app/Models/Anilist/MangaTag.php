<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangaTag extends Model
{
    use HasFactory;
    protected $table = 'mangas_tags';
    public $timestamps = false;
    protected $fillable = ['manga_id', 'tag_id'];

    public function mangas()
    {
        $this->belongsToMany(Manga::class, 'mangas_tags', 'manga_id', 'tag_id');
    }
}
