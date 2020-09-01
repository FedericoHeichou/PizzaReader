<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComicFormat extends Model {
    public function comics() {
        return $this->hasMany(Comic::class);
    }
}
