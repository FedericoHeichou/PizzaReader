<?php

namespace App\Console\Commands;

use App\Exceptions\DuplicatedChapter;
use App\Models\Chapter;
use App\Models\ChapterDownload;
use App\Models\Comic;
use App\Models\MangadexManga;
use App\Models\Page;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\VolumeDownload;
use Illuminate\Console\Command;
use App\Mangadex\Api\Manga as MangadexApi;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SyncMangadexLibrary extends Command
{
    private array $mangaToUpdate = [];
    public function __construct(private MangadexApi $mangadexApi)
    {
        return parent:: __construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-mangadex-library';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        Team::create(
//            [
//                'name' => 'test', 'slug' => 'test', 'url' => 'google.com',
//            ]
//        );
//        $user = User::create([
//            'name' => 'Valerie',
//            'email' => 'v@v.c',
//            'password' => Hash::make('12345678'),
//        ]);
//        $user->role()->associate(Role::where('name', 'admin')->first());
//        $user->save();
        $this->saveList();
        $this->prepareChaptersToSave();
        $this->saveChapters();
    }

    private function saveChapterImages(Comic $comic, Chapter $chapter, string $chapterId)
    {
        $chapter->refresh();
        $response = $this->mangadexApi->getChapterImages($chapterId);
        $path = Chapter::path($comic, $chapter);

        foreach ($response->chapter->data as $filename) {
            $imageResponse = $this->mangadexApi->getChapterImage($response->baseUrl, $response->chapter->hash, $filename);
            $imageContent = $imageResponse->body();
            $original_file_name = strip_forbidden_chars($filename);

            $this->storeAs($path, $original_file_name, $imageContent);

            $imagedata = getimagesizefromstring($imageContent);
            $size = mb_strlen($imageContent, '8bit');
            $page = Page::create([
                'chapter_id' => $chapter->id,
                'filename' => $original_file_name,
                'size' => $size,
                'width' => $imagedata[0],
                'height' => $imagedata[1],
                'mime' => $imagedata['mime'],
                'hidden' => false,
                'licensed' => false,
            ]);

//            $page->url = Page::getUrl($comic, $chapter, $page);
        }
    }
    private function saveChapters()
    {
        foreach ($this->mangaToUpdate as $mangaId => $chapters) {
            foreach ($chapters as $chapter) {
                $manga = MangadexManga::where('mangadex_id', $mangaId)->first();
                $this->saveSingleChapter($manga->comic, $chapter);
            }
        }
    }
    private function saveSingleChapter(Comic $comic, object $chapter)
    {
        $chapterId = $chapter->id;
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

        $this->saveChapterImages($comic, $chapter, $chapterId);
        $chapter->save();
    }
    private function prepareChaptersToSave()
    {
        foreach ($this->mangaToUpdate as $mangaId => $value) {
            $limit = 50;
            $offset = 0;
            $processed = 0;
            for(;;) {
                try{
                    $response = $this->mangadexApi->getMangaChapters($mangaId, $limit, $offset);
                } catch (\Exception $exception) {
                    continue;
                }
                $total = $response->total;
                foreach ($response->data as $mangaChapter) {
                    if ($mangaChapter->attributes->translatedLanguage === 'en') {
                        $this->mangaToUpdate[$mangaId][] = $mangaChapter;
                    }
                }
                $offset += $limit;
                $processed += count($response->data);
                if ($processed >= $total) {
                    break;
                }
                sleep(2);
            }
        }
    }
    private function saveList()
    {
        $authData = $this->mangadexApi->auth();
        $limit = 2;
        $offset = 0;
        for (;;) {
            $data = $this->mangadexApi->getList($authData['access_token'], $limit, $offset);
            dd($data);
            if (! empty($data['data'])) {
                $this->processSingleListPage($data);
            }
            $offset += $limit;
            sleep(3);
            break;
        }

    }
    private function processSingleListPage(array $data)
    {
        foreach ($data['data'] as $item) {
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
            $comic = $this->createComic([
                'name' => $title,
                'description' => $description,
                'hidden' => false,
                'author' => '',
                'genres' => implode(',', $genres),
                'order_index' => 0,
                'comic_format_id' => 1,
                'cover_image' => $this->mangadexApi->getMangaCover($item['id'], $this->getCoverArtId($item['relationships'])),
                'thumbnail' => 'thumbnail.png',
            ]);
            $comic->refresh();
            $manga = new MangadexManga();
            $manga->mangadex_id = $item['id'];
            $manga->comic_id = $comic->id;
            $manga->save();
            $this->mangaToUpdate[$item['id']] = [];
        }
    }

    private function createComic(array $fields): Comic
    {
        $fields['salt'] = Str::random();
        $fields['slug'] = Comic::generateSlug($fields);

        $comic = Comic::create($fields);
        $path = Comic::path($comic);
        Storage::makeDirectory($path);
        Storage::setVisibility($path, 'public');
        $this->storeAs($path, $comic->thumbnail, $fields['cover_image']);
        return $comic;
    }

    private function getCoverArtId(array $relationships): string
    {
        foreach ($relationships as $relationship) {
            if ($relationship['type'] === 'cover_art') {
                return $relationship['id'];
            }
        }
    }

    private function storeAs($path, $name, $content)
    {
        Storage::disk('local')->put("{$path}/$name", $content);
    }
}
