<?php

namespace App\Repository;

use App\Entity\IMDBSearchResult;
use App\Entity\ApiSearchResultsInterface;
use GuzzleHttp\ClientInterface;

class IMDBRepository
{
    private $client;
    private $apiKey;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function searchMovies(string $title): ApiSearchResultsInterface
    {
        $response = $this->client->request('GET', '', [
            'query' => [
                'apikey' => $this->apiKey,
                'type' => 'movie',
                's' => $title,
            ]
        ]);

        return new IMDBSearchResult($response);
    }
}
