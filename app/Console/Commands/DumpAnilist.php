<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Anilist\Api;

class DumpAnilist extends Command
{
    public function __construct(private Api $api)
    {
        parent::__construct();
    }


    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'app:dump-anilist';

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
        $this->getGenres();
        $this->test();
//        dd('fuck');
    }

    private function test()
    {
        $query = '
query ($id: Int) {
  Media (id: $id, type: MANGA) {
    id
    title {
      userPreferred
      romaji
      english
      native
    }
    isAdult
    season seasonYear type format status
    chapters
    volumes
    episodes
    duration
    description
    staffPreview:staff(perPage:8,sort:[RELEVANCE,ID]){edges{id role node{id name{userPreferred}language:languageV2 image{large}}}}
    source
    averageScore
    meanScore
    synonyms
    genres
    tags {id name description rank isMediaSpoiler isGeneralSpoiler userId}
    coverImage {
        extraLarge
        large
    }
    bannerImage
    startDate {
        year
        month
        day
    }
    endDate {
        year
        month
        day
    }
    relations{
        edges{
        id
        relationType(version:2)
        node{
            id
            title{userPreferred}
            format
            type
            status(version:2)
            bannerImage
            coverImage{large}
            }
        }
    }
    characters(perPage:50){edges{id role name node{id name{name:userPreferred}image{large}}}}
    externalLinks{id site url type language color icon notes isDisabled}
    rankings{id rank type format year season allTime context}
  }
}
';

// Define our query variables and values that will be used in the query request
        $variables = [
            "id" => 84817
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => $variables,
        ]);
        dd(json_decode($response->body()));
    }


}
