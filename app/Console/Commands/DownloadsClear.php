<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comic;
use App\Models\Chapter;

class DownloadsClear extends Command {

    protected $signature = 'downloads:clear {--dry-run}';
    protected $description = 'Clear all dangling zips and pdfs which are not in the database';

    function __construct() {
        parent::__construct();
    }

    public function handle() {
        $comics = Comic::get();
        foreach ($comics as $comic) {
            $this->clearcomic($comic);
            $c = 0;
            foreach ($comic->chapters as $chapter) {
                $c += $this->clearChapter($comic, $chapter);
            }
            echo "Chapters: $c\n\n";
        }
    }

    private function clearComic($comic) {
        $dry_run = $this->option('dry-run');
        $path = Comic::path($comic);

        echo "$comic->name:\nCleared volumes: ";
        $exclusions = preg_replace('/^/', "$path/", $comic->volume_downloads->pluck('filename')->toArray());
        $deleted_zips = cleanDirectoryByExtension($path, 'zip', $exclusions, $dry_run);
        if ($dry_run && !empty($deleted_zips)) {
            echo "\n";
            foreach ($deleted_zips as $file) { echo "$file\n"; }
        }
        echo count($deleted_zips) . "\n";
    }

    private function clearChapter($comic, $chapter) {
        $dry_run = $this->option('dry-run');
        $path = Chapter::path($comic, $chapter);
        $to_print = "";

        $download = $chapter->download;
        $exclusions = $download ? ["$path/$download->filename"] : [];
        $deleted_zips = cleanDirectoryByExtension($path, 'zip', $exclusions, $dry_run);
        if ($dry_run && !empty($deleted_zips)) {
            $to_print .= "\n";
            foreach ($deleted_zips as $file) { $to_print .= "$file\n"; }
            $to_print .= "zip=" . count($deleted_zips);
        }

        $pdf = $chapter->pdf;
        $exclusions = $pdf ? ["$path/$pdf->filename"] : [];
        $deleted_pdfs = cleanDirectoryByExtension($path, 'pdf', $exclusions, $dry_run);
        if ($dry_run && !empty($deleted_pdfs)) {
            $to_print .= "\n";
            foreach ($deleted_pdfs as $file) { $to_print .= "$file\n"; }
            $to_print .= "pdf=" . count($deleted_pdfs);
        }

        if($to_print) {
            echo Chapter::slugLangVolChSub($chapter) . ": " . $to_print . "\n";
        }

        return count($deleted_zips) + count($deleted_pdfs);
    }

}
