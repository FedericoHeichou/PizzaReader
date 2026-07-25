<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ComicController;
use Illuminate\Console\Command;
use App\Models\Comic;

class ThumbnailResize extends Command {

    protected $signature = 'thumbnail:resize';
    protected $description = 'Generate small thumbnail for all comics';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        $comics = Comic::whereNotNull('thumbnail')->get();
        foreach ($comics as $comic) {
            $path = Comic::path($comic);
            ComicController::storeSmall(storage_path("app/$path/$comic->thumbnail"), $path, $comic->thumbnail);
        }
    }
}
