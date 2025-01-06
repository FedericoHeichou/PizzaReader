<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangaCharacter extends Model
{
    use HasFactory;
    protected $table = 'mangas_characters';
    protected $fillable = ['manga_id', 'character_id'];
}
