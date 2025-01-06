<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function mangas()
    {
        return $this->belongsToMany(Manga::class, 'mangas_genres', 'genre_id', 'manga_id');
    }
}
