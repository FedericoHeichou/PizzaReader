<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComicFormat extends Model {
    public function comics() {
        return $this->hasMany(Comic::class);
    }
}
