<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    protected $fillable = [
        'chapter_id', 'ip', 'rating'
    ];

    protected $casts = [
        'id' => 'integer',
        'chapter_id' => 'integer',
        'rating' => 'integer',
    ];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }



}
