<?php

namespace App\Console\Commands;

use App\Anilist\Api;
use App\Models\Anilist\Character;
use App\Models\Anilist\Genre;
use App\Models\Anilist\Manga;
use App\Models\Anilist\MangaGenre;
use App\Models\Anilist\MangaTag;
use App\Models\Anilist\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\String\s;

class SaveAnilistMangas extends Command
{
    private Collection $genres;
    private Collection $tags;
    public function __construct(private Api $api)
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-anilist-mangas';

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
        $this->init();
        $page = 1;
        while(true){
            $mangas = $this->getManga($page);
            if(empty($mangas)){ break; }

            $this->insertPart($mangas);

            $page++;
            sleep(2);
            //            break;
        }
    }

    private function getManga(int $page, int $iteration=1)
    {
        $response = $this->api->fetchAllManga($page);
        $body = json_decode($response->body(), true);
        if(isset($body['data'])){
            return $body['data']['Page']['media'];
        }
        dd($response);
    }

    private function init()
    {
        $this->genres = Genre::all();
        $this->tags = Tag::all();
    }
    private function insertPart(array $mangas)
    {
        $mangaCollection = new Collection();
        foreach($mangas as $manga){
//            dd($manga);
            $mng = new Manga([
                'anilist_id' => $manga['id'],
                'idMal' => $manga['idMal'],
                'name' => $manga['title']['english'] ?: $manga['title']['romaji'],
                'format' => $manga['format'],
                'titles' => json_encode($manga['title']),
                'synonyms' => json_encode($manga['synonyms']),
                'is_adult' => $manga['isAdult'],
                'status' => $manga['status'],
                'chapters' => $manga['chapters'],
                'volumes' => $manga['volumes'],
                'description' => strip_tags($manga['description']),
                'source' => $manga['source'],
                'score' => $manga['averageScore'],
                'cover_image' => $manga['coverImage']['extraLarge'] ?: $manga['coverImage']['large'],
//                    'started_at' => \DateTime::createFromFormat('Y', $manga['startDate']['year']),
            ]);
            $mangaCollection->push($mng);
        }
        Manga::upsert($mangaCollection->toArray(), ['anilist_id']);
        $this->processRelations($mangas);

    }
    private function processRelations(array $mangas)
    {
        $mangaCollection = new Collection(
            Manga::all(['id', 'anilist_id'])->whereIn('anilist_id', array_column($mangas, 'id'))
        );
        $mangaGenreRelations = [];
        $mangaTagRelations = [];
//        Manga::all()->firstWhere()
        foreach ($mangas as $manga) {
            /** @var Manga $mangaObject */
            $mangaObject = $mangaCollection->firstWhere('anilist_id', $manga['id']);
            if (!$mangaObject) {
//                dd($manga);
            }
            $mangaId = $mangaObject->id;
            foreach ($manga['genres'] as $genre) {
                $mangaGenreRelations[] = [
                    'manga_id' => $mangaId,
                    'genre_id' => $this->genres->firstWhere('name', $genre)->id,
                ];
            }
            foreach ($manga['tags'] as $tag) {
                $tagObject = $mangaCollection->firstWhere('anilist_id', $tag['id']);
                if (!$tagObject) {
                    continue;
                }
                $mangaTagRelations[] = [
                    'manga_id' => $mangaId,
                    'tag_id' => $this->tags->firstWhere('name', $tag['name'])->id,
                ];
            }
            //todo process characters later
            $characters = [];
            foreach ($manga['characters']['edges'] as $character) {
                $character = new Character(
                    [
                        'anilist_id' => $character['node']['id'],
                        'role' => $character['role'],
                        'name' => $character['node']['name']['name'],
                        'image' => $character['node']['image']['large'],
                    ]
                );
            }
        }
        MangaGenre::upsert($mangaGenreRelations, ['manga_id', 'genre_id']);
        MangaTag::upsert($mangaTagRelations, ['manga_id', 'genre_id']);
    }
}
