<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComicFormat;

class ComicFormatSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Schema::disableForeignKeyConstraints();
        ComicFormat::truncate();
        Schema::enableForeignKeyConstraints();

        $format = new ComicFormat();
        $format->name = 'Manga';
        $format->save();

        $format = new ComicFormat();
        $format->name = 'Long Strip (Web Toons)';
        $format->save();
    }
}
