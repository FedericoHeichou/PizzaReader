<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;

class PagesCheck extends Command {

    protected $signature = 'pages:check {--csv}';
    protected $description = 'Lists all missing pages in the filesystem but that are in the database';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        $csv = $this->option('csv');
        $abs_path = storage_path('app/');
        $comics = Comic::get();
        foreach ($comics as &$comic) {
            foreach ($comic->chapters as &$chapter) {
                foreach ($chapter->pages as &$page) {
                    $page_path = 'public/' . Page::getPath($comic, $chapter, $page, $encode=false);
                    if (!Storage::exists($page_path)) {
                        if ($csv) {
                            echo $comic->name . "\t" . Chapter::getVolChSub($chapter) . "\t" . $page->filename . "\t" . Page::getUrl($comic, $chapter, $page) . "\t";
                        }
                        echo $abs_path . $page_path . "\n";
                    }
                }
            }
        }
    }

}
