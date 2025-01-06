<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'anilist_id',
        'role',
        'image'
    ];
    public $timestamps = false;

    public function mangas()
    {
        return $this->belongsToMany(Manga::class, 'mangas_characters', 'character_id', 'manga_id');
    }
}
