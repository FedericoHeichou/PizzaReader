<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangaGenre extends Model
{
    use HasFactory;
    protected $table = 'mangas_genres';
    protected $fillable = ['manga_id', 'genre_id'];
    public $timestamps = false;

//    public function manga()
//    {
//        return $this->belongsTo(Manga::class, 'manga_id');
//    }
//    public function genre()
//    {
//        return $this->belongsTo(Genre::class, 'genre_id');
//    }
}
