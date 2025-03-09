<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\PageController;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ChaptersImport extends Command {

    protected $signature = 'chapters:import
                            {pages_directory : Target directory containing the pages which will be imported}
                            {comic_slug_or_id : Comic slug or ID}
                            {--title= : Chapter title}
                            {--volume= : Chapter volume}
                            {--chapter= : Chapter number}
                            {--subchapter= : Chapter subchapter}
                            {--hidden= : Chapter visibility. Remember to set to false if you want to publish it immediately [default: configured default_hidden_comic value]}
                            {--licensed=false : Specify if the chapter is licensed}
                            {--published_on= : Chapter publication date in the format YYYY-MM-DDTHH:MM. If not set, the current date will be used}
                            {--publish_start= : Chapter publication start date in the format YYYY-MM-DDTHH:MM. If not set, published_on will be used}
                            {--publish_end= : Chapter publication start date in the format YYYY-MM-DDTHH:MM. If not set, the publication will never end}
                            {--timezone= : Chapter timezone. If not specified, the current PHP timezone will be used. Example: Europe/Rome}
                            {--language= : Chapter language. If not specified, the reader default language will be used}
                            {--team= : Team slug or ID}
                            {--team2= : Team2 slug or ID}
                            {--views=0 : Chapter views}
                        ';
    protected $description = 'Import a chapter with all its pages for a specific comic';

    function __construct() {
        parent::__construct();
    }

    private function log_verbose($message) {
        if ($this->option('verbose')) {
            $this->info($message);
        }
    }

    public function handle() {
        $pages_directory = $this->argument('pages_directory');
        $comic_slug_or_id = $this->argument('comic_slug_or_id');
        $title = $this->option('title');
        $volume = $this->option('volume');
        $chapter = $this->option('chapter');
        $subchapter = $this->option('subchapter');
        $hidden = $this->option('hidden');
        $licensed = $this->option('licensed');
        $published_on = $this->option('published_on');
        $publish_start = $this->option('publish_start');
        $publish_end = $this->option('publish_end');
        $timezone = $this->option('timezone');
        $language = $this->option('language');
        $team = $this->option('team');
        $team2 = $this->option('team2');

        // Validate the pages directory
        try {
            $files = File::files($pages_directory);
            if (empty($files)) {
                $this->error('No files found in the directory');
                return 1;
            }
        } catch (\Symfony\Component\Finder\Exception\DirectoryNotFoundException $e) {
            $this->error($e->getMessage());
            return 1;
        }
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $found_broken_files = false;
        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (!in_array($extension, $allowed_extensions)) {
                $this->error('Invalid file extension: ' . $extension . ' in ' . $file);
                $found_broken_files = true;
            }
        }
        if ($found_broken_files) {
            return 1;
        }

        // Validate the comic
        if (is_numeric($comic_slug_or_id)) {
            $comic = Comic::find($comic_slug_or_id);
        } else {
            $comic = Comic::slug($comic_slug_or_id);
        }
        if (!$comic) {
            $this->error('Comic not found');
            return 1;
        }

        // Validate the team1
        if ($team) {
            if (is_numeric($team)) {
                $team = Team::find($team);
            } else {
                $team = Team::slug($team);
            }
            if (!$team) {
                $this->error('Team not found');
                return 1;
            }
        } else {
            $this->error('Team is required');
            return 1;
        }

        // Validate the team2
        if ($team2) {
            if (is_numeric($team2)) {
                $team2 = Team::find($team2);
            } else {
                $team2 = Team::slug($team2);
            }
            if (!$team2) {
                $this->error('Team2 not found');
                return 1;
            }
        }

        // Do some conversions
        if ($hidden) {
            $hidden = filter_var($hidden, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($hidden === null) {
                $this->error('Invalid hidden value');
                return 1;
            }
        } else {
            $hidden = config('settings.default_hidden_comic');
        }
        if ($licensed) {
            $licensed = filter_var($licensed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($licensed === null) {
                $this->error('Invalid licensed value');
                return 1;
            }
        } else {
            $licensed = false;
        }

        // Set some default values
        if (!$timezone) {
            // Get current PHP timezone
            $timezone = date_default_timezone_get();
            $this->log_verbose('Timezone not set. Using default timezone: ' . $timezone);
        }
        if (!$published_on) {
            $published_on = now($timezone)->format('Y-m-d\TH:i');
            $this->log_verbose('Published on not set. Using current date: ' . $published_on);
        }
        if (!$publish_start) {
            $publish_start = $published_on;
            $this->log_verbose('Publish start not set. Using published on date: ' . $publish_start);
        }
        if (!$language) {
            $language = config('settings.default_language');
            $this->log_verbose('Language not set. Using comic default language: ' . $language);
        }

        // Before creating the chapter, we need to validate the fields.
        // We will create a fake Request object to use the same validation rules as the controller.
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'title' => $title,
            'volume' => $volume,
            'chapter' => $chapter,
            'subchapter' => $subchapter,
            'hidden' => $hidden ? '1' : '0',
            'licensed' => $licensed ? '1' : '0',
            'published_on' => $published_on,
            'publish_start' => $publish_start,
            'publish_end' => $publish_end,
            'timezone' => $timezone,
            'language' => $language,
            'team_id' => $team->id,
            'team2_id' => $team2 ? $team2->id : null,
        ]);

        // Call the ChapterController store to ensure the fields are valid
        $controller = new ChapterController();
        try {
            $response = $controller->store($request, $comic->slug);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        $session = $response->getSession();
        $error = $session->get('error');
        if ($error) {
            $this->error($error);
            return 1;
        }

        if (!$session->get('success')) {
            $this->error('Chapter not created, error not specified');
            return 1;
        }

        $chapter_id = $session->get('chapter_id');
        $this->log_verbose('Created chapter with id: ' . $chapter_id);

        try{
            // Now we can create the pages
            // We have to provider "file" type object to the controller
            // We can't just pass the file name list
            $request = new \Illuminate\Http\Request();
            $files_array = array_map(function ($file) {
                $path = $file->getPathname();
                $filename = $file->getFilename();
                return new \Illuminate\Http\UploadedFile($path, $filename, mime_content_type($path), null, true);
            }, $files);
            $request->replace([
                'files' => $files_array,
            ]);
            $request->files = new \Symfony\Component\HttpFoundation\FileBag();
            $request->files->add(['files' => $files_array]);
        
            $controller = new PageController();
            $response = $controller->store($request, $comic->slug, $chapter_id);
            if ($response->getStatusCode() !== 200) {
                $this->error('Error importing pages, status code: ' . $response->getStatusCode());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $chapter = Chapter::find($chapter_id);
            if ($chapter) {
                $chapter->delete();
            }
            $this->info('Chapter deleted because of the error');
            throw $e; // FIXME remove this
            return 1;
        }

        $this->info('Chapter imported successfully: ' . rtrim(config('app.url'), '/') . Chapter::getUrl($comic, Chapter::find($chapter_id)));
        return 0;
    }

}
