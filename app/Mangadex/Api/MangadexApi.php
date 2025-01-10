<?php

namespace App\Mangadex\Api;

use GuzzleHttp\Client;

class MangadexApi
{
    protected $client;

    // Main Constants
    private const HTTP_PROTOCOLS = 'https';
    private const API_DOMAIN = 'api.mangadex.org';

    public function __construct ()
    {
        $this->buildURL();
    }

    /**
     * Building the API URL
     *
     * @param string $endpoint
     */
    protected function buildURL () : void
    {
        $this->client = new Client([
            'base_uri' => self::HTTP_PROTOCOLS . '://' . self::API_DOMAIN
        ]);
    }

    /**
     * Building the Query Params for request
     *
     * @param array $queryParams
     *
     * @return array
     */
    protected function buildQueryParams (array $queryParams = []) : array
    {
        $query = [];

        if (!empty($queryParams))
        {
            foreach ($queryParams as $key => $param)
            {
                $query[$key] = $param;
            }
        }

        return $query;
    }

    /**
     * Handle request's response
     *
     * @param Psr\Http\Message\ResponseInterface $response
     *
     * @return object
     */
    protected function handleResponse ($response) : object
    {
        sleep(2);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody());
        } else {
            return json_encode([
                'statusCode' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
            ]);
        }
    }
}
