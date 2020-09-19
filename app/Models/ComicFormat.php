<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicFormat extends Model {

    protected $casts = [
        'id' => 'integer',
    ];

    public function comics() {
        return $this->hasMany(Comic::class);
    }
}
