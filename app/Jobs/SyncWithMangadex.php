<?php

namespace App\Jobs;

use App\Mangadex\Api\Manga as MangadexApi;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\MangadexChapter;
use App\Models\MangadexManga;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncWithMangadex implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private MangadexApi $mangadexApi)
    {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->saveList();
    }

    private function saveList()
    {
        $mangaList = [];
        $authData = $this->mangadexApi->auth();
        $limit = 10;
        $offset = 0;
        for (;;) {
            $data = $this->mangadexApi->getList($authData['access_token'], $limit, $offset);
            $mangaList = array_merge($mangaList, $data['data']);
            $offset += $limit;
            sleep(1);
            $mangacount = count($mangaList);
            echo "{$mangacount}/{$data['total']} titles presaved".PHP_EOL;
            if (count($mangaList) >= $data['total']) {
                break;
            }
        }
        echo 'Manga list received. Starting saving manga and its chapters'. PHP_EOL;
        $this->saveMangaAndChapters($data['data']);
    }
    private function saveMangaAndChapters(array $data)
    {
        foreach ($data as $item) {
            $manga = MangadexManga::where('mangadex_id', $item['id'])->first();
            if (!$manga) {
                $comic = $this->createComic($item);
                $manga = new MangadexManga();
                $manga->mangadex_id = $item['id'];
                $manga->comic_id = $comic->id;
                $manga->save();
                $manga->refresh();
            }
            $this->saveChapters($manga);
        }
    }

    private function createComic(array $item): Comic
    {
        $genres = [];
        foreach ($item['attributes']['tags'] as $tag) {
            $names = $tag['attributes']['name'];
            $name = isset($names['en']) ? $names['en'] : array_values($names)[0];
            $genres[] = $name;
        }
        $title = isset($item['attributes']['title']['en'])
            ? $item['attributes']['title']['en'] : array_values($item['attributes']['title'])[0];
        $description = isset($item['attributes']['description']['en'])
            ? $item['attributes']['description']['en'] : '';
        $fields = [
            'name' => $title,
            'description' => $description,
            'hidden' => false,
            'author' => '',
            'genres' => implode(',', $genres),
            'order_index' => 0,
            'comic_format_id' => 1,
            'cover_image' => $this->mangadexApi->getMangaCover($item['id'], $this->getCoverArtId($item['relationships'])),
            'thumbnail' => 'thumbnail.png',
        ];
        $fields['salt'] = Str::random();
        $fields['slug'] = Comic::generateSlug($fields);

        echo 'Saving manga '.$title.PHP_EOL;

        $comic = Comic::create($fields);
        $path = Comic::path($comic);
        Storage::makeDirectory($path);
        Storage::setVisibility($path, 'public');
        $this->storeAs($path, $comic->thumbnail, $fields['cover_image']);
        $comic->refresh();
        return $comic;
    }
    private function saveChapters(MangadexManga $manga)
    {
        echo "Saving {$manga->comic->title} chapters".PHP_EOL;
        $limit = 50;
        $offset = 0;
        $chapters = [];
        for(;;) {
            try{
                $response = $this->mangadexApi->getMangaChapters($manga->mangadex_id, $limit, $offset);
            } catch (\Exception $exception) {
//                continue;
                echo "Skipping. An error occurred: ".$exception->getMessage().PHP_EOL;
                break;
            }
            if($response->total <= $offset) {
                break;
            }
            foreach ($response->data as $mangaChapter) {
                if ($mangaChapter->attributes->translatedLanguage === 'en') {
                    $chapters[] = $mangaChapter;
                }
            }
            $offset += $limit;
            sleep(1);
        }
        foreach ($chapters as $chapter) {
            $this->saveSingleChapter($manga->comic, $chapter);
        }
    }

    private function saveSingleChapter(MangadexManga $manga, object $chapter)
    {
        $comic = $manga->comic;
        echo "Saving {$comic->title} chapter {$chapter->attributes->chapter}".PHP_EOL;
        $chapterId = $chapter->id;
        if (MangadexChapter::where('mangadex_id', $chapterId)->exists()) {
            return;
        }
        $fields = [
            'comic_id' => $comic->id,
            'team_id' => Team::first()->id,
            'volume' => $chapter->attributes->volume ?: 1,
            'chapter' => $chapter->attributes->chapter,
            'title' => $chapter->attributes->title,
            'salt' => Str::random(),
            'views' => 1,
            'rating' => 1,
            'language' => $chapter->attributes->translatedLanguage,
            'publish_start' => Carbon::yesterday(),
            'publish_end' => null,
            'published_on' => Carbon::yesterday()
        ] ;
        $fields['slug'] = Chapter::generateSlug($fields);

        $chapter = Chapter::create($fields);
        $path = Chapter::path($comic, $chapter);
        Storage::makeDirectory($path);
        Storage::setVisibility($path, 'public');
        $chapter->save();
        $chapter->refresh();
        try{
            $this->saveChapterImages($comic, $chapter, $chapterId);
            MangadexChapter::create(
                [
                    'mangadex_id' => $chapterId,
                    'title' => $chapter->attributes->title,
                    'chapter_number' => $chapter->attributes->chapter ?: 1,
                    'volume_number' => $chapter->attributes->volume,
                    'language' => $chapter->attributes->translatedLanguage,
                    'is_processed' => true,
                    'chapter_id' => $chapter->id,
                    'mangadex_manga_id' => $manga->id
                ]
            );
            $chapter->save();
        } catch (\Exception $exception){
            echo $exception->getMessage(); die;
            $chapter->delete();
        }
    }
    private function saveChapterImages(Comic $comic, Chapter $chapter, string $chapterId)
    {
        echo "Downloading image data".PHP_EOL;
        $chapter->refresh();
        $response = $this->mangadexApi->getChapterImages($chapterId);
        $path = Chapter::path($comic, $chapter);

        $files = [];
        $pages = [];
        foreach ($response->chapter->data as $filename) {
            $imageResponse = $this->mangadexApi->getChapterImage($response->baseUrl, $response->chapter->hash, $filename);
            $original_file_name = strip_forbidden_chars($filename);
            $files[$original_file_name] = $imageResponse->body();
            $imagedata = getimagesizefromstring($imageResponse->body());
            $size = mb_strlen($imageResponse->body(), '8bit');
            $pages[] =  [
                'chapter_id' => $chapter->id,
                'filename' => $original_file_name,
                'size' => $size,
                'width' => $imagedata[0],
                'height' => $imagedata[1],
                'mime' => $imagedata['mime'],
                'hidden' => false,
                'licensed' => false,
            ];
            sleep(2);
        }
        echo "Image data downloaded".PHP_EOL;
        echo "Saving chapter pages".PHP_EOL;
        foreach ($files as $filename => $content) {
            $this->storeAs($path, $filename, $content);
        }
        $chapter->pages()->createMany($pages);
//        Page::createMany($pages);
    }
    private function storeAs($path, $name, $content)
    {
        Storage::disk('local')->put("{$path}/$name", $content);
    }
    private function getCoverArtId(array $relationships): string
    {
        foreach ($relationships as $relationship) {
            if ($relationship['type'] === 'cover_art') {
                return $relationship['id'];
            }
        }
    }
}
