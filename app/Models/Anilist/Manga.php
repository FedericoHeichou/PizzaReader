<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    use HasFactory;
    protected $fillable = [
        'anilist_id',
        'idMal',
        'name',
        'titles',
        'synonyms',
        'is_adult',
        'status',
        'chapters',
        'volumes',
        'description',
        'score',
        'source',
        'cover_image',
        'started_at',
        'ended_at',
        'format'
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'mangas_genres', 'manga_id', 'genre_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'mangas_tags', 'manga_id', 'tag_id');
    }
    public function characters()
    {
        return $this->belongsToMany(Character::class, 'mangas_characters', 'manga_id', 'character_id');
    }
}
