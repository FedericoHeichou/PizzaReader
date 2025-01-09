<?php

namespace App\Mangadex\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class Manga extends MangadexApi
{
    private const   MANGA_ENDPOINT = '/manga';
    private const RELATION_ENDPOINT = '/relation';
    private const RANDOM_ENDPOINT = '/random';
    private const TAG_ENDPOINT = '/tag';

//    private const

    /**
     * Get Manga Lists
     *
     * @param array $queryParams
     *
     * @return object
     *
     */
    public function getMangas (array $queryParams) : object
    {
        $query = $this->buildQueryParams($queryParams);

        $response = $this->client->request('GET', self::MANGA_ENDPOINT, [
            'query' => $query,
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Get Manga by MangaDex ID
     *
     * @param string $id
     * @param bool $withRelationship
     * @param array $queryParams
     *
     * @return object
     */

    public function getMangaById (string $id, bool $withRelationship = false, array $queryParams = []) : object
    {
        $pathParam = $withRelationship ? $id . self::RELATION_ENDPOINT : $id;

        $query = !empty($queryParams) ? $this->buildQueryParams($queryParams) : [];

        $response = $this->client->request('GET', self::MANGA_ENDPOINT . '/' . $pathParam, [
            'query' => $query,
        ]);

        return $this->handleResponse($response);
    }
    public function getMangaChapters(string $mangaId, int $limit = 10, int $offset = 0) : object
    {
        return $this->handleResponse(
            $this->client->request('GET', self::MANGA_ENDPOINT . '/' . $mangaId . '/feed', ['query' => ['limit' => $limit, 'offset' => $offset]])
        );
    }
    public function getChapterImages(string $chapterId) : object
    {
        return $this->handleResponse(
            $this->client->request('GET', '/at-home/server/' . $chapterId)
        );
    }
    public function getChapterImage(string $baseUrl, string $chapterHash, string $imageId)
    {
        return Http::get("{$baseUrl}/data/{$chapterHash}/{$imageId}");
    }

    /**
     * Get a random Manga
     *
     * @param array $queryParams
     *
     * @return object
     */

    public function getRandomManga (array $queryParams = []) : object
    {
        $query = $this->buildQueryParams($queryParams);

        $response = $this->client->request('GET', self::MANGA_ENDPOINT . self::RANDOM_ENDPOINT, [
            'query' => $query,
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Get all manga tags
     *
     * @return object
     */
    public function getMangaTags () : object
    {
        $response = $this->client->request('GET', self::MANGA_ENDPOINT . self::TAG_ENDPOINT);

        return $this->handleResponse($response);
    }

    /**
     * TODO: Get manga volumes and chapters
     *
     * @param array $queryParams
     *
     * @return object
     */
    public function getMangaAggregate(Type $var = null)
    {
        //
    }
    public function auth()
    {
        $response = Http::asForm()->post('https://auth.mangadex.org/realms/mangadex/protocol/openid-connect/token', [
            'grant_type'=>'password',
            'username'=>'valerie001',
            'password'=>'*x2@szgPL!DuLe3',
            'client_id' => 'personal-client-05d928e0-e59b-41ad-a02c-4d4685927ec7-8bda937a',
            'client_secret' => '7tNEj2ht0WUiyMmpGoDSvTib8HL9N8LI'
        ]);
        return json_decode($response->getBody()->getContents(), true);
//        dd($response->getBody()->getContents());
    }
    public function getList(string $token, int $limit = 10, int $offset = 0)
    {
        $url = 'https://api.mangadex.org/user/follows/manga';
        $data = Http::withHeaders(['Authorization' => 'Bearer '.$token, 'accept' => 'application/json'])
            ->get($url, ['limit' => $limit, 'offset' => $offset]);
        return json_decode($data->getBody()->getContents(), true);
    }

    public function getMangaCover(string $mangaId, string $coverId)
    {
        $filename = Http::get("https://api.mangadex.org/cover/{$coverId}")->json()['data']['attributes']['fileName'];
        return Http::get("https://uploads.mangadex.org/covers/$mangaId/{$filename}")->body();
    }
}
