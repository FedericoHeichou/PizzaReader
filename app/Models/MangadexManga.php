<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangadexManga extends Model
{
    /** @use HasFactory<\Database\Factories\MangadexMangaFactory> */
    use HasFactory;

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
}
