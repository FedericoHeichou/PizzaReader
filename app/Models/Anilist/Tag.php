<?php

namespace App\Models\Anilist;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'anilistId', 'userId'];

    public function mangas()
    {
        return $this->belongsToMany(Manga::class);
    }
}
