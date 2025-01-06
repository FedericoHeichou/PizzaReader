<?php

namespace App\Anilist;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Api
{
    public function fetchManga(int $id)
    {
        $query = $query = '
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
            "id" => $id
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => $variables,
        ]);
        dd($response->body());
    }
    public function fetchAllManga(int $page = 1, int $perPage = 50): Response
    {
        $query = $query = '
            query ($id: Int, $page: Int, $perPage: Int, $search: String) {
  Page (page: $page, perPage: $perPage) {
    pageInfo {
      total
      currentPage
      lastPage
      hasNextPage
      perPage
    }
    media (id: $id, search: $search, type:MANGA) {
      id
      idMal
      title {
        romaji
        english
      }
      synonyms
      type
      isAdult
    type format status
    chapters
    volumes
    description
    staffPreview:staff(perPage:8,sort:[RELEVANCE,ID]){edges{id role node{id name{userPreferred}language:languageV2 image{large}}}}
    source
    averageScore
    meanScore
    popularity
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
}

        ';
// Define our query variables and values that will be used in the query request
        $variables = [
            "page" => $page,
            "perPage" => $perPage,
        ];

        return Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => $variables,
        ]);
    }

    public function getTags(): array
    {
        $query = '
            query {
                MediaTagCollection {
                    id,
                    name,
                    description
                }
            }
        ';
        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
        ]);
        return json_decode($response->body(), true)['data']['MediaTagCollection'];
    }
    public function getGenres(): array
    {
        $query = '
        query {
 	        GenreCollection
        }';
        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
        ]);
        return json_decode($response->body(), true)['data']['GenreCollection'];
    }
}
